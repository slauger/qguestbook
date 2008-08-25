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
* @subpackage language
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

/**
 * @ignore
 */
if (!defined('GUESTBOOK')) {
	die('Hacking attempt');
	exit;
}

if (empty($lang) || !is_array($lang)) {
	$lang = array();
}

$lang = array_merge($lang, array(
	// Wichtige Standartsacgeb
	'USER_LANG' => 'de',
	'DIRECTION' => 'ltr',
	'CHARSET' => 'utf-8',
	'TRANSLATION_INFO' => '',
	'GUESTBOOK' => 'Gästebuch',
	'VIEWS' => 'Aufrufe',
	'POST' => 'Eintrag',
	'PAGE' => 'Seite',
	'PAGES' => 'Seiten',
	'POSTED' => 'Verfasst am',
	'MESSAGE' => 'Nachricht',
	'NAME' => 'Name',
	'AUTHOR' => 'Autor',
	'WWW' => 'Webseite',
	'ICQ' => 'ICQ Nummer',
	'EMAIL' => 'E-Mail',
	'BBCODE' => 'BBCode',
	'HTML' => 'HTML',
	'SMILIES' => 'Smilies',
	'ACTIVE' => 'Aktiviert',
	'INACTIVE' => 'Deaktiviert',
	'PASSWORD' => 'Passwort',
	'IMAGE' => 'Grafik',

	// Fehlerverarbeitung
	'ERROR' => 'Fehler',
	'ERROR_MAIN' => 'Bei der Verarbeitung traten folgende Fehler auf.',
	'SQL_ERROR_EXPLAIN' => 'SQL Error %1s: %2s<br /><br />In Datei %3s, Zeile %4s',
	
	// Dinge aus index.php
	'GUESTBOOK_ENTRY' => 'Vorhandene Gästebucheinträge',
	'GUESTBOOK_EMPTY' => 'Es befinden sich zurzeit keine Eintraege im Gästebuch oder es wurden noch keine von den Moderatoren freigeschaltet.<br /><br />Um selbst einen neuen Eintrag zu verfassen, klicke bitte %1shier%2s.<br /><br />Ansonsten besuche uns einfach in kürze wieder, vielen Dank.',
	'SHOW_FROM_TO' => '%s Einträge, %s bis %s, von gesamt %s Einträgen werden angezeigt.',
	'PAGE_OF' => 'Seite <strong>%1$d</strong> von <strong>%2$d</strong>',
	'POSTS_COUNT' => '<strong>%1$d</strong> Einträge gesamt.',
	'WRITE_NEW' => 'Einen neuen Eintrag verfassen',

	// Dinge aus posting.php
	'BACK_TO_GUESTBOOK' => 'Zurück zum Gästebuch',
	'POSTING_WRITE_NEW' => 'Neuen Eintrag schreiben',
	'POSTING_HIDE_EMAIL' => 'E-Mail verstecken',
	'POSTING_HIDE_EMAIL_YES' => 'Ja, meine E-Mail Adresse verstecken',
	'POSTING_NO_TEXT' => 'Du hast keinen Text zu deinem Eintrag angegeben.',
	'POSTING_NO_NAME' => 'Du hast keinen Benutzernamen zu deinem Eintrag angegeben',
	'POSTING_NO_INVALID' => 'Du hast keine valide E-Mail Adresse zu deinem Eintrag angegeben.',
	'POSTING_ICQ_INVALID' => 'Deine angegebene ICQ-Nummer ist nicht valid.',
	'POSTING_WWW_INVALID' => 'Deine angegebene Webseite ist nicht valid.',
	
	// Bann Modul
	'ERROR_BANNED_IP' => 'Deine IP Adresse wurde vom Administrator gesperrt.',
	'ERROR_BANNED_USER' => 'Der gewählte Benutzername wurde vom Administrator gesperrt oder ist bereits belegt.',

	// Login
	'LOGIN_ERROR_USERNAME' => 'Du hast einen fehlerhaften Benutzernamen angegeben. Bitte prüfe deinen Benutzernamen und versuche es erneut.',
	
	// RSS Feed
	'RSS_TITLE' => 'Gästebucheintrag von %s',

	// Captcha
	'CAPTCHA_TITLE' => 'Spamschutz',
	'CAPTCHA_QUESTION' => 'Was ergibt %1s und %2s?',
));

// Include old System...
// No longer used!
include_once 'old_system.php';

?>