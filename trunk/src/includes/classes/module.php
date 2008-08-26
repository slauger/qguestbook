<?php

Class qModule
{
	// Aktive Module, die bereits laufen
	private $active_modules = array();
	
	// Wichtige Module, die für qGB unerlässlich sind
	private $required_modules = array();
	
	public function __construct()
	{
		global $db;
		$sql = 'SELECT directory, name
				FROM ' . ADDONS_TABLE . '
			WHERE active = ' . $db->sql_escape(1);
		if (!$result = $db->sql_query($sql)) {
			message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
		}
		
		// Aktive Module laden
		while ($row = $db->sql_fetchrow($result, 'BOTH')) {
			$this->load($row['directory']);
		}
	}
	
	public function action($action, $module = 'all')
	{
		$this->start($module, $action);
	}
	
	public function start($module, $method)
	{
		if (empty($this->active_modules)) {
			return;
		}
		
		if ($module == 'all') {
			foreach ($this->active_modules as $module) {
				$this->start($module, $method);
			}
		} else {
			if (method_exists($module, $method)) {
				eval('$module->'.$method.'();');
			}
		}
	}
	
	public function load($module)
	{
		global $root_dir;
		if (is_array($module)) {
			foreach ($module as $string) {
				$this->load($string);
			}
			return true;
		}
		
		// Hat das Modul eine Klasse?
		if (file_exists("{$root_dir}modules/{$module}/class.php")) {
			include_once "{$root_dir}modules/{$module}/class.php";
		} else {
			trigger_error("<b>qModule:</b> Failed to start Module $module (class.php not found)", E_USER_NOTICE);
		}
		
		// Wie stehts mit der Klasse?
		if (class_exists($module)) {
			$this->active_modules[$module] = new $module;
		} else {
			trigger_error("<b>qModule:</b> Failed to start Module $module (class not found)", E_USER_NOTICE);
		}
	}
	
	public function set_status($module, $status) {
		$sql = 'UPDATE ' . ADDONS_TABLE . '
				SET `active` = '.$db->sql_escape($active) . '
					 WHERE addon_id = ' . $db->sql_escape($module) . '
				LIMIT 1';
		if (!$result = $db->sql_query($sql)) {
			message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
		}
	}
	
	public function adjustable_modules()
	{
		global $db;
		$sql = 'SELECT name, directory
				FROM ' . ADDONS_TABLE . '
			WHERE admin = ' . $db->sql_escape(1);
		if (!$result = $db->sql_query($sql)) {
			message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
		}
		while ($row = $db->sql_fetchrow($result, 'BOTH')) {
			$return[$row['name']] = $row['directory'];
		}
		return $return;
	}
	
	public function module_infos()
	{
		global $db;
		$sql = 'SELECT id, admin, directory, name, description, version, author, license, active
				FROM ' . ADDONS_TABLE . '
			WHERE admin = ' . $db->sql_escape(1);
		if (!$result = $db->sql_query($sql)) {
			message_die($lang['ERROR_MAIN'], sprintf($lang['SQL_ERROR_EXPLAIN'], $error['code'], $error['error'], __FILE__, __LINE__));
		}
		while ($row = $db->sql_fetchrow($result, 'BOTH')) {
			$return[] = $row;
		}
		return $return;
	}
	
	// Modul installieren
	// In diesem Fall muss $dir übergeben werden
	public function install($module)
	{
		return; // Nichts zu tun
	}
	
	// Deinstallation via $id
	public function uninstall($module)
	{
		return; // Nichts zu tun
	}
	
	public function count_modules()
	{
		// Rein für statistische Zwecke
		return count($this->active_modules);
	}
}

?>