<?php

class TextLimiter
{
	public function stringLimit($text, $limit)
	{
		global $config;
		if (is_numeric($limit) && $limit > 0) {
			if (preg_match_all('#(\b\w{'.$config->get('max_lenght').',}\b)#', $text, $matches)) {
				foreach ($matches[0] as $cut_word) {
					$text = str_replace($cut_word, substr($cut_word, 0, -(strlen($cut_word) - $limit)) . '... ', $text);
				}
			}
		}
		return $text;
	}
	
	public function wordLimit($text, $limit)
	{
		return $text;
	}

	public function on_viewposts_second()
	{
		global $row, $config;
		// Maximale Zeichen per Wort begrenzen
		if ($config->get('string_limit') && $config->get('string_limit') != 0) {
			$row['posts_text'] = $this->stringLimit($row['posts_text']);
		}
		// Maximale Anzahl an Wörtern begrenzen
		if ($config->get('word_limit') && $config->get('word_limit') != 0) {
			$row['posts_text'] = $this->wordLimit($row['posts_text']);
		}
	}
}

?>