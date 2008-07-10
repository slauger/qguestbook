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
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

if (!defined('GUESTBOOK')) {
	die('Hacking attempt');
	exit;
}

// Befinden wir uns unter PHP4?
// Seit Version 0.2.4 wurde die Arbeit an einem Backport
// eingestellt. Falls doch noch interesse besteht und 
// jemand Lust hat, alles zu portenn, der kann sich bei mir melden.
if (substr(phpversion(), 0, 1) == 4) {
	// Backports für die Klassen existieren nocht nicht...
	trigger_error('PHP 5.0 or higher required', E_USER_ERROR);
	//include_once $root_dir . 'includes/functions/functions_php4.php';
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

// email
include_once $root_dir . 'includes/functions/functions_email.php';
require_once $root_dir . 'includes/email/mimePart.php';
require_once $root_dir . 'includes/email/htmlMimeMail5.php';

// classes
require_once $root_dir . 'includes/classes/config.php';
require_once $root_dir . 'includes/classes/encode.php';
require_once $root_dir . 'includes/classes/styles.php';
require_once $root_dir . 'includes/classes/globals.php';
require_once $root_dir . 'includes/classes/language.php';
require_once $root_dir . 'includes/classes/db_select.php';
require_once $root_dir . 'includes/classes/template.php';
require_once $root_dir . 'includes/classes/module.php';

// Schon installiert?
if (!defined('INSTALLED')) {
	header("Location: $root_dir/install/index.php");
	exit;
}

$globals = new qGlobals();

// Zerlegen der IP Adresse
$client_ip = ( !empty($HTTP_SERVER_VARS['REMOTE_ADDR']) ) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ( ( !empty($HTTP_ENV_VARS['REMOTE_ADDR']) ) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : getenv('REMOTE_ADDR') );
$user_ip = encode_ip($client_ip);

// Zur Datenbank verbinden
$db = new $database_class($dbhost, $dbuser, $dbpasswd, $dbname);

// Konfiguration auslesen
// Export als $config_table
// Wieder mal die Rückswärtskompatibilität
$config = new qConfig('config_table');

// PHP Debug Informationen anzeigen?
if ($config->get('show_warnings')) {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}

// Ein paar Sachen für den HTTP Header
header("Content-Type: text/html; charset=" . $config->get('charset'));
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$language = new qLanguage();
$encode = new qEncode($lang['CHARSET'], $config->get('charset'));
$language->export_language();

$styles = new qStyles();
$template = new qTemplate($styles->get('template_path'));
$styles->parse();

// Für die Administration brauchen wir nun noch gewisse Dinge...
if (defined('ADMIN_PAGE') || defined('LOGIN_PAGE')) {
	include_once $root_dir . 'includes/sessions/sessions.php';
	include_once $root_dir . 'includes/functions/functions_acp.php';
}

// Wurde das Gästebuch deaktivert?
if (!$config->get('active')) {
	if (!defined('ADMIN_PAGE') && !defined('LOGIN_PAGE')) {
		message_die($lang['ERROR_MAIN'], $encode->encode_html($config->get('disable_msg')));
	}
}

// Ein paar tolle Session Sachen
// Besonders für die Designer wichtig
if (user_logged_in()) {
	$template->assign_block_vars('switch_logged_in', array());
} else {
	$template->assign_block_vars('switch_logged_out', array());
}

$module = new qModule();

?>
