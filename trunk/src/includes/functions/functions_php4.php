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
* @subpackage functions
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id: functions_php4.php 20 2008-03-27 14:02:56Z kwhark $
* @link       http://www.simlau.net/
*/

if (!function_exists('file_put_contents')) {
	function file_put_contents($filename, $data) {
        	if (is_array($data)) {
          	  $data = implode('', $data);
		}
        	$handle = fopen ($filename, 'w');
		$return = fwrite($handle, $data, strlen($data));
		fclose($handle);
		return $return;
    	}
}

// $double_encode wird in PHP4 nicht unterstützt
// Daher ein kleines Workaround, wir ignorieren es einfach!
if (substr(phpversion(), 0, 1) == 4)
{
	rename_function('htmlentities', 'php_htmlentities');
	
	function htmlentities($string, $quote_style = ENT_COMPAT, $charset = 'ISO-8859-1', $double_encode = true) {
		return php_htmlentities($string, $quote_style, $charset);
	}
}

?>