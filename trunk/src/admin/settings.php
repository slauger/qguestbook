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
* @subpackage admin
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id: settings.php 24 2008-03-27 18:33:54Z kwhark $
* @link       http://www.simlau.net/
*/

//
// Konstanten
//
define('GUESTBOOK', true);
define('ADMIN_PAGE', true);
define('REQUIRED_AUTH_LEVEL', 3);

//
// Dateien includieren
//
$root_dir = '../';
include_once $root_dir . 'includes/common.php';
include_once $root_dir . 'includes/header.php';

if (isset($_POST['submit']))
{
	$ignore = array('submit');
	foreach ($_POST as $key => $value)
	{
		if (!in_array($key, $ignore) AND $config_table[$key] != $value)
		{
			if (!update_config_table($key, $value))
			{
				$failed = true;
				$failed_rows[$key] = $value;
			}
		}
	}

	if (isset($failed) && $failed == true)
	{
		message_die($lang['settings_edited'], sprintf($lang['settings_failed'], "<a href=\"" . PAGE_ADMIN_SETTINGS . "\">", "</a>", "<a href=\"" . PAGE_ADMIN_INDEX . "\">", "</a>"));
	}
	message_die($lang['settings_edited'], sprintf($lang['settings_success'], "<a href=\"" . PAGE_ADMIN_SETTINGS . "\">", "</a>", "<a href=\"" . PAGE_ADMIN_INDEX . "\">", "</a>"));
}

$template->set_filenames(array(
	'index' => 'settings_body.html',
));

/**
  * Checkboxen
  */
$active_yes = ($config_table['active']) ? "checked=\"checked\"" : "";
$active_no = (!$config_table['active']) ? "checked=\"checked\"" : "";

$show_warnings_yes = ($config_table['show_warnings']) ? "checked=\"checked\"" : "";
$show_warnings_no = (!$config_table['show_warnings']) ? "checked=\"checked\"" : "";

$admin_link_yes = ($config_table['admin_link']) ? "checked=\"checked\"" : "";
$admin_link_no = (!$config_table['admin_link']) ? "checked=\"checked\"" : "";

$moderated_yes = ($config_table['moderated']) ?  "checked=\"checked\"" : "";
$moderated_no = (!$config_table['moderated']) ?  "checked=\"checked\"" : "";

$email_mail = ($config_table['email_mode'] == 1) ?  "checked=\"checked\"" : "";
$email_sendmail = ($config_table['email_mode'] == 2) ?  "checked=\"checked\"" : "";
$email_smtp = ($config_table['email_mode'] == 3) ?  "checked=\"checked\"" : "";

$success_email_yes = ($config_table['success_email']) ?  "checked=\"checked\"" : "";
$success_email_no = (!$config_table['success_email']) ?  "checked=\"checked\"" : "";

$success_email_admin_yes = ($config_table['success_email_admin']) ?  "checked=\"checked\"" : "";
$success_email_admin_no = (!$config_table['success_email_admin']) ?  "checked=\"checked\"" : "";

$email_html_yes = ($config_table['email_html']) ?  "checked=\"checked\"" : "";
$email_html_no = (!$config_table['email_html']) ?  "checked=\"checked\"" : "";

$enable_icq_yes = ($config_table['enable_icq']) ?  "checked=\"checked\"" : "";
$enable_icq_no = (!$config_table['enable_icq']) ?  "checked=\"checked\"" : "";

$enable_www_yes = ($config_table['enable_www']) ?  "checked=\"checked\"" : "";
$enable_www_no = (!$config_table['enable_www']) ?  "checked=\"checked\"" : "";

$success_email_admin_all_yes = ($config_table['success_email_admin_all']) ?  "checked=\"checked\"" : "";
$success_email_admin_all_no = (!$config_table['success_email_admin_all']) ?  "checked=\"checked\"" : "";

$enable_rss_yes = ($config_table['newsfeed']) ?  "checked=\"checked\"" : "";
$enable_rss_no = (!$config_table['newsfeed']) ?  "checked=\"checked\"" : "";

$debug_info_yes = ($config_table['debug_info']) ?  "checked=\"checked\"" : "";
$debug_info_no = (!$config_table['debug_info']) ?  "checked=\"checked\"" : "";

$postorder_asc = ($config->get('postorder') == 'asc') ?  "checked=\"checked\"" : "";
$postorder_desc = ($config->get('postorder') == 'desc') ?  "checked=\"checked\"" : "";

/**
  * Sprachpakete auslesen
  */
$language_packs = read_directory($root_dir . 'includes/language/');

foreach ($language_packs['dir'] as $value)
{
	if (substr($value, 0, 1) != '.')
	{
		$template->assign_block_vars('language', array(
			'LANGUAGE' => $value,
			'LANGUAGE_DESC' => parse_language($value),
			'SELECTED' => ($config->get('language') == $value) ? " selected=\"selected\"" : "",
		));
	}
}

/**
  * Unterstützte Zeichensätze von qGuestbook
  * Auf jene hier begrenzt, da decode_html() nur diese verwenden kann...
  */
$charset = array('ISO-8859-1', 'ISO-8859-15', 'UTF-8', 'cp866', 'cp1251', 'cp1252', 'KOI8-R', 'BIG5', 'GB2312', 'BIG5-HKSCS', 'Shift_JIS', 'EUC-JP');

foreach ($charset as $value)
{
	$template->assign_block_vars('charset', array(
		'CHARSET' => $value,
		'SELECTED' => ($config_table['charset'] == $value) ? " selected=\"selected\"" : "",
	));
}

/**
  * Template Vars
  */
$template->assign_vars(array(
	'DISABLE_MSG' => $config_table['disable_msg'],
	'DESCRIPTION' => $config_table['description'],
	'DEFAULT_DATEFORMAT' => $config_table['default_dateformat'],
	'DEFAULT_LIMIT' => $config_table['posts_site'],
	'MAX_LENGTH' => $config_table['max_lenght'],
	'SUCCESS_EMAIL_TEXT' => $config_table['success_email_text'],
	'EMAIL_ADMIN' => $config_table['email_admin'],
	'SMTP_SERVER' => $config_table['smtp_server'],
	'SMTP_PORT' => $config_table['smtp_port'],
	'SMTP_USER' => $config_table['smtp_user'],
	'SMTP_PASS' => $config_table['smtp_pass'],
	'SCRIPT_PATH' => $config_table['script_path'],
	'SUCCESS_EMAIL_ADMIN_TEXT' => $config_table['success_email_admin_text'],
	'SENDMAIL' => $config_table['sendmail'],
	'SMTP_HELO' => $config_table['smtp_helo'],
	'RSS_LIMIT' => $config_table['rss_limit'],
	'LIMIT_IMAGES' => $config_table['limit_images'],

	'POSTORDER_ASC' => $postorder_asc,
	'POSTORDER_DESC' => $postorder_desc,
	'DEBUG_INFO_YES' => $debug_info_yes,
	'DEBUG_INFO_NO' => $debug_info_no,
	'RSS_YES' => $enable_rss_yes,
	'RSS_NO' => $enable_rss_no,
	'SUCCESS_EMAIL_ADMIN_ALL_YES' => $success_email_admin_all_yes,
	'SUCCESS_EMAIL_ADMIN_ALL_NO' => $success_email_admin_all_no,
	'ENABLE_ICQ_YES' => $enable_icq_yes,
	'ENABLE_ICQ_NO' => $enable_icq_no,
	'ENABLE_WWW_YES' => $enable_www_yes,
	'ENABLE_WWW_NO' => $enable_www_no,
	'EMAIL_HTML_YES' => $email_html_yes,
	'EMAIL_HTML_NO' => $email_html_no,
	'ACTIVE_YES' => $active_yes,
	'ACTIVE_NO' => $active_no,
	'SHOW_WARNINGS_YES' => $show_warnings_yes,
	'SHOW_WARNINGS_NO' => $show_warnings_no,
	'ADMIN_LINK_YES' => $admin_link_yes,
	'ADMIN_LINK_NO' => $admin_link_no,
	'MODERATED_YES' => $moderated_yes,
	'MODERATED_NO' => $moderated_no,
	'EMAIL_MAIL' => $email_mail,
	'EMAIL_SENDMAIL' => $email_sendmail,
	'EMAIL_SMTP' => $email_smtp,
	'SUCCESS_EMAIL_YES' => $success_email_yes,
	'SUCCESS_EMAIL_NO' => $success_email_no,
	'SUCCESS_EMAIL_ADMIN_YES' => $success_email_admin_yes,
	'SUCCESS_EMAIL_ADMIN_NO' => $success_email_admin_no,
));

/**
 * Style Einstellungen
 */
$sql = 'SELECT styles_id, styles_name, styles_css
	FROM . ' . STYLES_TABLE;
$result = $db->sql_query($sql);

while ( $row = $db->sql_fetchrow($result) )
{
	$selected = ( $row['styles_id'] == $config_table['style'] ) ? " selected=\"selected\"" : "";
	$template->assign_block_vars('styles', array(
		'ID' => $row['styles_id'],
		'NAME' => $row['styles_name'],
		'CSS' => $row['styles_css'],
		'SELECTED' => $selected,
	));
}

/**
 * Template ausgeben
 */
$template->pparse('index');

/**
 * Footer ausgeben
 */
include_once $root_dir . 'includes/footer.php';

?>