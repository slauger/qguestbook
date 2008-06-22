<?php

class cut_words
{
	public function on_viewposts_first()
	{
		global $row;
		$row['posts_text'] = words_cut($row['posts_text']);
	}
}

?>