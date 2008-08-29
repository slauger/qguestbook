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
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

define('GUESTBOOK', true);
define('ADMIN_PAGE', true);
define('REQUIRED_AUTH_LEVEL', 2);

$root_dir = '../';
include_once $root_dir . 'includes/common.php';

page_header('Beitragsmoderation');

//
// WIRD FUER 0.2.5 UEBERARBEITET!
// Im Moment: case comment

$url_append = '';
$mode = (!$globals->get('mode')) ? '' : $globals->get('mode');
$id   = (!$globals->get('id'))   ? '' : $globals->get('id');
$view = (!$globals->get('view')) ? '' : $globals->get('view');
$view = ($view == 'all' || $view == 'waitlist') ? $view : 'all';

switch ($mode) {
	/**
	 * Kommentar hinzufügen und/oder bearbeiten
	 * Updated: 29.08.2008 by kwhark
	 */
	case 'comment':
		// Soll das Kommentar geloescht werden?
		if ($globals->post('delete') && $globals->post('comment_id')) {
			$sql = 'DELETE FROM ' . COMMENTS_TABLE . '
					WHERE comment_id = ' . $db->sql_escape($globals->post('comment_id')) . '
				LIMIT 1';
			if (!$result = $db->sql_query($sql)) {
				$error = $db->sql_error();
				message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
			}
			message_die('Das Kommentar wurde geloescht', 'Das Kommentar wurde erfolgreich geloescht!');
		}
		
		// Kommentar in die DB eintragen
		if ($globals->post('textarea') && $globals->post('post_id')) {
			$sql = 'INSERT INTO ' . COMMENTS_TABLE . '
				(`comment_id`, `comment_post`, `comment_user`, `comment_text`, `comment_date`)
				VALUES (' . $db->sql_escape('') . ', ' . $db->sql_escape($globals->post('post_id')) . ', ' . $db->sql_escape($_SESSION['user_id']) . ', ' . $db->sql_escape($globals->post('textarea')) . ', ' . $db->sql_escape(time()) . ')';
			
			if (!$result = $db->sql_query($sql)) {
				message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
			}
			message_die($lang['guestbook_error'], 'Kommentar wurde hinzugefuegt!');
		}
		
		if (!$globals->get('id')) {
			message_die($lang['ERROR_MAIN'], 'Du hast keinen Beitrag ausgewählt, den du kommentieren willst.');
		}
		
		// Kommentare vorhanden?
		$sql = 'SELECT COUNT(`comment_id`)
				FROM ' . COMMENTS_TABLE . '
			WHERE comment_post = ' . $db->sql_escape($globals->get('id'));
		
		if (!$result = $db->sql_query($sql)) {
			message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
		}
		
		$comments = $db->sql_result($result, 0);
		
		$template->set_filenames(array(
			'body' => 'comment_body.html',
		));
		
		if ($comments && $comments == 1) {
			$sql = 'SELECT comment_id, comment_text
					FROM ' . COMMENTS_TABLE . '
					WHERE comment_post = ' . $db->sql_escape($globals->get('id')) . '
				LIMIT 1';

			if (!$result = $db->sql_query($sql)) {
				$error = $db->sql_error();
				message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
			}
			
			$template->assign_block_vars('comment_exists', array());
			while ($row = $db->sql_fetchrow($result)) {
				$template->assign_vars(array(
					'COMMENT_ID'	=> $encode->encode_html($row['comment_id']),
					'COMMENT_TEXT'	=> $encode->encode_html($row['comment_text']),
				));
			}
		}
		
		$template->assign_vars(array(
			'POST_ID'	=> $encode->encode_html($globals->get('id')),
		));
		
		$template->pparse('body');
	break;
	case 'edit':
		message_die($lang['guestbook_error'], 'Diese Funktion ist noch nicht verfügbar!');
	break;
	case 'delete':
		if (!isset($_GET['id']) || empty($_GET['id']))
		{
			message_die('Kein Beitrag ausgewählt', 'Du musst einen Beitrag auswählen, den du löschen willst.');
		}

		$sql = 'DELETE FROM ' . POSTS_TABLE . '
				WHERE posts_id = ' . $db->sql_escape($_GET['id']) . '
			LIMIT 1';
		$db->sql_query($sql);

		message_die('Eintrag wurde gelöscht', 'Der gewählte Eintrag wurde erfolgreich gelöscht<br /><a href="moderate.php?view='.$view.'">hier</a> gehts zurück');
	break;
	case 'disable':
		$value = 0;
	case 'enable':
		if (!isset($value)) {
			$value = 1;
			// hier Email an den User senden :)
		}

		if (isset($_GET['view']) && $_GET['view'] == 'all') {
			$url_append = '?view=all';
		}

		if (!isset($_GET['id']) || empty($_GET['id'])) {
			message_die('Kein Beitrag ausgewählt', 'Du musst einen Beitrag auswählen, den du aktivieren bzw. deaktiveren willst.');
		}

		$sql = 'UPDATE ' . POSTS_TABLE . '
			SET posts_active = ' . $db->sql_escape($value) . '
			WHERE posts_id = ' . $db->sql_escape($_GET['id']) . '
			LIMIT 1';
		$db->sql_query($sql);

		message_die('Änderung des Status erfolgreich', "Der Status des gewünschten Beitrags wurde geändert<br /><br /><a href=\"moderate.php?{$url_append}\">Hier</a> klicken, um zur Beitragsmoderation zurückzukehren.");
	break;
	default:
		$template->set_filenames(array(
			'body' => 'moderate_body.html',
		));

		$view_allowed = array('waitlist', 'all');
		if (!in_array($view, $view_allowed)) {
			$view = 'waitlist';
		}
		
		$where_statement = '';
		
		if ($view != 'all') {
			switch ($view) {
			 	case 'waitlist':
				// $where_statement = 'WHERE p.posts_id = ' . POST_WAIT_LIST;
				break;
			}
		}
		
		$sql = 'SELECT p.*, c.*, u.user_name
			FROM ' . POSTS_TABLE . ' p
				LEFT JOIN ' . COMMENTS_TABLE . ' AS c
					ON c.comment_post = p.posts_id
				LEFT JOIN ' . USERS_TABLE . ' AS u
					ON u.user_id = c.comment_user
				' . $where_statement . '
			ORDER BY p.posts_id DESC';

	$result = $db->sql_query($sql);

		if (!$db->sql_numrows($result))
		{
			if (empty($waitlist) || $view == 'waitlist')
			{
				message_die('Keine Beiträge vorhanden', "Es sind keine Beiträge in der Warteliste vorhanden.<br /><br />Klicke <a href=\"moderate.php?view=all\">hier</a> um alle Einträge anzuzeigen, die vorhanden sind.<br /><br />Klicke <a href=\"" . PAGE_ADMIN_INDEX . "\">hier</a> um zur Moderatoren Startseite zurückzukehren.");
			}
			else
			{
				message_die('Keine Beiträge vorhanden', "Es sind keine Beiträge zum Moderieren vorhanden.<br /><br />Klicke <a href=\"" . PAGE_ADMIN_INDEX . "\">hier</a> um zur Moderatoren Startseite zurückzukehren.<br /><br />Klicke <a href=\"" . PAGE_INDEX . "\">hier</a> um zum Gästebuch zurückzukehren.");
			}
		}

		$whois_url = 'http://network-tools.com/default.asp?host=%1s';

		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('moderate_posts', array(
				'POST_ID' => $encode->encode_html($row['posts_id']),
				'POST_NAME' => $encode->encode_html($row['posts_name']),
				'POST_TEXT' => nl2br($encode->encode_html($row['posts_text'])),
				'POST_EMAIL' => $encode->encode_html($row['posts_email']),
				'POST_ICQ' => $encode->encode_html($row['posts_icq']),
				'POST_WWW' => $encode->encode_html($row['posts_www']),
				'POST_IP' => decode_ip($row['posts_ip']),
				'POST_DATE' => format_date($row['posts_date']),
				'POST_IP_URL' => sprintf($whois_url, decode_ip($row['posts_ip'])),
				'COMMENT_USER' => $encode->encode_html($row['user_name']),
				'COMMENT_TEXT' => nl2br($encode->encode_html($row['comment_text'])),
				'COMMENT_DATE' => format_date($row['comment_date']),
				'URL_APPEND' => $url_append,
			));
			
			if (!empty($row['comment_text'])) {
				$template->assign_block_vars('moderate_posts.switch_comment', array());
			}
			
			if (valdiate_website($row['posts_www'])) {
				$template->assign_block_vars('moderate_posts.switch_www', array());
			}

			if (valdiate_icq($row['posts_icq'])) {
				$template->assign_block_vars('moderate_posts.switch_icq', array());
			}

			if (isset($row['posts_active']) && $row['posts_active'] == POST_ACTIVE) {
				$template->assign_block_vars('moderate_posts.switch_post_active', array());
			}
			elseif (isset($row['posts_active']) && $row['posts_active'] == POST_INACTIVE) {
				$template->assign_block_vars('moderate_posts.switch_post_inactive', array());
			}
			elseif (isset($row['posts_active']) && $row['posts_active'] == POST_WAIT_LIST) {
				$template->assign_block_vars('moderate_posts.switch_post_waitlist', array());
			} else {
				trigger_error('Es wurde an den SQL Tabellen herumgespielt!', E_USER_ERROR);
				exit;
			}
		}

		$template->pparse('body');
	break;
}

page_footer();

?>