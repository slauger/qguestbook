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
$root_dir = './';
include_once $root_dir . 'includes/common.php';

page_header($lang['GUESTBOOK_ENTRY']);

// Sind überhaupt Einträge vorhanden?
$sql = 'SELECT COUNT(`posts_id`)
		FROM ' . POSTS_TABLE . '
	WHERE posts_active = ' . $db->sql_escape(POST_ACTIVE);
$result = $db->sql_query($sql);

$max = $db->sql_result($result, 0);

// Aktive Einträge da?
if (!$max || $max == 0) {
	message_die($lang['ERROR_MAIN'], sprintf($lang['GUESTBOOK_EMPTY'], '<a href="' . PAGE_POSTING . '">', '</a>'));
}

/**
 * $start -> nicht niedriger als 1 (bzw 0)
 * 	  -> muss nummeric sein
 * $limit -> nicht größer als $max - $limit
 * 	  -> muss nummeric sein
 *
 * 
 *
 */

// Variable gesetzt? Ansonsten Standart.
$start = ($globals->get('start')) ? $globals->get('start') : 0;

// Etwas Hardcoded, aber nötig.
if (!$globals->get('limit')) {
	if ($config->get('posts_site') == 0) {
		$limit = $max;
	} else {
		if ($config->get('posts_site') > $max) {
			$limit = $max;
		} else {
			$limit = $config->get('posts_site');
		}
	}
} else {
	if ($globals->get('limit') > $max) {
		$limit = $max - $limit;
	} elseif ($globals->get('limit') == 0) {
		$limit = $max;
	} else {
		if ($globals->get('limit') <= 0 || !is_numeric($globals->get('limit'))) {
			if ($config->get('posts_site') > $max) {
				$limit = $max;
			} else {
				$limit = $config->get('posts_site');
			}
		} else {
			$limit = $globals->get('limit');
		}
	}
}

// Überprüfen der Variablen
if ($start >= $max) $start = $max - $limit; // Start zu gross? Setze ihn auf grösstmöglichstes Resultat.
if ($start <= 0 || !is_numeric($start)) $start = 0; // Start = 1 falls falsch.
if ($start + $limit > $max) $start = $max - $limit; // Limit zu gross? Setze ihn auf grösstmöglichstes Resultat.


// Wie wird sortiert?
$postorder = ($globals->get('postorder')) ? $globals->get('postorder') : $config->get('postorder');
$postorder = ($postorder == 'asc' || $postorder == 'desc') ? $postorder : $config->get('postorder');

//
// Der ultimative SQL-Query.
// Verbindet die Beitrags Tabelle mit der, der Kommentare und
// diese wiederrum mit der, der Userdaten.
//
// Natürlich ermöglicht dieser Query auch, dass die Beiträge
// an sich, öfter als einmal kommentiert werden können.
// Das Script ist aber im Moment noch nicht darauf ausgelegt.
//
$sql = 'SELECT p.*, c.*, u.user_name
	FROM ' . POSTS_TABLE . ' p
		LEFT JOIN ' . COMMENTS_TABLE . ' AS c
		ON c.comment_post = p.posts_id
		LEFT JOIN ' . USERS_TABLE . ' AS u
		ON u.user_id = c.comment_user
			WHERE p.posts_active = ' . $db->sql_escape(POST_ACTIVE) . '
		ORDER BY p.posts_id ' . strtoupper($postorder) . '
	LIMIT ' . $db->sql_escape($start) . ', ' . $db->sql_escape($limit);

$result = $db->sql_query($sql);

// Zur Sicherheit überprüfen wir nochmals...
if (!$posts = $db->sql_numrows($result)) {
	if ($max || $max > 0) {
		$start = 0;
		$limit = ($config->get('limit') > $max) ? $max : $config->get('posts_site');
		$limit = ($limit == 0) ? $max : $limit;

		$sql = 'SELECT p.*, c.*, u.user_name
			FROM ' . POSTS_TABLE . ' p
				LEFT JOIN ' . COMMENTS_TABLE . ' AS c
				ON c.comment_post = p.posts_id
				LEFT JOIN ' . USERS_TABLE . ' AS u
				ON u.user_id = c.comment_user
					WHERE p.posts_active = ' . $db->sql_escape(POST_ACTIVE) . '
				ORDER BY p.posts_id ' . strtoupper($postorder) . '
			LIMIT ' . $db->sql_escape($start) . ', ' . $db->sql_escape($limit);
		
		if (!$posts = $db->sql_numrows($result)) {
			message_die($lang['ERROR_MAIN'], sprintf($lang['GUESTBOOK_EMPTY'], '<a href="' . PAGE_POSTING . '">', '</a>'));
		}
		
		$result = $db->sql_query($sql);
	} else {
		message_die($lang['ERROR_MAIN'], sprintf($lang['GUESTBOOK_EMPTY'], '<a href="' . PAGE_POSTING . '">', '</a>'));
	}
}

// Schönere Seiten
$start = (!$start || $start < 0) ? 0 : $start;
$next  = ($start + $limit > $max) ? $max - $limit : $start + $limit;
$last  = ($start - $limit >= 0) ? $start - $limit : 0;

// Template wird geladen
$template->set_filenames(array(
	'index' => 'index_body.html',
));

// $posts Einträge werden angezeigt
// von $start bis $limit
// nächste Seite $limit+$limit;

// Wichtige Variablen
$template->assign_vars(array(
	'LIMIT' => $limit,
	'L_AUTHOR' => $lang['AUTHOR'],
	'L_POST' => $lang['MESSAGE'],
	'L_POSTED' => $lang['POSTED'],
	'L_NEW_POST' => $lang['WRITE_NEW'],
	'L_SITES' => $lang['PAGES'],
	'L_VIEW_POSTS' => $lang['GUESTBOOK_ENTRY'],
	'POSTS_GUESTBOOK' => sprintf($lang['POSTS_COUNT'], $max),
	'POSTS_STATISTIC' => sprintf($lang['SHOW_FROM_TO'], $posts, $start + 1, $limit, $max),
	
	'U_PAGE_LAST' => PAGE_INDEX . "?start={$last}&amp;limit={$limit}&amp;postorder={$postorder}",
	'U_PAGE_NEXT' => PAGE_INDEX . "?start={$next}&amp;limit={$limit}&amp;postorder={$postorder}",

));

// Schnösel an qTemplate weitergeben!
while ($row = $db->sql_fetchrow($result)) {
	$template->assign_block_vars('posts', array(
		'ID' => $encode->encode_html($row['posts_id'], false),
		'USERNAME' => $encode->encode_html($row['posts_name']),
		'MESSAGE' => bbcode($encode->encode_html($row['posts_text'], false)),
		'DATE_FORMAT' => format_date($row['posts_date']),
		'U_ICQ' => icq_url($row['posts_icq']),
		'U_WWW' => $encode->encode_html($row['posts_www']),
		'U_EMAIL' => $encode->encode_html($row['posts_email']),
		'COMMENT_TEXT' => bbcode($row['comment_text']),
		'COMMENT_USERNAME' => $encode->encode_html($row['user_name']),
		'COMMENT_DATE' => format_date($row['comment_date']),
	));
	
	// Valide ICQ UIN? Dann anzeigen.
	if (valdiate_icq($row['posts_icq'])) {
		$template->assign_block_vars('posts.switch_icq', array());
	}
	// Valide Webseite? Dann ebenfalls anzeigen.
	if (valdiate_website($row['posts_www'])) {
		$template->assign_block_vars('posts.switch_homepage', array());
	}
	// Soll die Email versteckt werden?
	if ($row['posts_hide_email'] != 1) {
		$template->assign_block_vars('posts.switch_email', array());
	}
	// Auch noch nicht die beste Lösung...
	if (!empty($row['comment_id'])) {
		$template->assign_block_vars('posts.switch_comment', array());
	}
}

// Yay, it's done!
$template->pparse('index');

page_footer();

?>