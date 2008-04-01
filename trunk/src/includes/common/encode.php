<?php
/**
* qEncode
*
* Zuständig für alle Kodierungssachen.
*
* <code>
*   <?php
*
*   $encode = new qEncode('utf-8', 'iso-8859-1');
*   $utf8 = 'Dies ist ein Text in Unicode';
*   $iso = encode_string($utf8);
*   echo $iso; // Ausgabe in iso-8859-1
*
*   ?>
* </code>
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
* @subpackage common
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id$
* @link       http://www.simlau.net/
* @since      File available since Release 0.2.2
*/

Class qEncode
{
	public function __construct($input = 'utf-8', $output = 'utf-8') {
		$this->input = $input;
		$this->output = $output;
	}

	public function get_encoding()
	{
		return array(
			'input' => $this->input,
			'output' => $this->output,
		);
	}

	// Methode überarbeitet in 0.2.4
	// Parameter $double_encode hinzugefügt (PHP 5.2.3)
	public function encode_html($string, $encode = true, $double_encode = true)
	{
		if (empty($string) || !is_string($string))
		{
			return $string;
		}
		
		// Soll der Zeichensatz umgewandelt werden?
		if (isset($encode) && $encode)
		{
			$string = $this->encode_string($string);
		}
		
		$return = @htmlentities($string, ENT_QUOTES, $this->output, $double_encode);
		
		if (!empty($return))
		{
			return $return;
		}
		return $string;
	}

	public function encode_string($string)
	{
		// Wenn gleicher Zeichensatz oder kein String, direkt zurückgeben
		if ($this->input == $this->output || !is_string($string) || empty($string))
		{
			return $string;
		}
		if (function_exists('utf8_encode'))
		{
			// Added in 0.2.4
			if ($this->input == 'iso-8859-1' && $this->output == 'utf-8')
			{
				$return = @utf8_encode($string);
				
				if (!empty($return))
				{
					return $return;
				}
			}
		}
		// Mit Iconv versuchen
		if (function_exists('iconv'))
		{
			$return = @iconv($this->input, $this->output, $string);

			if (!empty($return))
			{
				return $return;
			}
		}
		// Nun, wir versuchen es weiter...
		if (function_exists('mb_convert_encoding'))
		{
			switch ($this->input)
			{
				case 'iso-8859-1':
				case 'iso-8859-2':
				case 'iso-8859-4':
				case 'iso-8859-7':
				case 'iso-8859-9':
				case 'iso-8859-15':
				case 'windows-1251':
				case 'windows-1252':
				case 'cp1252':
				case 'shift_jis':
				case 'euc-kr':
				case 'big5':
				case 'gb2312':
					$return = @mb_convert_encoding($str, $this->output, $this->input);

					if (!empty($return))
					{
						return $return;
					}
			}
		}
		// Letzte Chance, recode_string
		if (function_exists('recode_string'))
		{
			$return = @recode_string($this->input . '..' . $this->output, $string);

			if (!empty($return))
			{
				return $return;
			}
		}
		// String wird unbehandelt zurückgegeben
		return $string;
	}
}

?>