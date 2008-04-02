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
	'AUTHOR' => 'Autor',
	'WWW' => 'Webseite',
	'ICQ' => 'ICQ',
	'EMAIL' => 'E-Mail',
	'BBCODE' => 'BBCode',
	'SMILIES' => 'Smilies',
	'ACTIVE' => 'aktiv',
	'INACTIVE' => 'inaktiv',
	'PASSWORD' => 'Passwort',
	'IMAGE' => 'Grafik',

	'ERROR' => 'Fehler',
	'ERROR_MAIN' => 'Bei der Verarbeitung traten folgende Fehler auf.',
	
	'GUESTBOOK_ENTRY' => 'Vorhandene Gästebucheinträge',
	'GUESTBOOK_EMPTY' => 'Es befinden sich zurzeit keine Eintraege im Gästebuch oder es wurden noch keine von den Moderatoren freigeschaltet.<br /><br />Um selbst einen neuen Eintrag zu verfassen, klicke bitte %1shier%2s.<br /><br />Ansonsten besuche uns einfach in kürze wieder, vielen Dank.',
	'SHOW_FROM_TO' => '%s Einträge, %s bis %s, von gesamt %s Einträgen werden angezeigt.',
	
	'LOGIN_ERROR_USERNAME' => 'Du hast einen fehlerhaften Benutzernamen angegeben. Bitte prüfe deinen Benutzernamen und versuche es erneut.',

	'PAGE_OF' => 'Seite <strong>%1$d</strong> von <strong>%2$d</strong>',
	'POSTS_COUNT' => '<strong>%1$d</strong> Einträge gesamt.',
	'WRITE_NEW' => 'Einen neuen Eintrag verfassen',

	'POSTING_NO_TEXT' => 'Du hast keinen Text zu deinem Eintrag angegeben.',
	'POSTING_NO_NAME' => 'Du hast keinen Benutzernamen zu deinem Eintrag angegeben',
	'POSTING_NO_INVALID' => 'Du hast keine valide E-Mail Adresse zu deinem Eintrag angegeben.',
	'POSTING_ICQ_INVALID' => 'Deine angegebene ICQ-Nummer ist nicht valid.',
	'POSTING_WWW_INVALID' => 'Deine angegebene Webseite ist nicht valid.',

	'ERROR_BANNED_IP' => 'Deine IP Adresse wurde vom Administrator gesperrt.',
	'ERROR_BANNED_USER' => 'Der gewählte Benutzername wurde vom Administrator gesperrt oder ist bereits belegt.',
	
	'SQL_ERROR_EXPLAIN' => 'SQL Error %1s: %2s<br /><br />In Datei %3s, Zeile %4s',
));

/**
* AB HIER FOLGT DAS ALTE SYSTEM.
* DIESES IST NUR NOCH FÜR DIE RÜCKWÄRTSKOMPATIBILITÄT VORHANDEN.
* BITTE NICHT MEHR VERWENDEN! NUR NOCH GROSSBUCHSTABEN VERWENDEN!
* ALTERNATIV: EIN WORKAROUND MIT STRTOUPPER();
*/

/**
  * Informationen für die HTML Ausgabe
  */
$lang["xml_lang"] = "de";
$lang["lang"] = "de";
$lang["dir"] = "ltr";
$lang["charset"] = "utf-8";

/**
 * Übersetzer (optional)
 */
$lang["translation_info"] = "";

/**
 * Meist genutzt Wörter
 */
$lang["guestbook"] = "Gästebuch";
$lang["views"] = "Aufrufe";
$lang["post"] = "Beitrag";
$lang["posts"] = "Beiträge";
$lang["page"] = "Seite";
$lang["pages"] = "Seiten";
$lang["posted"] = "Verfasst am";
$lang["username"] = "Benutzername";
$lang["password"] = "Passwort";
$lang["email"] = "E-Mail";
$lang["poster"] = "Poster";
$lang["author"] = "Autor";
$lang["time"] = "Zeit";
$lang["hours"] = "Stunden";
$lang["message"] = "Nachricht";
$lang["day"] = "Tag";
$lang["days"] = "Tagen";
$lang["smilies"] = "Smilies";
$lang["bbcode"] = "BBCode";
$lang["bbcodes"] = "BBCodes";
$lang["active"] = "Aktiviert";
$lang["inactive"] = "Deaktivert";

$lang["back"] = "Zurück";

//
// Messenger AddOns
//
$lang["icq"] = "ICQ-Nummer";
$lang["aim"] = "AIM-Name";
$lang["msn"] = "MSN Messenger";
$lang["yim"] = "Yahoo Messenger";

$lang["guestbook_error"] = "Bei der Verarbeitung traten folgende Fehler auf";
$lang["guestbook_error_desc"] = $lang["guestbook_error"]; // rückwärts kombatibilität
$lang["guestbook_empty"] = "Es befinden sich zurzeit keine Eintraege im Gästebuch oder es wurden noch keine von den Moderatoren freigeschaltet.<br /><br />Um selbst einen neuen Eintrag zu verfassen, klicke bitte %1shier%2s.<br /><br />Ansonsten besuche uns einfach in kürze wieder, vielen Dank.";

$lang["guestbook_posts_count"] = "%1s Einträge im Gästebuch."; // %s replaced by posts count
$lang["site_from_sites"] = "Seite %1s von %2s."; // e.g. site 1 from 100

$lang["guestbook_write_new"] = "Einen neuen Eintrag verfassen";

// index.php
$lang["posts_view_all"] = "Vorhandene Gästebucheinträge";

// posting.php
$lang["posting_no_text"] = "Du musst einen Text zu deinem Eintrag angeben!";
$lang["posting_no_name"] = "Du musst deinen Namen zu deinem Eintrag angeben!";
$lang["posting_www_not_valid"] = "Deine Webseite hat kein valides Format!<br /><br />%1sHier klicken%2s, um es noch einmal zu versuchen.<br /><br />%1sHier klicken%2s, um zum Gästebuch zurückzukehren.";
$lang["posting_icq_not_valid"] = "Deine ICQ Nummer hat kein valides Format!<br /><br />%1sHier klicken%2s, um es noch einmal zu versuchen.<br /><br />%1sHier klicken%2s, um zum Gästebuch zurückzukehren.";
$lang["posting_email_not_valid"] = "Du musst eine valide E-Mail Adresse angeben!";
$lang["posting_success"] = "Dein Eintrag wurde erfolgreich eingetragen";
$lang["posting_success_text"] = "%1sDein Eintrag wurde erfolgreich ins Gästebuch eingetragen!<br /><br />Klicke %2shier%3s um zum Gästebuch zurückzukehren.<br /><br />Klicke %4shier%5s um einen weiteren Eintrag zu verfassen.";

// old stuff ^^
$lang["click_return_login"] = "%1sHier klicken%2s, um es noch einmal zu versuchen";
$lang["click_return_guestbook"] = "%1sHier klicken%2s, um zum Gästebuch zurückzukehren";
$lang["click_view_message"] = "%1sHier klicken%2s, um deine Nachricht anzuzeigen";
$lang["click_return_modcp"] = "%1sHier klicken%2s, um zur Moderatorenkontrolle zurückzukehren";

$lang["user_wrote"] = "%1s hat folgendes geschrieben:"; // %s replaced by username

$lang["current_time"] = "Aktuelles Datum und Uhrzeit: %1s"; // %s replaced by time

//
// Gästebuch: Email Versand
//
$lang["email_post_user"] = "Dein Eintrag im Gästebuch";
$lang["email_post_admin"] = "Neuer Eintrag im Gästebuch";

// login.php
$lang["login_failed"] = "Dein eingegebenes Passwort ist nicht korrekt, oder der gewählte Benutzer existiert nicht.<br /><br />Klicke %1shier%2s, um zum Login zurückzukehren.<br /><br />Klicke %1shier%2s, um zum Gästebuch zurückzukehren.";
$lang["login_success"] = "Du wurdest erfolgreich eingeloggt";
$lang["login_success_desc"] = "Du wurdest erfolgreich eingeloggt.<br /><br />Klicke %1shier%2s, um zum Moderatorenbereich zu gelagen.<br /><br />Klicke %1shier%2s, um zum Gästebuch zurückzukehren.";

// login.php?logout
$lang["logout_failed"] = "Um dich einloggen zu können musst du dich zuerst einloggen.<br /><br />Klicke %1shier%2s, um zur Login Seite zu gelangen.<br /><br />Klicke %1shier%2s, um zum Gästebuch zurückzukehren.";
$lang["logout_success"] = "Du wurdest erfolgreich ausgeloggt";
$lang["logout_success_desc"] = "Du wurdest erfolgreich ausgeloggt.<br /><br />Klicke %1shier%2s, um dich erneut einzuloggen.<br /><br />Klicke %1shier%2s, um zum Gästebuch zurückzukehren.";

// admin/*
$lang["admin_welcome"] = "Willommen im Moderatorenbereich!";
$lang["admin_welcome_long"] = "Du befindest dich jetzt im Moderatorenbereich. Hier kannst du Einstellungen treffen, Beiträge moderieren, Moderatoren verwalten und hast eine kleine Übersicht über die alle aktuellen Informationen zu deinem Gästebuch.";

// Statistik
$lang["switchted_on"] = "Angeschaltet";
$lang["switchted_off"] = "Ausgeschaltet";
$lang["posts_count_desc"] = "Einträge insgesamt:";
$lang["waitlist_desc"] = "Einträge in der Warteliste:";
$lang["installed_desc"] = "Gästebuch installiert vor";
$lang["statistic_infos_title"] = "Aktuelle Statistik";
$lang["statistic_infos_desc"] = "Hier findest du die aktuelle Statistik des Gaestebuchs.";
$lang["security_infos_title"] = "Sicherheits Informationen";
$lang["security_infos_desc"] = "Hier findest du aktuelle Informationen über die Sicherheit deines Gästebuchs";

// Smilies
$lang["smilies_export"] = "Smilie Paket erstellen";
$lang["smilies_export_desc"] = "Hier kannst du deine installierten Smilies in ein Smilie Paket exportieren";
$lang["smilies_export_submit"] = "Smilie Paket wurde erfolgreich exportiert";
$lang["smilies_export_submit_desc"] = "Das Smilie Paket wurde erfolgreich in die Datei %1s geschrieben.<br /><br />Klicke %2shier%3s, um zur Smilie Administration zurück zukehren.<br /><br />Klicke %4shier%5s um zur Moderatoren Startseite zurückzukehren.";
$lang["smilies_export_file"] = "Die Datei %1s ist nicht beschreibbar!<br /><br />Klicke %2shier%3s, um zur Smilie Administration zurück zukehren.<br /><br />Klicke %4shier%5s um zur Moderatoren Startseite zurückzukehren.";

// Konfiguration
$lang['settings_edited'] = 'Gästebuchkonfiguration geändert';
$lang['settings_failed'] = 'Die Gästebuchkonfiguration wurde geändert, jedoch ließen sich einige Konfigurationswerte nicht ändern.<br />Diese Konfigurationswerte blieben daher unverändert.Klicke %shier%s um zu Konfiguration des Gästebuchs zurückzukehren.<br /><br />Klicke %shier%s um zur Moderatoren Startseite zurückzukehren.';
$lang['settings_success'] = 'Die Gästebuchkonfiguration wurde geändert.<br /><br />Klicke %shier%s um zu Konfiguration des Gästebuchs zurückzukehren.<br /><br />Klicke %shier%s um zur Moderatoren Startseite zurückzukehren.';

?>