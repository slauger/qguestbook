<?php
/**
* qGuestbook
*
* An advanced Guestbook written in PHP5.
*
* PHP version 5
*
* LICENSE:
* Copyright (C) 2007-2008 Simon Lauger
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* @category   Guestbook
* @package    qGuestbook
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    0.2.4
* @link       http://www.simlau.net/
*/

if (!defined('GUESTBOOK')) {
	die('Hacking attempt');
	exit;
}

// config
include_once $root_dir . 'includes/config/config.php';

// constants
include_once $root_dir . 'includes/constants/constants.php';

// functions
include_once $root_dir . 'includes/functions/functions.php';
include_once $root_dir . 'includes/functions/functions_valdiate.php';
include_once $root_dir . 'includes/functions/functions_display.php';
include_once $root_dir . 'includes/functions/functions_sessions.php';

// bbcode
require_once $root_dir . 'includes/bbcode/stringparser.class.php';
require_once $root_dir . 'includes/bbcode/stringparser_bbcode.class.php';

// email
include_once $root_dir . 'includes/functions/functions_email.php';
require_once $root_dir . 'includes/email/mimePart.php';
require_once $root_dir . 'includes/email/htmlMimeMail5.php';

// classes
require_once $root_dir . 'includes/database/db_select.php';
require_once $root_dir . 'includes/template/template.php';
require_once $root_dir . 'includes/language/language.php';
require_once $root_dir . 'includes/encode/encode.php';

// Befinden wir uns unter PHP4?
// Wenn ja brauchen wir noch einige Sachen...
if (substr(phpversion(), 0, 1) == 4)
{
	include_once $root_dir . 'includes/functions/functions_php4.php';
}

// Zerlegen der IP Adresse
$client_ip = ( !empty($HTTP_SERVER_VARS['REMOTE_ADDR']) ) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ( ( !empty($HTTP_ENV_VARS['REMOTE_ADDR']) ) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : getenv('REMOTE_ADDR') );
$user_ip = encode_ip($client_ip);

// Zur Datenbank verbinden
$db = new $database_class($dbhost, $dbuser, $dbpasswd, $dbname);

// Konfiguration auslesen
$sql = 'SELECT config_name, config_value
	FROM ' . CONFIG_TABLE;

if (!$result = $db->sql_query($sql)) {
	// Ohne Config macht es keinen Sinn weiter zu machen...
	die('<b>Fatal Error:</b> cant\'t query config table!');
	exit;
}

// Schreiben der Konfiguration
while ($row = $db->sql_fetchrow($result)) {
	$config_table = (!is_array($config_table)) ? array() : $config_table;
	$config_table[$row['config_name']] = $row['config_value'];
}

// Nun kann auch die BBCode Klasse gestartet werden
// Diese braucht ein paar $config_table Variablen...
include_once $root_dir . 'includes/bbcode/bbcode.php';

// PHP Debug Informationen anzeigen?
if ($config_table['show_warnings'] == 1)
{
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}

// Zur Sicherheit nachsehen ob der Zeichensatz gesetzt wurde
if (!isset($config_table['charset']) || empty($config_table['charset'])) {
	$config_table['charset'] = 'utf-8';
}

// Ein paar Sachen für den HTTP Header
header("Content-Type: text/html; charset=" . $config_table['charset']);
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: text/html");

// Kodierungsklasse starten
$encode = new qEncode('utf-8', $config_table['charset']);

// Den Sprachparser starten
$language = new qLanguage();

//
// Das neue Templatesystem.
//
// Hier fehlt aber leider noch eine Kleinigkeit...
// Wenn wir uns im Moderatorenbereich befinden, dann befinden sich alle Dateien
// direkt im style Ordner, also z. B. admin/style/theme/style.css...
// Es existieren hier _keine_ extra Unterordner für extra Styles, der Moderatorenbereich
// hat nur _einen_ festgelegten Style.
//
$config_table['default_style'] = 1;

if (defined('ADMIN_PAGE') || defined('LOGIN_PAGE'))
{
	// Standartpfad ändern
	$styles_dir     = $root_dir . 'admin/style';
	$style_theme    = $styles_dir . '/theme/style.css';
	$style_imageset = $styles_dir . '/imageset/';
	$style_template = $styles_dir . '/template';

	// Template Grafiken includieren
	include_once $style_imageset . '/imageset.php';
} else {
	$sql = 'SELECT styles_name, styles_template, styles_theme, styles_imageset
		FROM ' . STYLES_TABLE . '
		WHERE styles_id = ' . $db->sql_escape($config_table['default_style']);

	if (!$result = $db->sql_query($sql)) {
		die('<b>Fatal Error:</b> can\'t query styles data');
	}

	$styles_info = $db->sql_fetchrow($result);
	$styles_dir = $root_dir.'styles/'.$styles_info['styles_template'];
	$style_template = $styles_dir.'/template';
	$style_imageset = $styles_dir.'/imageset/'.$styles_info['styles_imageset'] . '/';
	$style_theme = $styles_dir.'/theme/'.$styles_info['styles_theme'].'/style.css';

	// Imageset includieren
	include_once $style_imageset . '/imageset.php';
}

// Template Klasse starten
$template = new qTemplate($style_template);

// Die wichtigsten Template Variablen
$template->assign_vars(array(
	'STYLE_THEME' => $style_theme,
));

// Die Grafiken parsen
parse_imageset();

// Für die Administration brauchen wir nun noch gewisse Dinge...
if (defined('ADMIN_PAGE') || defined('LOGIN_PAGE'))
{
	include_once $root_dir . 'includes/sessions/sessions.php';
	include_once $root_dir . 'includes/functions/functions_acp.php';
}

// Wurde das Gästebuch deaktivert?
if ($config_table['active'] == 0) {
	// Deaktivert wird nur, wenn wir uns nicht im Moderatoren-Bereich befinden
	if (!defined('ADMIN_PAGE') && !defined('LOGIN_PAGE')) {
		message_die($lang['guestbook_error'], $encode->encode_string($config_table['disable_msg']));
	}
}

// Ein paar tolle Session Sachen
if (user_logged_in()) {
	$template->assign_block_vars('switch_logged_in', array());
} else {
	$template->assign_block_vars('switch_logged_out', array());
}

?>
