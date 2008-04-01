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
* @version    CVS: $Id: posting.php 15 2008-03-27 13:35:32Z kwhark $
* @link       http://www.simlau.net/
*/

define('GUESTBOOK', true);
$root_dir = "./";
include_once $root_dir . 'includes/common.php';
page_header('Neuen Eintrag schreiben');

$valdiate_error = '';
$mode = (!isset($_GET['mode']) || empty($_GET['mode'])) ? '' : $_GET['mode'];
$mode = (isset($_POST['preview'])) ? 'preview' : $mode;
$reply_id = (isset($_GET['id'])) ? $_GET['id'] : '';

switch ($mode) {
	case 'insert':		
		// Pflichtfeld "Username"
		if (!$globals->post('name') || !$user = valdiate_username($globals->post('name'))) {
			$valdiate_error = 'name';
			break;
		}
		
		// Pflichtfeld "Email"
		if (!$globals->post('email') || !$email = valdiate_email($globals->post('email'))) {
			$valdiate_error ='email';
			break;
		}
		
		// Pflichtfeld "Textarea"
		if (!$globals->post('textarea')) {
			$valdiate_error = 'textarea';
			break;
		}
		
		// Text based Captcha, added in 0.2.4
		//
		// Im Moment nur eine "Quick and Dirty"-Lösung
		// Das ganze wird noch auf die Datenbank ausgelagert, um den Bots
		// die Arbeit noch etwas zu erschweren...
		//
		// In späteren Versionen wird es auch möglich sein, zwichen Textbasierten
		// und einem grafischen Captcha zu wechseln.
		if (!$globals->post('captcha_sum') || !$globals->post('captcha_checksum')) {
			$valdiate_error = 'captcha';
			echo "passed";
			break;
		} else {
			if ($globals->post('captcha_checksum') != md5($globals->post('captcha_sum'))) {
				$valdiate_error = 'captcha';
				break;
			}
		}
		
		// Valdiate Fields
		$hide_email = ($globals->post('hide_email')) ? 1 : 0;
		$text = trim($globals->post('textarea'));
		$user = trim($globals->post('name'));
		$icq = ($globals->post('icq')) ? trim($_POST['icq']) : '';
		$www = (isset($_POST['www'])) ? trim($_POST['www']) : '';

		// Website URL valdieren
		if (!empty($icq)) {
			// Darf er die überhaupt angeben?
			if ($config_table['enable_icq'] == 0) {
				$icq = '';
			}
			// Ja, aber ist sie valid?
			elseif (!valdiate_icq($icq)) 	{
				$valdiate_error = 'icq';
				break;
			}
		}
		
		// ICQ UIN valdieren
		if (!empty($www)) {
			// Darf er die überhaupt angeben?
			if ($config_table['enable_www'] == 0) {
				$www = '';
			}
			// Ja, aber ist sie valid?
			elseif (!valdiate_website($www)) {
				$valdiate_error = 'www';
				break;
			}
		}
		
		// Handelt es sich hier etwa um Flooding?
		$sql = 'SELECT posts_id
			FROM ' . POSTS_TABLE . '
				WHERE posts_ip = ' . $db->sql_escape($user_ip) . '
				AND (' . time() . ' - posts_date) < ' . $config->get('flood_timeout');
		$result = $db->sql_query($sql);
		
		if ($db->sql_numrows($result)) {
			// Ja, also Fehlermeldung anzeigen
			$valdiate_error = 'flood';
			break;
		}
		
		// Beitrag in die Moderations-Warteschlange stellen?
		$active = (isset($config_table['moderated']) && $config_table['moderated'] == 1) ? POST_WAIT_LIST : POST_ACTIVE;

		$sql = 'INSERT INTO ' . POSTS_TABLE . '
			(posts_id, posts_name, posts_email, posts_ip, posts_www, posts_icq, posts_text, posts_date, posts_active, posts_hide_email) 
			VALUES (' . $db->sql_escape('') . ', ' . $db->sql_escape($user) . ',' . $db->sql_escape($email) . ', ' . $db->sql_escape($user_ip) . ', ' . $db->sql_escape($www) . ', ' . $db->sql_escape($icq) . ', ' . $db->sql_escape($text) . ', ' . $db->sql_escape(time()) . ', ' . $db->sql_escape($active) . ', ' . $db->sql_escape($hide_email) . ')';
		$db->sql_query($sql);
		
		// Bestätigungs E-Mail an den User
		if ($config->get('success_email')) {
			generate_mail($lang['email_post_user'], array($email), sprintf($config_table['success_email_text'], $user));
		}
		
		// Benachrichtungs E-Mails an die Mods
		if ($config->get('success_email_admin')) {
			// An die komplette Manschaft ;)
			if ($config->get('success_email_admin')) {
				$sql = 'SELECT user_email
					FROM ' . USERS_TABLE;
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result)) {
					$email_adresses[] = $row['user_email'];
				}
			} else {
				// Nur an den Administrator
				// Bzw. die angegebene E-Mail in der Konfiguration
				$email_adresses[] = $config->get('email_admin');
			}

			// HTMLMimmeMail5 möchte ein Array
			$email_adresses = (!$config->get('success_email_admin_all')) ? array($config->get('email_admin')) : $email_adresses;
			
			// E-Mail verschicken!
			// Fixed in 0.2.4 (forgot $email_adresses)
			generate_mail($lang['email_post_admin'], $email_adresses, sprintf($config_table['success_email_admin_text'], $user, $text, real_path()));
		}
		
		// Done! Bestätigung ausgeben.
		message_die($lang['posting_success'], sprintf($lang['posting_success_text'], '', "<a href=\" " . PAGE_INDEX . "\">", "</a>", "<a href=\"" . PAGE_POSTING . "\">", "</a>"));
		
	break;
	case 'preview':
		// Vorschau ist noch nicht möglich.
		message_die($lang['guestbook_error'], 'Die Vorschau Funktion ist leider in diesem Release noch nicht verfügbar, wird aber so bald wie möglich nachgereicht werden. :)<br /><br />Versuchs doch einfach später nochmal... :P');
	break;
	case 'reply':
		// Zitat generieren.
		// Die Funktion generate_quote() nimmt uns hier die Arbeit ab.
		$reply_text = (!empty($reply_id)) ? generate_quote($reply_id) : '';
	break;
}

$template->set_filenames(array(
	'index'=> 'posting_body.html'
));

// User darf ICQ UIN angeben
if ($config->get('enable_icq') == 1) {
	$template->assign_block_vars('icq_enabled', array());
}

// User darf Homepage angeben
if ($config->get('enable_www')) {
	$template->assign_block_vars('www_enabled', array());
}

// Smilie und BBCode Status anzeigen
$bbcodes_status = (!$config->get('bbcode')) ? $lang['inactive'] : $lang['active'];
$smilies_status = (!$config->get('smilies')) ? $lang['smilies'] : $lang['active'];

// Fehler beim valdieren der Userdaten?
if (isset($valdiate_error) && !empty($valdiate_error)) {
	$template->assign_block_vars('valdiate_error', array());
	$error_message = valdiate_error($valdiate_error);
}

// Initialisierung
$textarea = "";

// Textfeld des Formulars füllen
// Wenn Zitat gewählt, geht dieses vor
if (isset($reply_text) && !empty($reply_text)) {
	$textarea = $encode->encode_html($reply_text);
} elseif ($globals->post('textarea')) {
	$textarea = $encode->encode_html($_POST['textarea']);
}

// Captcha
$captcha_a = round(rand(1, 100) / 10);
$captcha_b = round(rand(1, 100) / 10);
$captcha_checksum = md5($captcha_a + $captcha_b);

// Template Vars
$template->assign_vars(array(
	'BBCODES_STATUS' => $bbcodes_status,
	'SMILIES_STATUS' => $smilies_status,
	'TEXTAREA' => $textarea,
	'HTML_STATUS' => $lang['inactive'],
	'NAME' => (isset($_POST['name']) && !empty($_POST['name'])) ? $encode->encode_html($_POST['name']) : "",
	'EMAIL' => (isset($_POST['email']) && !empty($_POST['email'])) ? $encode->encode_html($_POST['email']) : "",
	'ICQ' => (isset($_POST['icq']) && !empty($_POST['icq'])) ? $encode->encode_html($_POST['icq']) : "",
	'WWW' => (isset($_POST['www']) && !empty($_POST['www'])) ? $encode->encode_html($_POST['www']) : "",
	'HIDE_EMAIL' => (isset($_POST['hide_email']) && !empty($_POST['hide_email'])) ? "checked=\"checked\"" : "",
	'ERROR_TITLE' => $lang['guestbook_error'],
	'ERROR_MESSAGE' => (isset($error_message) && !empty($error_message)) ? $error_message : "",
	
	'CAPTCHA_A' => $captcha_a,
	'CAPTCHA_B' => $captcha_b,
	'CAPTCHA_CHECKSUM' => $captcha_checksum,
));

$template->pparse('index');

page_footer();

?>