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
    die("Hacking attempt");
    exit;
}

Class qStyles
{
	private $style;
	private $theme;
	private $template;
	private $imageset;
	
	public function __construct()
	{
		if (defined('ADMIN_PAGE') || defined('LOGIN_PAGE'))
		{
			$this->style_admin();
		} else {
			$this->style_main();
		}
	}

	private function style_admin()
	{
		global $root_dir;
		$this->path['style'] = $root_dir . 'admin/style/';
		$this->path['theme'] = $this->path['style'] . 'theme/style.css';
		$this->path['imageset'] = $this->path['style'] . 'imageset/';
		$this->path['template'] = $this->path['style'] . 'template/';
	}
	
	private function style_main()
	{
		global $config, $db, $root_dir;
		if (!$config->get('default_style')) {
			trigger_error('no style selected', E_USER_ERROR);
		}
		
		$sql = 'SELECT *
			FROM ' . STYLES_TABLE . '
				WHERE styles_id = ' . $db->sql_escape($config->get('default_style')) . '
			LIMIT 1';
		
		if (!$result = $db->sql_query($sql)) {
			trigger_error('cant query styles data', E_USER_ERROR);
		}
		
		if ($row = $db->sql_fetchrow($result)) {
			$this->style['id'] = $config->get('default_style');
			$this->style['name'] = $row['styles_name'];
			$this->style['imageset'] = $row['styles_imageset'];
			$this->style['template'] = $row['styles_template'];
			$this->style['theme'] = $row['styles_theme'];
		}
		
		$this->path['style'] = $root_dir . 'styles/';
		$this->path['theme'] = $this->path['style'] . 'themes/' . $this->style['theme'] . '/style.css';
		$this->path['imageset'] = $this->path['style'] . 'imagesets/' . $this->style['imageset'];
		$this->path['template'] = $this->path['style'] . 'templates/' . $this->style['template'];
	}
	
	public function parse()
	{
		global $config, $template;
		include_once $this->path['imageset'] . '/imageset.php';
		
		foreach ($imageset as $image => $path) {
			$this->images[$image] = $this->path['imageset'] . '/' . sprintf($path, $config->get('language'));
			$template->assign_vars(array(
				$image => $this->images[$image],
			));
		}
		
		$template->assign_vars(array(
			'STYLE_THEME' => $this->path['theme'],
		));
	}
	
	public function img($img, $alt)
	{
		global $language;
		if (!isset($this->images[$img])) {
			return false;
		}
		if (!$language->get($alt)) {
			$alt = 'IMAGE';
		}
		return sprintf("<img src=\"%1s\" alt=\"%2s\" />", $this->images[$img], $language->get($alt));
	}

	public function get($name)
	{
		switch ($name)
		{
			case 'template_path':
				return $this->path['template'];
			default:
				return false;
		}
	}
}

?>