<?php

Class qModule
{
	private $user_actions; // = array();
	private $active_modules; // = array();
	
	public function action($action, $module = 'all')
	{
		switch ($action) {
			case 'on_start_output':
			case 'on_end_output':
			case 'on_error':
			case 'on_connect_db':
			case 'on_disconnect_db':
			break;
			case 'on_view_index':
				$this->start($module, 'on_view_index');
			break;
			case 'index_before_vars':
				$this->start($module, 'index_before_vars');
			break;
			case 'index_after_vars':
				$this->start($module, 'index_after_vars');
			break;
			case 'on_submit_post':
				$this->start($module, 'on_submit_post');
			break;
			default:
				// Benutzerdefinierte Action starten
				if (in_array($action, $this->user_actions)) {
					$this->start($module, $this->user_actions[$action]);
				}
			break;
		}
	}
	
	public function start($module, $method)
	{
		if (empty($this->active_modules)) {
			return false;
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
		} else die("file $module.php doesnt exists");
		if (class_exists($module)) {
			$this->active_modules[$module] = new $module;
		} else die("cant start $class");
		
	}
	
	public function user_add_action($name, $method)
	{
		if (in_array($this->user_actions, $name)) {
			// Unsere Actions dürfen nicht überschrieben werden
			return false;
		}
		$this->user_actions[$name] = $method;
	}
}

?>