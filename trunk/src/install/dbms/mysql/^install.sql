-- phpMyAdmin SQL Dump
-- version 2.10.3deb1ubuntu0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 15. März 2008 um 13:27
-- Server Version: 5.0.45
-- PHP-Version: 5.2.3-1ubuntu6.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Datenbank: `qGuestbook`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `gbook_abuse`
-- 

CREATE TABLE IF NOT EXISTS `gbook_abuse` (
  `abuse_id` int(11) NOT NULL auto_increment,
  `abuse_post` varchar(55) NOT NULL,
  `abuse_date` varchar(55) NOT NULL,
  `abuse_ip` varchar(55) NOT NULL,
  PRIMARY KEY  (`abuse_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Daten für Tabelle `gbook_abuse`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `gbook_banlist`
-- 

CREATE TABLE IF NOT EXISTS `gbook_banlist` (
  `banlist_id` int(11) NOT NULL auto_increment,
  `banlist_email` varchar(255) NOT NULL default '',
  `banlist_ip` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`banlist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `gbook_banlist`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `gbook_comments`
-- 

CREATE TABLE IF NOT EXISTS `gbook_comments` (
  `comment_id` int(11) NOT NULL auto_increment,
  `comment_post` varchar(55) NOT NULL,
  `comment_user` varchar(55) NOT NULL,
  `comment_text` varchar(55) NOT NULL,
  `comment_date` varchar(55) NOT NULL,
  PRIMARY KEY  (`comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `gbook_comments`
-- 

INSERT INTO `gbook_comments` (`comment_id`, `comment_post`, `comment_user`, `comment_text`, `comment_date`) VALUES 
(1, '1', '1', 'Ein Kommentar... ;)', '1205582434');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `gbook_config`
-- 

CREATE TABLE IF NOT EXISTS `gbook_config` (
  `config_name` varchar(255) NOT NULL default '',
  `config_value` text NOT NULL,
  PRIMARY KEY  (`config_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Daten für Tabelle `gbook_config`
-- 

INSERT INTO `gbook_config` (`config_name`, `config_value`) VALUES 
('default_lang', '1'),
('active', '1'),
('description', 'Das OpenSource GÃ¤stebuch'),
('sitename', 'qGuestbook'),
('page_title', 'Gästebuch'),
('debug_info', '1'),
('show_warnings', '1'),
('disable_msg', 'Das GÃ¤stebuch wurde vom Administrator gesperrt!\r\n\r\nBitte versuche es spÃ¤ter noch einmal.'),
('submit_msg', 'Vielen Dank für deinen Eintrag in unserem Gästebuch!'),
('enable_icq', '1'),
('enable_www', '1'),
('version', '0.2.1'),
('smilies_path', 'images/smiles/'),
('posts_site', '5'),
('admin_link', '1'),
('gzip', '0'),
('moderated', '1'),
('style', 'soscy'),
('posts_sort_new', '1'),
('default_dateformat', 'd.m.Y, H:i'),
('allow_mark_post', '1'),
('charset', 'UTF-8'),
('startdate', '1178009196'),
('max_lenght', '20'),
('success_email_text', 'Hallo %1s!\r\nVielen Dank fÃ¼r deinen Eintrag in unser GÃ¤stebuch.\r\nSchau doch einfach noch mal auf unserer Homepage vorbei. ;)\r\n\r\nWichtig: Deine IP Adresse wurde aus SicherheitsgrÃ¼nden gespeichert. Sie ist vom Administrator jederzeit einsehbar.'),
('email_admin', 'Betatester <admin@example.com'),
('email_mode', '3'),
('smtp_server', ''),
('smtp_port', '25'),
('smtp_helo', 'Hello, nice to meet you.'),
('smtp_auth', 'SMTP'),
('smtp_user', ''),
('smtp_pass', ''),
('email_html', '0'),
('script_path', '/qBook/'),
('success_email', '0'),
('success_email_admin', '0'),
('success_email_admin_text', 'Hallo lieber Moderator!\r\n\r\nDer Benutzer %1s hat sich soeben in dein GÃ¤stebuch eingetragen.\r\n\r\nEr hat folgendes geschrieben:\r\n\r\n---\r\n%2s\r\n---\r\n\r\nDein GÃ¤stebuch kannst du unter der folgenden Adresse erreichen: %3s'),
('sendmail', '/usr/sbin/sendmail -ti'),
('success_email_admin_all', '0'),
('bbcode', '1'),
('smilies', '1'),
('rss_limit', '10'),
('language', 'de'),
('password_length', '4'),
('newsfeed', '1'),
('https', '0'),
('censor_words', '1'),
('bar', 'bar');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `gbook_disallow`
-- 

CREATE TABLE IF NOT EXISTS `gbook_disallow` (
  `disallow_id` int(11) NOT NULL auto_increment,
  `disallow_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`disallow_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `gbook_disallow`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `gbook_meta`
-- 

CREATE TABLE IF NOT EXISTS `gbook_meta` (
  `meta_name` varchar(255) NOT NULL default '',
  `meta_content` text NOT NULL,
  PRIMARY KEY  (`meta_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Daten für Tabelle `gbook_meta`
-- 

INSERT INTO `gbook_meta` (`meta_name`, `meta_content`) VALUES 
('description', 'Das qGuestbook OpenSource Gaestebuch!'),
('keywords', 'GÃ¤stebuch, und so');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `gbook_posts`
-- 

CREATE TABLE IF NOT EXISTS `gbook_posts` (
  `posts_id` int(11) NOT NULL auto_increment,
  `posts_name` varchar(55) NOT NULL,
  `posts_email` varchar(55) NOT NULL,
  `posts_ip` varchar(55) NOT NULL,
  `posts_www` varchar(55) NOT NULL,
  `posts_icq` varchar(55) NOT NULL,
  `posts_text` text NOT NULL,
  `posts_date` varchar(55) NOT NULL,
  `posts_active` varchar(1) NOT NULL default '1',
  `posts_hide_email` varchar(1) NOT NULL default '1',
  `posts_marked` varchar(1) NOT NULL default '0',
  `posts_comment` varchar(11) NOT NULL,
  PRIMARY KEY  (`posts_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- 
-- Daten für Tabelle `gbook_posts`
-- 

INSERT INTO `gbook_posts` (`posts_id`, `posts_name`, `posts_email`, `posts_ip`, `posts_www`, `posts_icq`, `posts_text`, `posts_date`, `posts_active`, `posts_hide_email`, `posts_marked`, `posts_comment`) VALUES 
(1, 'Simon', 'kwhark@gmail.com', '7f000001', 'http://blog.simlau.net/', '206092388', 'Wheee ;)', '1205579134', '1', '1', '0', '');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `gbook_smilies`
-- 

CREATE TABLE IF NOT EXISTS `gbook_smilies` (
  `smilies_id` smallint(5) unsigned NOT NULL auto_increment,
  `smilies_code` varchar(50) NOT NULL,
  `smilies_url` varchar(100) NOT NULL,
  `smilies_name` varchar(75) NOT NULL,
  PRIMARY KEY  (`smilies_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;

-- 
-- Daten für Tabelle `gbook_smilies`
-- 

INSERT INTO `gbook_smilies` (`smilies_id`, `smilies_code`, `smilies_url`, `smilies_name`) VALUES 
(1, ':D', 'icon_biggrin.gif', 'Very Happy'),
(2, ':-D', 'icon_biggrin.gif', 'Very Happy'),
(3, ':grin:', 'icon_biggrin.gif', 'Very Happy'),
(4, ':)', 'icon_smile.gif', 'Smile'),
(5, ':-)', 'icon_smile.gif', 'Smile'),
(6, ':smile:', 'icon_smile.gif', 'Smile'),
(7, ':(', 'icon_sad.gif', 'Sad'),
(8, ':-(', 'icon_sad.gif', 'Sad'),
(9, ':sad:', 'icon_sad.gif', 'Sad'),
(10, ':o', 'icon_surprised.gif', 'Surprised'),
(11, ':-o', 'icon_surprised.gif', 'Surprised'),
(12, ':eek:', 'icon_surprised.gif', 'Surprised'),
(13, ':shock:', 'icon_eek.gif', 'Shocked'),
(14, ':-/', 'icon_confused.gif', 'Confused'),
(15, ':-?', 'icon_confused.gif', 'Confused'),
(16, ':???:', 'icon_confused.gif', 'Confused'),
(17, '8)', 'icon_cool.gif', 'Cool'),
(18, '8-)', 'icon_cool.gif', 'Cool'),
(19, ':cool:', 'icon_cool.gif', 'Cool'),
(20, ':lol:', 'icon_lol.gif', 'Laughing'),
(21, ':x', 'icon_mad.gif', 'Mad'),
(22, ':-x', 'icon_mad.gif', 'Mad'),
(23, ':mad:', 'icon_mad.gif', 'Mad'),
(24, ':P', 'icon_razz.gif', 'Razz'),
(25, ':-P', 'icon_razz.gif', 'Razz'),
(26, ':razz:', 'icon_razz.gif', 'Razz'),
(27, ':redface:', 'icon_redface.gif', 'Embarassed'),
(28, ':cry:', 'icon_cry.gif', 'Crying or Very sad'),
(29, ':evil:', 'icon_evil.gif', 'Evil or Very Mad'),
(30, ':twisted:', 'icon_twisted.gif', 'Twisted Evil'),
(31, ':roll:', 'icon_rolleyes.gif', 'Rolling Eyes'),
(32, ':wink:', 'icon_wink.gif', 'Wink'),
(33, ';)', 'icon_wink.gif', 'Wink'),
(34, ';-)', 'icon_wink.gif', 'Wink'),
(35, ':!:', 'icon_exclaim.gif', 'Exclamation'),
(36, ':?:', 'icon_question.gif', 'Question'),
(37, ':idea:', 'icon_idea.gif', 'Idea'),
(38, ':arrow:', 'icon_arrow.gif', 'Arrow'),
(39, ':|', 'icon_neutral.gif', 'Neutral'),
(40, ':-|', 'icon_neutral.gif', 'Neutral'),
(41, ':neutral:', 'icon_neutral.gif', 'Neutral'),
(42, ':mrgreen:', 'icon_mrgreen.gif', 'Mr. Green');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `gbook_styles`
-- 

CREATE TABLE IF NOT EXISTS `gbook_styles` (
  `styles_id` int(11) NOT NULL auto_increment,
  `styles_name` varchar(50) NOT NULL default '',
  `styles_template` varchar(50) NOT NULL default '',
  `styles_theme` varchar(50) NOT NULL default '',
  `styles_imageset` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`styles_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `gbook_styles`
-- 

INSERT INTO `gbook_styles` (`styles_id`, `styles_name`, `styles_template`, `styles_theme`, `styles_imageset`) VALUES 
(1, 'soscy', 'soscy', 'soscy', 'soscy');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `gbook_user`
-- 

CREATE TABLE IF NOT EXISTS `gbook_user` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_name` varchar(50) NOT NULL default '',
  `user_pass` varchar(50) NOT NULL default '',
  `user_email` varchar(50) NOT NULL default '',
  `user_session` varchar(55) NOT NULL,
  `user_time` varchar(55) NOT NULL,
  `user_ip` varchar(55) NOT NULL,
  `user_level` varchar(1) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- 
-- Daten für Tabelle `gbook_user`
-- 

INSERT INTO `gbook_user` (`user_id`, `user_name`, `user_pass`, `user_email`, `user_session`, `user_time`, `user_ip`, `user_level`) VALUES 
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'kwhark@gmail.com', '', '1205581140', '7f000001', '3');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `gbook_votes`
-- 

CREATE TABLE IF NOT EXISTS `gbook_votes` (
  `votes_id` smallint(5) NOT NULL auto_increment,
  `votes_name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`votes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- 
-- Daten für Tabelle `gbook_votes`
-- 

INSERT INTO `gbook_votes` (`votes_id`, `votes_name`) VALUES 
(1, 'sehr gut'),
(2, 'gut'),
(3, 'befriedigend'),
(4, 'ausreichend'),
(5, 'mangelhaft'),
(6, 'ungenuegend');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `gbook_words`
-- 

CREATE TABLE IF NOT EXISTS `gbook_words` (
  `words_id` int(11) NOT NULL auto_increment,
  `words_name` varchar(255) NOT NULL default '',
  `words_replacement` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`words_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=179 ;

-- 
-- Daten für Tabelle `gbook_words`
-- 

INSERT INTO `gbook_words` (`words_id`, `words_name`, `words_replacement`) VALUES 
(1, '*damn', '******'),
(2, '*dyke', '******'),
(3, '*fuck*', '******'),
(4, '*shit*', '******'),
(5, '@$$', '******'),
(6, 'amcik', '******'),
(7, 'andskota', '******'),
(8, 'arschloch', '******'),
(9, 'arse*', '******'),
(10, 'asshole', '******'),
(11, 'assrammer', '******'),
(12, 'ayir', '******'),
(13, 'b!+ch', '******'),
(14, 'b!tch', '******'),
(15, 'b17ch', '******'),
(16, 'b1tch', '******'),
(17, 'bastard', '******'),
(18, 'bi+ch', '******'),
(19, 'bi7ch', '******'),
(20, 'bitch*', '******'),
(21, 'boiolas', '******'),
(22, 'bollock*', '******'),
(23, 'breasts', '******'),
(24, 'buceta', '******'),
(25, 'butt-pirate', '******'),
(26, 'c0ck', '******'),
(27, 'cabron', '******'),
(28, 'cawk', '******'),
(29, 'cazzo', '******'),
(30, 'chink', '******'),
(31, 'chraa', '******'),
(32, 'chuj', '******'),
(33, 'cipa', '******'),
(34, 'clits', '******'),
(35, 'Cock*', '******'),
(36, 'cum', '******'),
(37, 'cunt*', '******'),
(38, 'd4mn', '******'),
(39, 'daygo', '******'),
(40, 'dego', '******'),
(41, 'dick*', '******'),
(42, 'dike*', '******'),
(43, 'dildo', '******'),
(44, 'dirsa', '******'),
(45, 'dupa', '******'),
(46, 'dziwka', '******'),
(47, 'ejackulate', '******'),
(48, 'Ekrem*', '******'),
(49, 'Ekto', '******'),
(50, 'enculer', '******'),
(51, 'faen', '******'),
(52, 'fag*', '******'),
(53, 'fanculo', '******'),
(54, 'fanny', '******'),
(55, 'fatass', '******'),
(56, 'fcuk', '******'),
(57, 'feces', '******'),
(58, 'feg', '******'),
(59, 'Felcher', '******'),
(60, 'ficken', '******'),
(61, 'fitt*', '******'),
(62, 'Flikker', '******'),
(63, 'foreskin', '******'),
(64, 'Fotze', '******'),
(65, 'Fu(*', '******'),
(66, 'fuk*', '******'),
(67, 'futkretzn', '******'),
(68, 'fux0r', '******'),
(69, 'gay', '******'),
(70, 'gook', '******'),
(71, 'guiena', '******'),
(72, 'h0r', '******'),
(73, 'h4x0r', '******'),
(74, 'hell', '******'),
(75, 'helvete', '******'),
(76, 'hoer*', '******'),
(77, 'honkey', '******'),
(78, 'hore', '******'),
(79, 'Huevon', '******'),
(80, 'hui', '******'),
(81, 'injun', '******'),
(82, 'jism', '******'),
(83, 'jizz', '******'),
(84, 'kanker*', '******'),
(85, 'kawk', '******'),
(86, 'kike', '******'),
(87, 'klootzak', '******'),
(88, 'kraut', '******'),
(89, 'knulle', '******'),
(90, 'kuk', '******'),
(91, 'kuksuger', '******'),
(92, 'Kurac', '******'),
(93, 'kurwa', '******'),
(94, 'kusi*', '******'),
(95, 'kyrpa*', '******'),
(96, 'l3i+ch', '******'),
(97, 'l3itch', '******'),
(98, 'lesbian', '******'),
(99, 'lesbo', '******'),
(100, 'mamhoon', '******'),
(101, 'masturbat*', '******'),
(102, 'merd*', '******'),
(103, 'mibun', '******'),
(104, 'monkleigh', '******'),
(105, 'motherfucker', '******'),
(106, 'mofo', '******'),
(107, 'mouliewop', '******'),
(108, 'muie', '******'),
(109, 'mulkku', '******'),
(110, 'muschi', '******'),
(111, 'nazis', '******'),
(112, 'nepesaurio', '******'),
(113, 'nigga', '******'),
(114, 'nigger*', '******'),
(115, 'nutsack', '******'),
(116, 'orospu', '******'),
(117, 'paska*', '******'),
(118, 'perse', '******'),
(119, 'phuck', '******'),
(120, 'picka', '******'),
(121, 'pierdol*', '******'),
(122, 'pillu*', '******'),
(123, 'pimmel', '******'),
(124, 'pimpis', '******'),
(125, 'piss*', '******'),
(126, 'pizda', '******'),
(127, 'poontsee', '******'),
(128, 'poop', '******'),
(129, 'porn', '******'),
(130, 'p0rn', '******'),
(131, 'pr0n', '******'),
(132, 'preteen', '******'),
(133, 'pula', '******'),
(134, 'pule', '******'),
(135, 'pusse', '******'),
(136, 'pussy', '******'),
(137, 'puta', '******'),
(138, 'puto', '******'),
(139, 'qahbeh', '******'),
(140, 'queef*', '******'),
(141, 'rautenberg', '******'),
(142, 'schaffer', '******'),
(143, 'scheiss*', '******'),
(144, 'schlampe', '******'),
(145, 'schmuck', '******'),
(146, 'screw', '******'),
(147, 'scrotum', '******'),
(148, 'sh!t*', '******'),
(149, 'sharmuta', '******'),
(150, 'sharmute', '******'),
(151, 'shemale', '******'),
(152, 'shipal', '******'),
(153, 'shiz', '******'),
(154, 'skribz', '******'),
(155, 'skurwysyn', '******'),
(156, 'slut', '******'),
(157, 'smut', '******'),
(158, 'sphencter', '******'),
(159, 'spic', '******'),
(160, 'spierdalaj', '******'),
(161, 'splooge', '******'),
(162, 'suka', '******'),
(163, 'teets', '******'),
(164, 'b00b*', '******'),
(165, 'teez', '******'),
(166, 'testicle*', '******'),
(167, 'titt*', '******'),
(168, 'tits', '******'),
(169, 'twat', '******'),
(170, 'vittu', '******'),
(171, 'w00se', '******'),
(172, 'wank*', '******'),
(173, 'wetback*', '******'),
(174, 'whoar', '******'),
(175, 'wichser', '******'),
(176, 'wop*', '******'),
(177, 'yed', '******'),
(178, 'zabourah', '******');
