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
* @version    0.2.4
* @link       http://www.simlau.net/
*/

// Template laden
// file_get_contents() mit ein paar anpassungen :D
function load_email()
{
	die('function not ready yet!');
}

// Email versenden
// btw. ne neue Version von generate_email() ;)
function send_email($adresses, $subject, $text)
{
	global $config_table;

	$mail = new htmlMimeMail5();
	$mail->setFrom($config_table['email_admin']);
	$mail->setSubject($subject);
	$mail->setPriority('normal');
	$mail->setText($text);

	$email_mode = get_email_mode();
	if ($email_mode == 'smtp')
	{
		$mail->setSMTPParams($config_table['smtp_server'], $config_table['smtp_port'], $config_table['smtp_helo'], $config_table['smtp_auth'], $config_table['smtp_user'], $config_table['smtp_pass']);
	}
	else if ($email_mode == 'sendmail')
	{
		$email->setSendmailPath($config_table['sendmail']);
	}
	$mail->send($send_to, $email_mode);
}

// Formatiert eine Email Adresse...
function format_email_adress($username, $email)
{
	$formated = sprintf('%s <%s>', $username, $email);
	return $formated;
}

// alte Version... mit unschönen HTML Features ;)
function generate_mail($subject, $send_to, $text)
{
	global $config_table;
	$mail = new htmlMimeMail5();
	$mail->setFrom($config_table['email_admin']);
	$mail->setSubject($subject);
	$mail->setPriority('normal');
	if ($config_table['email_html'] == 1)
	{
		$mail->setHTML(sprintf( "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"" .
       					"http://www.w3.org/TR/html4/strict.dtd\">" .
					"<html>\n" .
					"  <head>\n" .
					"    <title>%s</title>\n" .
					"  </head>\n" .
					"  <body>\n" .
					"    <h3>%s</h3>\n" .
					"      <p>\n" .
					"        %s</a>\n" .
					"     </p>\n" .
					"   </body>\n" .
					"</html>\n", bbcode($subject), bbcode($subject), bbcode($text)));
	// Grafiken anhängen
	//$mail->addEmbeddedImage(new fileEmbeddedImage('background.gif', 'image/gif', new Base64Encoding()));
	// Dateien anhängen
	// $mail->addAttachment(new fileAttachment('example.zip', 'application/zip', new Base64Encoding()));
	}
	else
	{
			$mail->setText($text);
	}

	if (get_email_mode() == 'smtp')
	{
		$mail->setSMTPParams($config_table['smtp_server'], $config_table['smtp_port'], $config_table['smtp_helo'], $config_table['smtp_auth'], $config_table['smtp_user'], $config_table['smtp_pass']);
	}
	if (get_email_mode() == 'sendmail')
	{
		$email->setSendmailPath($config_table['sendmail']);
	}
	$mail->send($send_to, get_email_mode());
}

function get_email_mode()
{
	global $config_table;
	switch ($config_table['email_mode'])
	{
		case 1:
			return 'mail';
		break;
		case 2:
			return 'sendmail';
		break;
		case 3:
			return 'smtp';
		break;
	}
}

?>