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
* @version    0.2.4
* @link       http://www.simlau.net/
*/

//
// Konstanten
//
define('GUESTBOOK', true);
define('ADMIN_PAGE', true);
define('REQUIRED_AUTH_LEVEL', 2);

//
// Dateien includieren
//
$root_dir = '../';
include_once $root_dir . 'includes/common.php';
include_once $root_dir . 'includes/header.php';
include_once $root_dir . 'includes/email/htmlMimeMail5.php';
require_once $root_dir . 'includes/bbcode/stringparser_bbcode.class.php';
include_once $root_dir . 'includes/bbcode/bbcode.php';

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';

if (isset($_POST['submit']))
{
	$subject = (isset($_POST['subject'])) ? $_POST['subject'] : '';
	$email = (isset($_POST['email'])) ? $_POST['email'] : '';
	$message = (isset($_POST['message'])) ? $_POST['message'] : '';

	if (empty($subject))
	{
		message_die($lang['guestbook_error'], 'Du musst zu deiner Email einen Betreff angeben!');
	}

	if (empty($message))
	{
		message_die($lang['guestbook_error'], 'Du musst zu deiner Email einen Text angeben!');
	}

	switch ($email)
	{
		case 1:
			$email_adresses[] = $config_table['email_admin'];
		break;
		case 2:
			$sql = 'SELECT user_name, user_email
				FROM ' . USERS_TABLE;
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$email_adresses[] = format_email($row['user_name'], $row['user_email']);
			}
		break;
		default:
			message_die($lang['guestbook_error'], 'Bitte benutze das Formular im Moderatoren Bereich!');
	}
	generate_mail($subject, $email_adresses, $message);
	message_die('Email wurde verand ;)', 'Deine Email wurde an die gewählten Empfänger verschickt.<br /><br />');
}

$template->set_filenames(array(
	'index' => 'email_body.html',
));

$template->assign_vars(array(
	'L_EMAIL_TITLE' => 'Massen E-Mail Versand',
	'L_EMAIL_EXPLAIN' => 'Auf dieser Seite kannst du E-Mails an die Moderatoren, oder an den Administrator versenden.',
));

$template->pparse('index');

include_once $root_dir . 'includes/footer.php';

?>
