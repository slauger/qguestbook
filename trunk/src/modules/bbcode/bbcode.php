<?php
/**
* qGuestbook BBCode Module
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
* @category   Module
* @package    Modules
* @subpackage BBCode
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

include_once dirname(__FILE__).'/functions.php';
require_once dirname(__FILE__).'/stringparser_bbcode.class.php';

class bbcode
{
	public function __construct()
	{
		global $config;
		$this->parser = new StringParser_BBCode ();
		$this->parser->setParagraphHandlingParameters("\n\n", '', '');
		$this->parser->addFilter (STRINGPARSER_FILTER_PRE, 'convertlinebreaks');
		$this->parser->addCode ('quote', 'usecontent', 'bbcode_quote', array (), 'inline', array ('listitem', 'block', 'inline', 'link', 'quote', 'image'), array ());
		$this->parser->addCode ('b', 'simple_replace', null, array ('start_tag' => '<b>', 'end_tag' => '</b>'), 'inline', array ('listitem', 'block', 'inline', 'link', 'quote'), array ());
		$this->parser->addCode ('i', 'simple_replace', null, array ('start_tag' => '<i>', 'end_tag' => '</i>'),
                  'inline', array ('listitem', 'block', 'inline', 'link'), array ());
		$this->parser->addCode ('u', 'simple_replace', null, array ('start_tag' => '<u>', 'end_tag' => '</u>'),
                  'inline', array ('listitem', 'block', 'inline', 'link'), array ());
		$this->parser->addCode ('url', 'usecontent?', 'bbcode_url', array ('usecontent_param' => 'default'),
                  'link', array ('listitem', 'block', 'inline'), array ('link'));
		$this->parser->addCode ('link', 'callback_replace_single', 'bbcode_url', array (),
                  'link', array ('listitem', 'block', 'inline'), array ('link'));
		$this->parser->addCode ('img', 'usecontent', 'bbcode_image', array (),
                  'image', array ('listitem', 'block', 'inline', 'link'), array ());
		$this->parser->addCode ('bild', 'usecontent', 'bbcode_image', array (),
                  'image', array ('listitem', 'block', 'inline', 'link'), array ());
		$this->parser->addCode ('list', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'),
                  'list', array ('block', 'listitem'), array ());
		$this->parser->addCode ('*', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'),
                  'listitem', array ('list'), array ());
		$this->parser->setOccurrenceType ('img', 'image');
		$this->parser->setOccurrenceType ('bild', 'image');
		$this->parser->setMaxOccurrences ('image', $config->get('limit_images'));
		$this->parser->addParser ('list', 'bbcode_stripcontents');
		$this->parser->setCodeFlag ('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
		$this->parser->setCodeFlag ('*', 'paragraphs', true);
		$this->parser->setCodeFlag ('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
		$this->parser->setCodeFlag ('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
		$this->parser->setCodeFlag ('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);
		$this->parser->setRootParagraphHandling (true);
	}

	public function bbcode($string)
	{
		global $config;
		//$string = nl2br(words_cut(badwords(($string))));
		//$string = ($config->get('smilies')) ? smilies($string) : $string;
		$string = ($config->get('bbcode')) ? $this->parser->parse($string) : $string;
		return $string;
	}
	
	public function on_viewposts_second()
	{
		global $row;
		if (isset($row['posts_text']) && !empty($row['posts_text'])) {
			$row['posts_text'] = $this->bbcode($row['posts_text']);
			//echo $this->bbcode($row['posts_text']);
			//$row['posts_text'] .= "\n\n[b]Dieser Teil wurde per Modul hinzugefügt! Hier koennte z. B. Ihre Werbung stehen![/b]\nmodule->action(on_loop_index); -> bbcode->on_loop_index();";
		}
	}
}


?>