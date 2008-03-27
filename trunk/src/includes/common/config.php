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

Class qConfig
{
	private $config;	
	
	public function __construct($export = false)
	{
		if (!empty($export))
		{
			$this->export = $export;
		}
		
		$this->refresh();
	}
	
	public function refresh()
	{
		$this->config = array();
		$this->query();
		
		if (isset($this->export))
		{
			$this->export();
		}
	}
	
	private function query()
	{
		global $db;
		
		$sql = 'SELECT config_name, config_value
			FROM ' . CONFIG_TABLE;
		
		if (!$result = $db->sql_query($sql)) {
			trigger_error('cant query config table', E_USER_ERROR);
		}
		
		while ($row = $db->sql_fetchrow($result))
		{
			$this->config[$row['config_name']] = $row['config_value'];
		}
	}
	
	public function update($name, $value)
	{
		global $db;
		
		$sql = 'UPDATE ' . CONFIG_TABLE . '
				SET config_value = ' . $db->sql_escape($value) . '
			WHERE config_name = ' . $db->sql_escape($name) . '
				LIMIT 1';
		
		if (!$db->sql_query($sql)) {
			trigger_error('cant update config table', E_USER_WARNING);
		}
	}
	
	public function get($name)
	{
		if (isset($this->config[$name]))
		{
			return $this->config[$name];
		}
		return false;
	}
	
	public function add($name, $value)
	{
		// Nichts zu tun
		return false;
	}
	
	public function remove($name)
	{
		// Nichts zu tun
		return false;
	}
	
	private function export()
	{
		global ${$this->export};
		${$this->export} = $this->config;
	}

}