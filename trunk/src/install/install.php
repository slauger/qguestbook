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
* @subpackage install
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id: install.php 20 2008-03-27 14:02:56Z kwhark $
* @link       http://www.simlau.net/
*/

define('GUESTBOOK', true);

$root_dir = '../';
$qgb_version = '0.2.4';
require_once $root_dir . 'includes/config/config.php';
require_once $root_dir . 'includes/database/db_select.php';

// Danach bitte die folgende Zeile löschen
trigger_error('please edit the install.php before install qgb!', E_USER_WARNING);

// GRUNDKONFIGURATION
$config = array(
	'default_lang' => '1',
	'active' => '1',
	'description' => 'Das OpenSource Gästebuch',
	'sitename' => 'qGuestbook',
	'page_title' => 'Gästebuch',
	'debug_info' => '1',
	'show_warnings' => '1',
	'disable_msg' => 'Das Gästebuch wurde vom Administrator gesperrt!',
	'submit_msg' => 'Vielen Dank für deinen Eintrag in unserem Gästebuch!',
	'enable_icq' => '1',
	'enable_www' => '1',
	'version' => 'SVN Subversion',
	'smilies_path' => 'images/smiles/',
	'posts_site' => '5',
	'admin_link' => '1',
	'gzip' => '0',
	'moderated' => '0',
	'default_style' => '1',
	'postorder' => 'desc',
	'default_dateformat' => 'd.m.Y, H:i',
	'allow_mark_post' => '1',
	'charset' => 'UTF-8',
	'startdate' => '1178009196',
	'max_lenght' => '55',
	'success_email_text' => 'Hallo %1s!
				Vielen Dank fÃ¼r deinen Eintrag in unser GÃ¤stebuch.
				Schau doch einfach noch mal auf unserer Homepage vorbei. ;)
				
				Wichtig: Deine IP Adresse wurde aus SicherheitsgrÃ¼nden gespeichert. Sie ist vom Administrator jederzeit einsehbar.',
	'email_admin' => 'qGuestbook <admin@example.com',
	'email_mode' => '1',
	'smtp_server' => 'smtp.example.com',
	'smtp_port' => '25',
	'smtp_helo' => 'Hello, nice to meet you.',
	'smtp_auth' => 'SMTP',
	'smtp_user' => '',
	'smtp_pass' => '',
	'email_html' => '0',
	'script_path' => '/qBook/',
	'success_email' => '0',
	'success_email_admin' => '0',
	'success_email_admin_text' => 'Hallo lieber Moderator!
					
					Der Benutzer %1s hat sich soeben in dein GÃ¤stebuch eingetragen.
					
					Er hat folgendes geschrieben:
					
					---
					%2s
					---
					
					Dein GÃ¤stebuch kannst du unter der folgenden Adresse erreichen: %3s',
	'sendmail' => '/usr/sbin/sendmail -ti',
	'success_email_admin_all' => '0',
	'bbcode' => '1',
	'smilies' => '1',
	'rss_limit' => '10',
	'language' => 'de',
	'password_length' => '4',
	'newsfeed' => '1',
	'https' => '0',
	'censor_words' => '1',
	'limit_images' => '3',
);

error_reporting(E_ALL);

$support = array();

// Brauchen wir die Zusatzfunktionen für PHP4?
// Backports für file_put_contents() etc
if (substr(phpversion(), 0, 1) == 4)
{
	$support['php4'] = true;
}

// GD Support?
if (function_exists('gd_info'))
{
	$gd_info = gd_info();
	if (substr($gd_info['GD Version'], 0, 1) == 2)
	{
		$support['gd'] = substr($gd_info['GD Version'], 0, 3);
	}
}

// MySQL
if (function_exists('mysql_get_client_info'))
{
	$support['mysql'] = @mysql_get_client_info();
	if (substr(mysql_get_client_info(), 0, 1) == 5)
	{
		$support['mysql5'] = @mysql_get_client_info();
	}
}

// SQLite
if (function_exists('sqlite_libversion'))
{
	$support['sqlite'] = @sqlite_libversion();
}

// Wird die gewünschte DB unterstützt?
if (!isset($support[$dbtype]) && empty($support[$dbtype]))
{
	die('<b>FATAL ERROR:</b> Database not supported!');
	exit;
}

// Dump vorhanden?
if (!is_file("dbms/$dbtype/install.sql"))
{
	trigger_error('sql dump not found!', E_USER_ERROR);
}

echo "<h2>qGuestbook Installation</h2>";

echo "<h3>PHP Einstellungen</h3>";
echo "<ul>";
echo "	<li>PHP Version: Ok (" . phpversion() . ")</li>";
echo "  <li>PHP GD Extension: " . ((isset($support['gd'])) ? 'Ok ('.$support['gd'].' oder hoeher)' : 'Nicht verfuegbar, GD Funktionen werden deaktivert!') . "</li>";
echo "  <li>MySQL Support: " . ((isset($support['mysql'])) ? 'Ok ('.$support['mysql'].')' : 'Nicht verfuegbar') . "</li>";
echo "	<li>SQLite Support: " . ((isset($support['sqlite'])) ? 'Ok ('.$support['sqlite'].')' : 'Nicht verfuegbar') . "</li>";
echo "</ul>";

echo "<h3>Grundkonfiguration</h3>";
echo "<form action=\"install.php\" method=\"post\">";
//echo "<b>Administrator:<br /><input type=\"text\" name=\"username\" /><br /><br />";
//echo "<b>Passwort:<br /><input type=\"text\" name=\"password\" /><br /><br />";
//echo "<b>E-Mail Adresse:<br /><input type=\"text\" name=\"email\" /><br /><br />";
echo "<b><input type=\"submit\" name=\"install\" value=\"Installieren\" /><br />";
echo "</form>";

if (isset($_POST['install']))
{
	echo "<h3>Installiere</h3>";	

	// Config Update...
	/*$config_table = array(
		'enable_capatcha' => (isset($support['gd'])) ? 1 : 0,
	);*/

	$db = new $database_class($dbhost, $dbuser, $dbpasswd, $dbname);
	
	$sql_dump = file("dbms/$dbtype/install.sql");
	$sql_dump = str_replace('gbook_', $table_prefix, $sql_dump);
	$sql_query = $db->sql_split_dump($sql_dump);
	
	foreach ($sql_query as $sql)
	{
		if (trim($sql) != "")
		{
			if (!$db->sql_query($sql))
			{
				$error = $db->sql_error();
				$result[] = htmlspecialchars($sql)."<br /><font style=\"color: red;\"><b>+++ Fehlgeschlagen (".$error['name'].")</b></font><br />";
			}
			else
			{
				$result[] = htmlspecialchars($sql)."<br /><br /><font style=\"color: green;\"><b>+++ Erfolgreich</b></font>";
			}
		}
	}

	echo "<h2>Installiere...</h2>";
	echo implode('<br /><br />', $result);
	
	echo "<h2>Update Konfiguration...</h2>";
	foreach ($config as $key => $value)
	{
		// $config->update($key, $value);
	}
	
	echo "fertig!";
}

?>