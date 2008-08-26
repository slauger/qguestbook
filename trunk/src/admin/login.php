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
define('LOGIN_PAGE', true);
define('REQUIRED_AUTH_LEVEL', 0);

// Dateien includieren
$root_dir = '../';
include_once $root_dir . 'includes/common.php';

// Will der User sich ausloggen?
if ($globals->post('logout')) {
	if (user_logged_in()) {
		user_logout();
		message_die($lang['logout_success'], sprintf('Erfolgreich ausgeloggt'));
	}
}

if (user_logged_in(get_sid())) {
	header('Location: ' . PAGE_ADMIN_INDEX);
	exit;
}

// Sind Daten da?
if (!$globals->post('user_name') || !$globals->post('user_pass')) {

	page_header('Login');

	$template->set_filenames(array(
		'index'=> 'login_body.html',
	));
	
	$template->assign_vars(array(
		'L_LOGIN'		=> $lang['LOGIN_TITLE'],
		'L_LOGIN_DESC'		=> $lang['LOGIN_DESC'],
		'L_USERNAME'		=> $lang['USERNAME'],
		'L_PASSWORD'		=> $lang['PASSWORD'],
	));

	$template->pparse('index');

	page_footer();
}

// Ok, Daten sind vorhanden, aber sind sie auch richtig?
if ($user_id = user_exists($globals->post('user_name'), $globals->post('user_pass'))) {
	user_login($user_id, generate_sid());
	message_die($lang['LOGIN_MESSAGE_SUCCESS'], sprintf($lang['LOGIN_MESSAGE_SUCCESS_DESC'], '<a href="' . PAGE_ADMIN_INDEX . '">', '</a>', '<a href="' . PAGE_INDEX . '">', '</a>'));
} else {
	message_die($lang['ERROR_MAIN'], sprintf($lang['LOGIN_ERROR_DATA'], '<a href="' . PAGE_ADMIN_LOGIN . '">', '</a>', '<a href="' . PAGE_INDEX . '">', '</a>'));
}

?>