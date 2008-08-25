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

define('GUESTBOOK', true);
$root_dir = "./";
include_once $root_dir . 'includes/common.php';
page_header('Neuen Eintrag schreiben');

$valdiate_error = '';
$mode = (!$globals->get('mode')) ? '' : $globals->get('mode');
$reply_id = ($globals->get('id')) ? $globals->get('id') : '';

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
		if ($config->get('enable_captcha')) {
			if (!$globals->post('captcha_sum') || !$globals->post('captcha_checksum')) {
				$valdiate_error = 'captcha';
				break;
			} else {
				if ($globals->post('captcha_checksum') != md5($globals->post('captcha_sum'))) {
					$valdiate_error = 'captcha';
					break;
				}
			}
		}
		
		// Valdiate Fields
		$hide_email = ($globals->post('hide_email')) ? 1 : 0;
		$text = trim($globals->post('textarea'));
		$user = trim($globals->post('name'));
		$icq = ($globals->post('icq')) ? trim($globals->post('icq')) : '';
		$www = ($globals->post('www')) ? trim($globals->post('www')) : '';

		// Website URL valdieren
		if (!empty($icq)) {
			// Darf er die überhaupt angeben?
			if (!$config->get('enable_icq')) {
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
			if (!$config->get('enable_www')) {
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
		if (!$result = $db->sql_query($sql)) {
			message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
		}
		
		if ($db->sql_numrows($result)) {
			// Ja, also Fehlermeldung anzeigen
			$valdiate_error = 'flood';
			break;
		}
		
		// Beitrag in die Moderations-Warteschlange stellen?
		$active = ($config->get('moderated')) ? POST_WAIT_LIST : POST_ACTIVE;

		$sql = 'INSERT INTO ' . POSTS_TABLE . '
			(posts_id, posts_name, posts_email, posts_ip, posts_www, posts_icq, posts_text, posts_date, posts_active, posts_hide_email) 
			VALUES (' . $db->sql_escape('') . ', ' . $db->sql_escape($user) . ',' . $db->sql_escape($email) . ', ' . $db->sql_escape($user_ip) . ', ' . $db->sql_escape($www) . ', ' . $db->sql_escape($icq) . ', ' . $db->sql_escape($text) . ', ' . $db->sql_escape(time()) . ', ' . $db->sql_escape($active) . ', ' . $db->sql_escape($hide_email) . ')';
		if (!$result = $db->sql_query($sql)) {
			message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
		}
		
		// Bestätigungs E-Mail an den User
		if ($config->get('success_email')) {
			generate_mail($lang['email_post_user'], array($email), sprintf($config->get('success_email_text'), $user));
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
			generate_mail($lang['email_post_admin'], $email_adresses, sprintf($config->get('success_email_admin_text'), $user, $text, real_path()));
		}
		
		// Done! Bestätigung ausgeben.
		message_die($lang['posting_success'], sprintf($lang['posting_success_text'], '', "<a href=\" " . PAGE_INDEX . "\">", "</a>", "<a href=\"" . PAGE_POSTING . "\">", "</a>"));
		
	break;
	case 'preview':
		// Vorschau ist noch nicht möglich.
		$valdiate_error = 'Funktion nocht nicht verfügbar!';
		break;
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
if ($config->get('enable_icq')) {
	$template->assign_block_vars('icq_enabled', array());
}

// User darf Homepage angeben
if ($config->get('enable_www')) {
	$template->assign_block_vars('www_enabled', array());
}

// Smilie und BBCode Status anzeigen
$bbcodes_status = (!$config->get('bbcode')) ? $lang['INACTIVE'] : $lang['ACTIVE'];
$smilies_status = (!$config->get('smilies')) ? $lang['INACTIVE'] : $lang['ACTIVE'];

// Fehler beim valdieren der Userdaten?
if (!empty($valdiate_error)) {
	$template->assign_block_vars('valdiate_error', array());
	$error_message = valdiate_error($valdiate_error);
}

// Textfeld des Formulars füllen
// Wenn Zitat gewählt, geht dieses vor
if (isset($reply_text) && !empty($reply_text)) {
	$textarea = $encode->encode_html($reply_text);
} elseif ($globals->post('textarea')) {
	$textarea = $encode->encode_html($globals->post('textarea'));
} else {
	$textarea = '';
}

if ($config->get('enable_captcha')) {
	$template->assign_block_vars('captcha_enabled', array());
}

// Captcha
$captcha_a = round(rand(1, 100) / 10);
$captcha_b = round(rand(1, 100) / 10);
$captcha_checksum = md5($captcha_a + $captcha_b);

// Template Vars
$template->assign_vars(array(
	'TEXTAREA'		=> $textarea,
	'NAME'			=> ($globals->post('name')) ? $encode->encode_html($globals->post('name')) : "",
	'EMAIL'			=> ($globals->post('email')) ? $encode->encode_html($globals->post('email')) : "",
	'ICQ'			=> ($globals->post('icq')) ? $encode->encode_html($globals->post('icq')) : "",
	'WWW'			=> ($globals->post('www')) ? $encode->encode_html($globals->post('www')) : "",
	'HIDE_EMAIL'		=> ($globals->post('hide_email')) ? "checked=\"checked\"" : "",
	'ERROR_MESSAGE'		=> (isset($error_message) && !empty($error_message)) ? $error_message : "",

	'CAPTCHA_CHECKSUM'	=> $captcha_checksum,
	'CAPTCHA_QUESTION'	=> sprintf($lang['CAPTCHA_QUESTION'], $captcha_a, $captcha_b),

	'SMILIES_STATUS'	=> $smilies_status,
	'BBCODE_STATUS'		=> $bbcodes_status,
	
	'ERROR_TITLE'		=> $lang['ERROR_MAIN'],
	'CAPTCHA_TITLE'		=> $lang['CAPTCHA_TITLE'],
	'HTML_STATUS'		=> $lang['INACTIVE'],
	'L_HTML'		=> $lang['HTML'],
	'L_BBCODE'		=> $lang['BBCODE'],
	'L_SMILIES'		=> $lang['SMILIES'],
	'L_WWW'			=> $lang['WWW'],
	'L_ICQ'			=> $lang['ICQ'],
	'L_EMAIL'		=> $lang['EMAIL'],
	'L_NAME'		=> $lang['NAME'],
	'L_MESSAGE'		=> $lang['MESSAGE'],
	'L_WRITE_NEW'		=> $lang['POSTING_WRITE_NEW'],
	'L_HIDE_EMAIL'		=> $lang['POSTING_HIDE_EMAIL'],
	'L_HIDE_EMAIL_YES'	=> $lang['POSTING_HIDE_EMAIL_YES'],
	'L_BACK_GUESTBOOK'	=> $lang['BACK_TO_GUESTBOOK'],
));

$template->pparse('index');

page_footer();

?>