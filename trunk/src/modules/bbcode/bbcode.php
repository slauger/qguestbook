<?php

class bbcode
{
	public function __construct()
	{
		global $config;
		$this->parser = new StringParser_BBCode ();
		$this->parser->setParagraphHandlingParameters("\n\n", '', '');
		$this->parser->addFilter (STRINGPARSER_FILTER_PRE, 'convertlinebreaks');
		$this->parser->addCode ('quote', 'usecontent', 'bbcode_quote', array (), 'inline', array ('listitem', 'block', 'inline', 'link', 'quote', 'image'), array ());
		$this->parser->addCode ('b', 'simple_replace', null, array ('start_tag' => '<b>', 'end_tag' => '</b>'),
                  'inline', array ('listitem', 'block', 'inline', 'link', 'quote'), array ());
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
	
	public function index_after_vars()
	{
		global $row;
		if (isset($row['posts_text']) && !empty($row['posts_text'])) {
			$row['posts_text'] = $this->bbcode($row['posts_text']);
			//$this->bbcode($row['posts_text']);
			//echo $this->bbcode($row['posts_text']);
			//$row['posts_text'] .= "\n\n[b]Dieser Teil wurde per Modul hinzugefügt! Hier koennte z. B. Ihre Werbung stehen![/b]\nmodule->action(on_loop_index); -> bbcode->on_loop_index();";
		}
	}
public function convertlinebreaks ($text)
{
	return preg_replace ("/\015\012|\015|\012/", "\n", $text);
}

public function bbcode_stripcontents ($text)
{
	return preg_replace ("/[^\n]/", '', $text);
}

public function bbcode_url ($action, $attributes, $content, $params, $node_object)
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

public function bbcode_image ($action, $attributes, $content, $params, $node_object)
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


public function bbcode_quote ($action, $attributes, $content, $params, &$node_object) {
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

}

?>