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
* @subpackage common
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

if (!defined('GUESTBOOK')) {
	die('Hacking attempt');
	exit;
}

/**
 * Beschreibung:
 *
 * Sprachdateien lassen sich durch die Methode qLanguage::cache_language() cachen.
 * Sprachdateien im Cache sind bereits komplett mit der Funktion decode_html()
 * bearbeitet und somit komplett in HTML umgewandelt. Dadurch kann qGuestbook diese
 * besser und vorallem schneller verarbeiten.
 *
 * Es wird empfohlen diese Methode zu nutzen, solang mindestens PHP 4.0.7 oder PHP5
 * installiert ist. In Versionen vor PHP 4.0.7 funktioniert die Funktion
 * serialize() nicht richtig und es kann möglicherweiße zu Fehlern kommen.
 * Bei Updates oder änderungen an den Sprachdateien muss zwingend der Cache erneut
 * generiert werden.
 *
 * Bitte nutzen sie dazu die Funktion im Modertoren Bereich.
 *
 * Achtung: Der jewelige Ordner in dem der Cache gespeichert wird, (Verzeichniss "includes/store") benötigt dann natürlich Chmod 777.
 */

Class qLanguage // extends qLanguageAbstract
{

	/**
	 * Enthält das geparste Sprachpaket.
	 *
	 * @var    array
	 * @access protected
	 */
	protected $language_pack;

	/**
	 * Der Name des benutzen Sprachpakets.
	 *
	 * @var    string
	 * @access protected
	 */
	protected $language_pack_name;

	/**
	 * Der Konstruktor der Klasse.
	 *
	 * @param  string  $language Das Sprachpaket, das genutzt werden soll.
	 * @param  integer $serialized Gib an, ob ein serialisiertes Sprachpaket geladen wird.
	 * @access protected
	 */
	public function __construct($language = 'de', $seralized = 0)
	{
		global $root_dir, $config_table;

		$this->language_pack_name = $config_table['language'];
		$language_file = sprintf('%1sincludes/language/%2s/common.php', $root_dir, $config_table['language']);

		if (!is_file($language_file))
		{
			die('<b>qLanguage Error:</b> failed getting language file' . $language_file);
		}

		$this->import_unserialized($language_file);
	}

	/**
	 * Das komplette Sprachpaket in einen anderen
	 * Zeichensatz konvertieren.
	 */
	public function encode_language()
	{
		global $encode, $lang;
		foreach ($lang as $key => $value)
		{
			if (isset($lang[$key]))
			{
				$lang[$key] = $encode->encode_string($value);
			}
		}
	}

	/**
	 * Lädt eine Datei aus dem Cache und bereitet sie auf.
	 *
	 * @param  string  $filename Pfad zur zu ladenden Datei.
	 * @access protected
	 */
	protected function import_seralized($filename)
	{
		$serialized = file_get_contents($filename);
		$this->language_pack = unserialize($serialized);
	}

	/**
	 * Macht das serialisierte Sprachpaket global verfügbar.
	 *
	 * @access protected
	 */
	protected function export_seralized()
	{
		global $lang;
		if (is_array($lang))
		{
			$lang = array();
		}
		$lang = $this->language_pack;
	}

	/**
	 * Serialisiert ein bearbeitetes Sprachpaket.
	 *
	 * @param  string  $filename Pfad, in dem die serialisiert Datei gespeichert werden soll.
	 * @access protected
	 */
	protected function cache_language($filename)
	{
		$serialized = serialize($this->language_pack);
		file_put_contents($filename, $serialized);
	}


	protected function import_unserialized($filename)
	{
		global $lang;
		include_once $filename;
		$this->language_pack = $lang;
	}
	
	/**
	* Alias for export_unserialized()
	*/
	public function export_language()
	{
		$this->export_unserialized();
	}
	
	/**
	 * Macht das serialisierte Sprachpaket wieder global verfügbar.
	 *
	 * @access protected
	 */
	protected function export_unserialized($var = 'lang')
	{
		global $$var, $config_table;
		global $root_dir, $encode;
		
		if (!is_array($lang))
		{
			$lang = array();
		}
		
		// Müssen wir das Sprachpaket behandeln?
		if ($encode->get_encoding() != $lang['CHARSET'])
		{
			array_map_r(array($encode, 'encode_html'), $this->language_pack);
			$$var = $this->language_pack;
		}
	}
	
	/**
	 * Lädt ein E-Mail Template
	 */
	public function load_template($template) {
		global $config_table, $root_dir;
		if (isset($config_table['language']) && !empty($config_table['language'])) {
			$template_path = $root_dir . 'includes/language/' . $config_table['language'] . '/email/';
			if (!$return = @file_get_contents($template_path . $template))
			{
				return false;
			}
			return $return;
		}
	}

	public function language_info()
	{
		return $this->language_pack_name;
	}
	
	public function get($var)
	{
		if (!isset($this->language_pack[$var]) || empty($this->language_pack[$var])) {
			return false;
		}
		return $this->language_pack[$var];
	}
}

?>