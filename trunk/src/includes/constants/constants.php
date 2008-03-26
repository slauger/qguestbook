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
* @subpackage constants
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    0.2.4
* @link       http://www.simlau.net/
*/

if (!defined('GUESTBOOK')) {
    die("Hacking attempt");
    exit;
}

//
// Wichtig, auf keinen Fall aendern!
// Wird in den kommenden Releases noch enorm wichtig!
//
define('USER_ANONYMOUS', 0);
define('USER_REGISTERED', 1);
define('USER_MODERATOR', 2);
define('USER_ADMINISTRATOR', 3);

// Fix für einen kleinen Schreibfehler
// In der Version 0.2.2, wir bleiben kompatibel
define('USER_REGISTRED', USER_REGISTERED);

//
// Kann ggf. angepasst werden,
// definiert die Farben für die Ränge
//
define('COLOR_ANONYMOUS', '#000000');
define('COLOR_REGISTRED', '#000000');
define('COLOR_MODERATOR', '#008000');
define('COLOR_ADMINISTRATOR', '#b22222');

//
// SQL-Tabellen
//
define('CONFIG_TABLE', $table_prefix.'config');
define('LANGUAGE_TABLE', $table_prefix.'lang');
define('STYLES_TABLE', $table_prefix.'styles');
define('DISSALOW_TABLE', $table_prefix.'dissalow');
define('BANLIST_TABLE', $table_prefix.'banlist');
define('POSTS_TABLE', $table_prefix.'posts');
define('USERS_TABLE', $table_prefix.'user');
define('ABUSE_TABLE', $table_prefix.'abuse');
define('SMILIES_TABLE', $table_prefix.'smilies');
define('META_TABLE', $table_prefix.'meta');
define('WORDS_TABLE', $table_prefix.'words');
define('COMMENTS_TABLE', $table_prefix.'comments');

//
// Administrations Seiten
//
define('PAGE_ADMIN_MODERATE', $root_dir.'admin/moderate.php');
define('PAGE_ADMIN_USERS', $root_dir.'admin/users.php');
define('PAGE_ADMIN_DATABASE', $root_dir.'admin/database.php');
define('PAGE_ADMIN_SETTINGS', $root_dir.'admin/settings.php');
define('PAGE_ADMIN_SMILIES', $root_dir.'admin/smilies.php');
define('PAGE_ADMIN_EMAIL', $root_dir.'admin/email.php');
define('PAGE_ADMIN_INDEX', $root_dir.'admin/index.php');
define('PAGE_ADMIN_META', $root_dir.'admin/meta.php');
define('PAGE_ADMIN_LOGIN', $root_dir.'admin/login.php');
define('PAGE_ADMIN_LOGOUT', $root_dir.'admin/login.php?logout');

// Login Seiten; Fuer Kompatibilitaet mit Versionen vor 0.2.1
define('PAGE_LOGIN', $root_dir.'admin/login.php');
define('PAGE_LOGOUT', $root_dir.'admin/login.php?logout');

// Standart Seiten
define('PAGE_INDEX', $root_dir . 'index.php');
define('PAGE_POSTING', $root_dir . 'posting.php');
define('PAGE_ABUSE', $root_dir . 'abuse.php');
define('PAGE_NEWSFEED', $root_dir . $config_table['script_url'].'newsfeed.php');

// Status des Eintrags
define('POST_INACTIVE', 0);
define('POST_ACTIVE', 1);
define('POST_WAIT_LIST', 2);

// Als Abuse markiert?
define('POST_NOT_MARKED', 0);
define('POST_MARKED', 1);

?>