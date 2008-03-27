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

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';

switch ($mode)
{
	case 'clean':
		clean_database();
		message_die('Datenbanken wurden aufgeräumt', 'Datenbaken wurden aufgeräumt');
	break;
	default:
		message_die('Datenbank Wartung', 'In Zukunft kannst du deine Datenbank hier warten.<br />Im Moment es ist aber ledeglich moeglich, die die Datenbank <a href="database.php?mode=clean">aufräumen</a>.');
	break;
}

/*
$template->set_filenames(array(
	'index' => 'database_body.html',
));

$template->pparse('index');
*/

//
// Footer
//
include_once $root_dir . 'includes/footer.php';

?>