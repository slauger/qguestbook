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
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

define('GUESTBOOK', true);

$root_dir = '../';
$qgb_version = '0.2.5';
require_once $root_dir . 'includes/config/config.php';

// Grundkonfiguration
// Alles in diesem Array wird automatisch neu in die Config geschrieben
// Kann z. B. Installationsdatum sein
$config = array();

error_reporting(E_ALL);

// Brauchen wir die Zusatzfunktionen für PHP4?
// Backports für file_put_contents() etc
if (substr(phpversion(), 0, 1) == 4) {
	$support['php4'] = true;
	die('Sorry, but qGuestbook doesent support PHP4 at this time!');
	exit;
}

// GD Support?
if (function_exists('gd_info')) {
	$gd_info = gd_info();
	if (substr($gd_info['GD Version'], 0, 1) == 2) {
		$support['gd'] = $gd_info['GD Version']; //substr($gd_info['GD Version'], 0, 3);
	}
}

// MySQL
if (function_exists('mysql_get_client_info')) {
	$support['mysql'] = @mysql_get_client_info();
	if (substr(mysql_get_client_info(), 0, 1) == 5)
	{
		$support['mysql5'] = @mysql_get_client_info();
	}
}

// SQLite
if (function_exists('sqlite_libversion')) {
	$support['sqlite'] = @sqlite_libversion();
}

// Wird die gewünschte DB unterstützt?
if (!isset($support['mysql']) || !isset($support['mysql5'])) {
	die('qGuestbook benoetigt MySQL, alle anderen Datenbanksystem befinden sich noch im Teststadium!');
	exit;
}

// Dump vorhanden?
if (!is_file("dbms/mysql/install.sql")) {
	trigger_error('sql dump not found!', E_USER_ERROR);
}

echo "<html>";
echo "<head>
	<style>
/**
  SoScy qGuestbook Theme

  Dies ist eine Portierung des \"Soscy\"
  Templates für phpMyGuestbook.
  Angepasst für qGuestbook mit einigen
  grafischen Verbesserungen, vorallem für
  Mozilla Browser

  @version: CVS \$Id$
*/

/* Allgemeine Konfiguration */
body
{
	font-family: Arial, Helvetica, sans-serif;
	font-size:14px;
	background-color: #F8F8F8;
}

/* alle Links, die nicht anderweitig festgelegt wurden */
a
{
	color: #043698;
	text-decoration: none;
}
a:hover
{
	color: #A00000;
	text-decoration: underline;
}

/* Text für Hinweise, etc. */
.gensmall {
 	font-size: 10px;
	color: #000000;
	/* background-color:#000000; */
}
a.gensmall
{
	color: #043698;
	text-decoration: none;
}
a.gensmall:visited {
	color: #003090;
	text-decoration: none;
}
a.gensmall:active {
	color: #FF0000;
	text-decoration: underline;
}
a.gensmall:hover {
	color: #A00000;
	text-decoration: underline;
}

/* Quote & Code blocks */
.code {
	font-family: Courier, 'Courier New', sans-serif; font-size: 11px; color: #006600;
	background-color: #FAFAFA; border: #D1D7DC; border-style: solid;
	border-left-width: 1px; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px
}

.quote {
	font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; color: #444444; line-height: 125%;
	background-color: #FAFAFA; border: #D1D7DC; border-style: solid;
	border-left-width: 1px; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px
}

/*
Der Schrott hier wird nicht gebraucht...

/*input,textarea, select {
    color : #000000;
    font-family : Verdana, Arial, Helvetica, sans-serif;
    font-size : 11px;
    font-weight : normal;
}

input.post, textarea.post, select {
    background-color : #E9EEF7;
}
*/

/*
  Die Definition für den Copyright Hinweis im Footer
  Bitte möglichst lesbar lassen ;)
*/
.copyright
{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #444444;
	font-size: 11px;
	letter-spacing: -1px;
}
a.copyright {
	color: #333333;
	text-decoration: none;
}
a.copyright:hover {
	color: #000000;
	text-decoration: underline;
}

/* Tabellen und ihre Besonderheiten */
TD {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
}
TH, TD.th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	background-color: #7E98CB;
	height: 25px;
	font-size: 11px;
	font-weight: bold;
color: #FFFF00;
}
TD.row1 {
 	border-bottom: 1px #021A4A solid;
	border-top: 1px #021A4A solid;
	
	background-color: #F8F8F8;
	width: 7%;
	/*vertical-align: middle;*/
}

TD.row2 {
	background-color: #F0F0F0;
	/*vertical-align: middle;*/
}

/* Fr den Tabellen Tag, allgemeine Tabllen Einstellungen */
.headline {
	border: 4px #021A4A solid;
	/*vertical-align: middle;*/
	margin-left: auto;
	margin-right: auto;
	-moz-border-radius: 11px;
}

.mainline
{
	border: 2px #021A4A solid;

}

/* Die Überschriften Zeile fr Tabellen */
TH.thHead {
	font-weight: bold;
	font-size: 12px;
	height: 25px;
	background-image: url('images/cell1l.jpg');

}

/* Der Seitentitel */
h1 {
	font-family: \"Trebuchet MS\", Verdana, Arial, Helvetica, sans-serif;
	font-size: 20px;
	font-weight: bold;
	text-decoration: none;
	line-height: 120%;
	color : #000000;
}

/* In der Beitragsansicht, wenn die Beiträge auf mehrere Seiten verteilt werden,
 * werden die Links mit dieser Klasse formatiert */
.nav {
	font-size: 11px;
	font-weight: bold;
	text-decoration: none;
	color: #000000;
}
a.nav {
	text-decoration: none;
	color: #043698;
}
a.nav:visited {
	text-decoration: none; color: #003090;
}
a.nav:active {
	text-decoration: underline; color: #FF0000;
}
a.nav:hover {
	text-decoration: underline;
	color: #A00000;
}

/* Fr die Links Titel <> Neuen Eintrag schreiben ...
   Wurde in Version Alpha 1 benutzt, kann weq eigentlich
*/
.nav_bar
{
    font-weight:bold;
}
a.nav_bar
{
    color: #043698;
	text-decoration: none;
}
a.nav_bar:hover
{
    color: #A00000;
	text-decoration: underline;
}

/* Fr Kommentare in der Beitragsansicht */
.comment
{
	font-size: 10px;
	text-decoration: none;
	color: #001030;
}

/* Form elements */
input,textarea, select
{
	color : #000000;
	font: normal 11px Verdana, Arial, Helvetica, sans-serif;
	border-color : #000000;
}

/* The text input fields background colour */
input.post, textarea.post, select
{
	background-color : #FFFFFF;
}

input {
	text-indent : 2px;
 }

/* The buttons used for bbCode styling in message post */
input.button
{
	background-color : #EFEFEF;
	color : #000000;
	font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif;
}

/* The main submit button option */
input.mainoption
{
	background-color : #FAFAFA;
	font-weight : bold;
}

/* None-bold submit button */
input.liteoption
{
	background-color : #FAFAFA;
	font-weight : normal;
}

/* Grafiken haben bei uns keinen Rand :P */
.image
{
	border: 0px none #000000;
}

input
{
	color: #000000;
	background-color: #ffffff;
	border: 1px solid #000000;
}

textarea
{
	color: #000000;
	background-color: #ffffff;
	border: 1px solid #000000;
}
</style>
</head>";
echo "<body>";
echo "<h2>qGuestbook Installation</h2>";

if (!isset($_POST['install'])) {
	echo "<p>Dieses Script installiert qGuestbook auf ihrem Server. Bitte stellen sie sicher, dass sie der Datei config.php im Verzeichniss includes/config Schreibrechte (Chmod 777) gegeben haben.<br />Ist dies nicht moeglich, muessen sie die Datei von Hand anpassen.</p>";

	echo "<h3>Pruefe Server Vorraussetzungen</h3>";
	echo "<ul>";
	echo "  <li>PHP Version: <b>Ok</b> (" . phpversion() . ")</li>";
	echo "  <li>PHP GD Extension: " . ((isset($support['gd'])) ? '<b>Ok</b> ('.$support['gd'].')' : '<b>Nicht verfuegbar</b>, GD Funktionen werden deaktivert!') . "</li>";
	echo "  <li>MySQL Support: " . ((isset($support['mysql'])) ? '<b>Ok</b> ('.$support['mysql'].')' : '<b>Nicht verfuegbar</b>') . "</li>";
	echo "  <li>SQLite Support: " . ((isset($support['sqlite'])) ? '<b>Ok</b> ('.$support['sqlite'].')' : '<b>Nicht verfuegbar</b>') . "</li>";
	echo "</ul>";
	
	echo "<h3>Grundkonfiguration</h3>";
	echo "<form action=\"install.php?install=true\" method=\"post\">";

	echo "<table border=\"0\" style=\"border: 1px solid #000;\">";
	echo "<tr>
		<td><h3>Datenbank Einstellungen</h3></td>
		<td>&nbsp;</td>
		<td><h3>Administratoren Konto</h3></td>
		<td>&nbsp;</td>
	</tr>";
	echo "<tr>
	<td><b>Datenbank Host:</b></td>
	<td>&nbsp;<input type=\"text\" name=\"dbhost\" value=\"localhost\" />&nbsp;</td>
	<td><b>Benutzername:</b></td>
	<td>&nbsp;<input type=\"text\" name=\"username\" />&nbsp;</td>
	</tr>";
	echo "<tr>
	<td><b>Datenbank Benutzer:</b></td>
	<td>&nbsp;<input type=\"text\" name=\"dbuser\" />&nbsp;</td>
	<td><b>E-Mail Adresse:</b></td><td>&nbsp;<input type=\"text\" name=\"email\" />&nbsp;</td>
	</tr>";
	echo "<tr>
	<td><b>Datenbank Passwort:</b></td>
	<td>&nbsp;<input type=\"password\" name=\"dbpasswd\" />&nbsp;</td>
	<td><b>Passwort:</b></td><td>&nbsp;<input type=\"password\" name=\"user_passwd\" />&nbsp;</td>
	</tr>";
	echo "<tr>
	<td><b>Datenbank Name:</b></td>
	<td>&nbsp;<input type=\"text\" name=\"dbname\" />&nbsp;</td>
	<td><b>Passwort wiederholen:</b></td><td>&nbsp;<input type=\"password\" name=\"user_passwd2\" />&nbsp;</td>
	</tr>";
	echo "<tr>
		<td><b>Tabellen Prefix:</b></td>
		<td>&nbsp;<input type=\"text\" name=\"table_prefix\" value=\"gbook_\" />&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>";
	echo "<tr>
		<td><br /><h3>Sonstige Einstellungen</h3></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>";
	echo "<tr>
		<td><b>Scriptpfad:</b></td>
		<td>&nbsp;<input type=\"text\" name=\"script_path\" value=\"/pfad/zu/qGuestbook\" />&nbsp;</td>
	</tr>";
	echo "<tr>
		<td><br /><br /><input type=\"submit\" name=\"install\" value=\"Installation starten\" style=\"width: 100%;\" /></td>
	</tr>";
	echo "</table>";
	echo "<p>Bitte pruefen sie alle Eingaben und klicken sie anschliessend auf den Button &quot;Installation starten&quot;.</p>";
	echo "</form>";

} else {
	
	$check_fields = array(
		'dbhost', 'dbuser', 'dbpasswd', 'dbname', 'table_prefix',
		'username', 'email', 'user_passwd', 'user_passwd2', 'script_path',
	);

	foreach ($check_fields as $index) {
		if (!isset($_POST[$index]) || empty($_POST[$index])) {
			die("Das Feld $index wurde nicht ausgefuellt!<br />Bitte ueberpruefen Sie ihre Eingaben!");
			exit;
		}
	}

	if ($_POST['user_passwd'] != $_POST['user_passwd2']) {
		die('<p><b>Fehler:</b> Die Verifizierung ihres Passworts ist nicht korrekt!</p>');
	}
	
	echo "<h2>Installiere</h2>";

	/**
	 * Connect to DB
	 */
	echo "<h3>Verbinde zur Datenbank...</h3>";
	require_once "{$root_dir}includes/database/dbms/mysql.php";
	$db = new $database_class($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpasswd'], $_POST['dbname']);
	echo "<p>Verbindungstest erfolgreich!</p>";
	
	$sql_querys = $db->sql_split_dump("dbms/$dbtype/install.sql");
	
	/**
	 * Execute SQL Querys
	 */
	echo "<h3>Importiere Datenbank...</h3>";
	echo '<textarea name="user_eingabe" cols="100" rows="10">';
	foreach ($sql_querys as $key => $value) {
		if ($_POST['table_prefix'] != 'gbook_') {
			$value = str_replace('gbook_', $db->sql_escape($_POST['table_prefix']), $value);
		}
		if (!$db->sql_query($value)) {
			$error = $db->sql_error();
			echo "# Fehlgeschlagen!\n# Error ". $error['code'] . ": " . $error['error'] . "\n";
		} else {
			echo "# Erfolgreich!\n";
		}
		echo str_replace("\n", "", $value)."\n\n";
	}
	echo '</textarea>';
	echo "<p>Datenbank erfolgreich importiert!</p>";
	
	/**
	 * Update default config
	 */
	echo "<h3>Update Konfiguration...</h3>";
	
	$config = array(
		'startdate' => time(),
		'version' => $qgb_version,
		'email_admin' => $_POST['email'],
		'script_path' => $_POST['script_path'],
	);
	
	$db->sql_query('TRUNCATE TABLE ' . $db->sql_escape($_POST['table_prefix'], false) . 'user');
	if (!$db->sql_query("INSERT INTO ". $db->sql_escape($_POST['table_prefix'], false) . "user 
			(`user_id` , `user_name`, `user_pass`, `user_email`, `user_session`, `user_time`, `user_ip`, `user_level`)
			VALUES (".$db->sql_escape(1)." , ".$db->sql_escape($_POST['username']).", ".$db->sql_escape(md5($_POST['user_passwd'])).", ".$db->sql_escape($_POST['email']).", '', '', '', ".$db->sql_escape(3).")")) {
		die(var_dump($db->sql_error()));
	}
	echo "<p>Administrator erfolgreich angelegt!</p>";
	
	foreach ($config as $key => $value) {
		$sql = 'UPDATE ' . $db->sql_escape($_POST['table_prefix'], false) . 'config
			SET config_value = ' . $db->sql_escape($value) . '
				WHERE config_name = ' . $db->sql_escape($key) . '
			LIMIT 1';
		if (!$db->sql_query($sql)) {
			$failed = (isset($failed)) ? $failed++ : 1;
			$failed_fields[] = $key;
		}
	}
	if (isset($failed)) {
		echo "<p>$failed Konfigurations-Felder konnten nicht geupdatet werden!</p>";
	} else {
		echo "<p>Konfiguration erfolgreich in die Datenbank geschrieben!</p>";
	}
	
	/**
	 * Write config.php
	 */
	echo "<h3>Schreibe Konfigurationsdatei...</h3>";
	$config_sample = $root_dir.'includes/config/sample.php';
	$config_file =  $root_dir.'includes/config/config.php';
	if (!is_file($config_sample) && is_readable($config_sample)) {
		echo "<p>Beispiel Konfigurationsdatei konnte nicht gefunden werden!</p>";
	} else {
		$config_data = sprintf(file_get_contents($config_sample), 'mysql', $_POST['dbhost'], $_POST['dbuser'], $_POST['dbpasswd'], $_POST['dbname'], $_POST['table_prefix']);
		if (!file_put_contents($config_file, $config_data)) {
			echo "<p>Konfigurationsdatei konnte nicht geschrieben werden!<br />Sie muessen die Datei nun von Hand bearbeiten bzw. anlegen:</p>";
				echo '<textarea name="user_eingabe" cols="100" rows="10">'.$config_data.'</textarea>';
		} else {
			echo "<p>Konfigurationsdatei erfolgreich geschrieben!</p>";
		}
	}
	
	echo "<h3><font color=\"red\">Die Installation ist abgeschlossen!</font></h3>";
	echo "<p>Bitte entferne nun das Verzeichniss install/ von deinem Webserver</p>";
}

echo "</body>";
echo "</html>";

?>