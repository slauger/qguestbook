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

// Konstanten
define('GUESTBOOK', true);
define('ADMIN_PAGE', true);
define('REQUIRED_AUTH_LEVEL', 2);

$root_dir = '../';
include_once $root_dir . 'includes/common.php';

page_header('Aktuelle Statistik');

// Template setzen
$template->set_filenames(array(
	'index' => 'index_body.html',
));

//
// Einige tolle SQL COUNT() Querys
// für unsere Statistik!
//

// Eintraege gesamt
$sql = 'SELECT COUNT(`posts_id`)
	FROM ' . POSTS_TABLE;

if (!$result = $db->sql_query($sql)) {
	message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
}

$posts_count = $db->sql_result($result, 0);

// Eintraege in der Warteschlange
$sql = 'SELECT posts_id
	FROM ' . POSTS_TABLE . '
	WHERE posts_active = ' . POST_WAIT_LIST;

if (!$result = $db->sql_query($sql)) {
	message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
}

$wait_list_count = $db->sql_result($result, 0);

// Count Users
$sql = 'SELECT COUNT(`user_id`)
	FROM ' . USERS_TABLE;

if (!$result = $db->sql_query($sql)) {
	message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
}

$users_count = $db->sql_result($result, 0);

// Users Online
$sql = 'SELECT user_id, user_name
	FROM ' . USERS_TABLE . '
		WHERE user_session <> ' . $db->sql_escape('');

if (!$result = $db->sql_query($sql)) {
	message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
}

$user_online = array();
while ($row = $db->sql_fetchrow($result)) {
	$user_online[] = array(
		'user_id' => $row['user_id'],
		'user_name' => "<b>" . $row['user_name'] . "</b>",
	);
}

$user_names = array();
foreach ($user_online as $key => $value) {
	$user_names[] = '<b>' . $user_online[$key]['user_name'] . '</b>';
}
$user_names = implode(', ', $user_names);
$users_online = count($user_online);
$users_online = ($users_online > 0) ? $users_online . ' Benutzer online: ' : $users_online . ' Benutzer online:';
$users_explain = ($users_online > 1) ? 'Moderatoren gesamt' : 'Moderator gesamt';

// Statistikzeugs
$php_version = phpversion();
$guestbook_days = round((time() - $config_table['startdate']) / 86400);
$guestbook_date = format_date($config_table['startdate']);
$posts_per_day = sprintf("%.2f Einträge pro Tag", $posts_count / $guestbook_days);
$post_per_day_desc = ($guestbook_days > 1) ? $lang['days'] : $lang['day'];
$register_globals = (ini_get('register_globals') == '1') ? "<font color=\"#CA3200\">" . $lang['switchted_on'] . "</font>" : "<font color=\"#00CA0F\">" . $lang['switchted_off'] . "</font>";
$safe_mode = (ini_get('safe_mode') == '1') ? "<font color=\"#00CA0F\">" . $lang['Switchted_On'] . "</font>" : "<font color=\"#CA3200\">" . $lang['switchted_off'] . "</font>";
$magic_quotes_gpc = (ini_get('magic_quotes_gpc') == '1') ? "<font color=\"#00CA0F\">" . $lang['switchted_on'] . "</font>" : "<font color=\"#CA3200\">" . $lang['switchted_off'] . "</font>";
$wait_list_count = ($wait_list_count > 0) ? $wait_list_count . ' Einträge' : 'Keine Eintraege';
$posts_count = ($posts_count > 0) ? $posts_count . ' Einträge' : 'Keine Eintraege';
$install_dir_exists = (is_dir($root_dir.'install')) ? "<br /><font color=\"#CA3200\"><b>Achtung: Das Installationverzeichniss wurde noch nicht geloescht!</b></font>" : "";

$template->assign_vars(array(
	'VERSION' => $config_table['version'],
	'L_ADMIN_WELCOME' => $lang['admin_welcome'],
	'L_ADMIN_WELCOME_LONG' => $lang['admin_welcome_long'],
	'L_WAIT_LIST' => $lang['waitlist_desc'],
	'L_POSTS_COUNT' => $lang['posts_count_desc'],
	'L_INSTALLED' => $lang['installed_desc'],
	'L_STATISTIC_TITLE' => $lang['statistic_infos_title'],
	'L_STATISTIC_DESC' => $lang['statistic_infos_desc'],
	'L_SECURITY_TITLE' => $lang['security_infos_title'],
	'L_SECURITY_DESC' => $lang['security_infos_desc'],
	
	'POSTS_COUNT' => $posts_count,
	'WAIT_LIST' => $wait_list_count,
	// PHP Infos
	'SAFE_MODE' => $safe_mode,
	'REGISTER_GLOBALS' => $register_globals,
	'MAGIC_QUOTES_GPC' => $magic_quotes_gpc,
	'PHP_VERSION' => $php_version,
	// Stats
	'POSTS_PER_DAY' => $posts_per_day,
	'START_DATE' => "Vor {$guestbook_days} Tagen", //sprintf('%s %s', round($guestbook_days, 2), $post_per_day_desc),
	'USERS_ONLINE' => sprintf('%1s %2s', $users_online, $user_names),
	'USERS_ONLINE_EXPLAIN' => $user_names,
	'SQL_SERVER' => $db->sql_server_info(),
	'USERS_COUNT' => "$users_count registrierte Benutzer gesamt",
	'INSTALL_DIR' => $install_dir_exists,
));

$template->pparse('index');

page_footer();

?>