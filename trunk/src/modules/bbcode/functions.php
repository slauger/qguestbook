<?php

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

?>