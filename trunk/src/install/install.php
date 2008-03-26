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
* @version    0.2.4
* @link       http://www.simlau.net/
*/

define('GUESTBOOK', true);

$root_dir = '../';
$qgb_version = '0.2.4';
require_once $root_dir . 'includes/config/config.php';
require_once $root_dir . 'includes/database/db_select.php';

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
	die('<b>FATAL ERROR:</b> SQL-Dump not found!');
	exit;
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
echo "<b>Administrator:<br /><input type=\"text\" name=\"username\" /><br /><br />";
echo "<b>Passwort:<br /><input type=\"text\" name=\"password\" /><br /><br />";
echo "<b>E-Mail Adresse:<br /><input type=\"text\" name=\"email\" /><br /><br />";
echo "<b><input type=\"submit\" name=\"install\" value=\"Installieren\" /><br />";
echo "</form>";

if (isset($_POST['install']))
{
	echo "<h3>Installiere</h3>";	

	// Config Update...
	$config_table = array(
		'enable_capatcha' => (isset($support['gd'])) ? 1 : 0,
	);

	$db = new $database_class($dbhost, $dbuser, $dbpasswd, $dbname);
	
	$sql_dump = file("dbms/$dbtype/install.sql");
	$sql_dump = str_replace('gbook_', $table_prefix, $sql_dump);
	$sql_query = $db->sql_split_dump($sql_dump);
	
	foreach ($sql_query as $sql)
	{
		if (trim($sql) != "")
		{
			if (false) //!$db->sql_query($sql))
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
}

?>