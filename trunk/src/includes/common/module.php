<?php

Class qModule
{
	private $active_modules = array();
	private $required_modules = array();
	
	public function __construct()
	{
		$this->active_modules = array();
		$this->load(array(
			'bbcode',
			'cut_words',
		));
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
		if (file_exists($root_dir . 'modules/' . $module . '/' . $module . '.php')) {
			include_once $root_dir . 'modules/' . $module . '/' . $module . '.php';
		} else {
			trigger_error("$module: module file ($module.php) doesnt exists", E_USER_NOTICE);
		}
		
		if (class_exists($module)) {
			$this->active_modules[$module] = new $module;
		} else {
			trigger_error("$module: cant start class $module", E_USER_NOTICE);
		}
	}
	
	public function install($module)
	{

	}
	
	public function uninstall($module)
	{

	}
	
	public function num_modules()
	{
		return count($this->active_modules);
	}
}

?>