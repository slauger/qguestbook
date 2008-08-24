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
* @version    CVS: $Id: module.php 82 2008-07-10 12:27:15Z kwhark $
* @link       http://www.simlau.net/
*/

define('GUESTBOOK', true);
define('ADMIN_PAGE', true);

$root_dir = '../';
include_once $root_dir . "includes/common.php";

if (!isset($_GET['module']) || empty($_GET['module'])) {
	message_die('Bitte wählen sie ein Modul aus!', 'Modul auswählen!');
}

if (is_file($root_dir.'modules/'.$_GET['module'].'/admin.php')) {
	include_once $root_dir.'modules/'.$_GET['module'].'/admin.php';
} else {
	message_die('Fehlerhafter Aufruf!', '');
}

?>