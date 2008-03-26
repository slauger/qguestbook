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
* @package    StringParser_BBCode
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    0.2.4
* @link       http://www.simlau.net/
*/

if (!defined('GUESTBOOK')) {
	die('Hacking attempt!');
}

if (!class_exists('StringParser_BBCode')) {
	die('Hacking attempt!');
}

$config_table['limit_images'] = (isset($config_table['limit_images'])) ? $config_table['limit_images'] : 2;

function convertlinebreaks ($text)
{
	return preg_replace ("/\015\012|\015|\012/", "\n", $text);
}

function bbcode_stripcontents ($text)
{
	return preg_replace ("/[^\n]/", '', $text);
}

function bbcode_url ($action, $attributes, $content, $params, $node_object)
{
	if ($action == 'validate')
	{
		return true;
	}

	if (!isset ($attributes['default']))
	{
		return '<a href="'.$content.'">'.$content.'</a>';
	}
	return '<a href="'.$attributes['default'].'">'.$content.'</a>';
}

function bbcode_image ($action, $attributes, $content, $params, $node_object)
{
	global $encode;
	if ($action == 'validate')
	{
		return true;
	}
	if (!isset($content) || empty($content))
	{
		// Hat der User das Attribut falsch angegeben?
		if (isset($attributes['default']) && !empty($attributes['default']))
		{
			$content = $attributes['default'];
		}
		else
		{
			return "[img][/img]";
		}
	}
	
	$disallowed = array('javascript:', 'file:', 'data:', 'jar:');
	foreach ($disallowed as $string)
	{
		// XXS gibts hier nicht!
		if (preg_match("/$string/i", $content, $matches))
		{
			return "[img]".$encode->encode_html($content)."[/img]";
		}
			
	}
	return '<img src="'.$encode->encode_html($content).'" alt="" />';
}	


function bbcode_quote ($action, $attributes, $content, $params, &$node_object) {
	global $bbcode;
	if ($action == 'validate')
	{
		return true;
	}
	$quote = isset($attributes['default']) ? $attributes['default'] . " hat folgendes geschrieben:" : "Zitat:";
	return "<table width=\"90%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\" align=\"center\">
			<tr>
				<td><span class=\"genmed\"><strong>" . $attributes['default'] . " hat folgendes geschrieben:</strong></span></td>
			</tr>
			<tr>
				<td class=\"quote\">" . $content . "</td>
			</tr>
		</table>";
}

$bbcode = new StringParser_BBCode ();

// Kleiner Fix: Das tut qGB bereits für uns...
$bbcode->setParagraphHandlingParameters("\n\n", '', '');

$bbcode->addFilter (STRINGPARSER_FILTER_PRE, 'convertlinebreaks');

$bbcode->addCode ('quote', 'usecontent', 'bbcode_quote', array (),
                  'inline', array ('listitem', 'block', 'inline', 'link', 'quote', 'image'), array ());

$bbcode->addCode ('b', 'simple_replace', null, array ('start_tag' => '<b>', 'end_tag' => '</b>'),
                  'inline', array ('listitem', 'block', 'inline', 'link', 'quote'), array ());

$bbcode->addCode ('i', 'simple_replace', null, array ('start_tag' => '<i>', 'end_tag' => '</i>'),
                  'inline', array ('listitem', 'block', 'inline', 'link'), array ());

$bbcode->addCode ('u', 'simple_replace', null, array ('start_tag' => '<u>', 'end_tag' => '</u>'),
                  'inline', array ('listitem', 'block', 'inline', 'link'), array ());

$bbcode->addCode ('url', 'usecontent?', 'bbcode_url', array ('usecontent_param' => 'default'),
                  'link', array ('listitem', 'block', 'inline'), array ('link'));

$bbcode->addCode ('link', 'callback_replace_single', 'bbcode_url', array (),
                  'link', array ('listitem', 'block', 'inline'), array ('link'));

$bbcode->addCode ('img', 'usecontent', 'bbcode_image', array (),
                  'image', array ('listitem', 'block', 'inline', 'link'), array ());

$bbcode->addCode ('bild', 'usecontent', 'bbcode_image', array (),
                  'image', array ('listitem', 'block', 'inline', 'link'), array ());

$bbcode->addCode ('list', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'),
                  'list', array ('block', 'listitem'), array ());

$bbcode->addCode ('*', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'),
                  'listitem', array ('list'), array ());

$bbcode->setOccurrenceType ('img', 'image');

$bbcode->setOccurrenceType ('bild', 'image');

$bbcode->setMaxOccurrences ('image', $config_table['limit_images']);

$bbcode->addParser ('list', 'bbcode_stripcontents');

$bbcode->setCodeFlag ('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);

$bbcode->setCodeFlag ('*', 'paragraphs', true);

$bbcode->setCodeFlag ('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);

$bbcode->setCodeFlag ('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);

$bbcode->setCodeFlag ('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);

$bbcode->setRootParagraphHandling (true);

?>