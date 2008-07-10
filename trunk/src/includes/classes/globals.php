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
* @subpackage common
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

if (!defined('GUESTBOOK')) {
    die("Hacking attempt");
    exit;
}

/**
* qGlobals Befreit uns davon, die Variablen immer
* auf ihre Existenz hin, zu prüfen.
*
* Die Klasse wird in Zukunft auch noch ein paar andere
* Sachen übernehmen...
*/

Class qGlobals
{
	private $_GET;
	private $_POST;
	private $_SERVER;
	
	public function __construct()
	{
		$this->_GET = $_GET;
		$this->_POST = $_POST;
		$this->_SERVER = $_SERVER;
	}
	
	public function get($var)
	{
		if (!isset($_GET[$var]) || empty($_GET[$var])) {
			return false;
		}
		return $_GET[$var];
	}
	
	public function post($var)
	{
		if (!isset($_POST[$var]) || empty($_POST[$var])) {
			return false;
		}
		return $_POST[$var];
	}
	
	public function server($var)
	{
		if (!isset($_SERVER[$var]) || empty($_SERVER[$var])) {
			return false;
		}
		return $_SERVER[$var];
	}
	
}

?>