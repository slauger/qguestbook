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
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

if ( !defined('GUESTBOOK') )
{
    die("Hacking attempt");
    exit;
}

//
// Badwords
//
function valdiate_badwords($string)
{
	global $db;
	$sql = 'SELECT words_id, words_word, words_censor
		FROM ' . BADWORDS_TABLE;
	$result = $db->sql_query($sql);
	if ($db->sql_fetchrow($result))
	{
		if (preg_match('/' .  $row['words_word'] . '/is', $string, $matches))
		{
			return false;
		}
	}
	return true;
}

//
// Ist ein Wort im String zu lang?
//
function valdiate_lenght($string, $lenght)
{
	$explode = explode(' ', $string);
	foreach ($explode as $value)
	{
		if (strlen($value) > $lenght)
		{
			return false;
			// $text = chunk_split($value, $lenght, '<br />');
		}
	}
	return true;
}

//
// Valdiert einen Benutzernamen
//
function valdiate_username($username)
{
	if (!empty($username))
	{
		return $username;
	}
	return false;
}

//
// Valdiert eine ICQ-Nummer
//
function valdiate_icq($icq)
{
	if (!preg_match('/^[0-9]+$/', $icq))
	{
		$icq = '';
	}
	return $icq;
}

//
// Valdiert eine Email-Adresse
//
function valdiate_email($email) {
	if (empty($email))
	{
		return false;
	}
	if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $email))
	{
		return false;
	}
	return $email;
}

//
// Valdiert eine URL
//
function valdiate_website($website)
{
	if (!empty($website))
	{
		if (!preg_match('#^http[s]?\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $website))
		{
			$website = '';
		}
	}
	return $website;
}

//
// Valdiert ein Textfeld
//
function validate_field($field)
{
	if (!empty($field))
	{
		$string = (strlen($string) < 2) ? '' : $string;
		$string = (ereg("[ \t]" ,$string)) ? '' : $string;
	}
	return $field;
}

//
// Valdiert ein Passwort
//
function valdiate_password($string)
{
	global $config_table;

	if (strlen($string) > $config_table['password_length'])
	{
		return md5($string);
	}
	return false;
}

?>