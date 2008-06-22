<?php

$sql = "SELECT directory
	FROM
	WHERE type = 'module'
	AND function = 'snippet'";

$query_result=$database->query($query);
if ($query_result->numRows()>0) {
	while ($row = $query_result->fetchRow()) {
		$module_dir = $row['directory'];
		if (file_exists(WB_PATH.'/modules/'.$module_dir.'/include.php')) {
			include(WB_PATH.'/modules/'.$module_dir.'/include.php');
		}
	}
}

Class qModules
{
	public function add_callbacks($function)
	{
		if ($args = func_get_args() == 1) {
			return false;
		}
		
		$args_arr = func_get_args();
		
		for ($i = 1; $i < $args; $i++)
		{
			$cb_func = $args_arr[$i];
			$this->callbacks[$cb_func] = $function;
		}
		
		return true;
	}
	
	public function prepare_var($var, $name)
	{
		if (isset($this->callbacks[$name])) {
			foreach ($this->callbacks[$name] as $function) {
				if (is_array($var)) {
					array_map($function, $name);
				}
			}
		}
	}
	
	public function get_info($module)
	{
		if (!is_file("{$this->directory}/$module/info.php")) {
			return false;
		}
		include_once "{$this->directory}/$module/info.php";
	}
	public function module_loaded($module)
	{
		
	}
}

$modules = new qModules('modules/');
$modules->add_callbacks('htmlspecialchars', 'mytext', 'comment_text');
$modules->add_callbacks('bbcodes', 'mytext', 'comment_text');
echo $modules->prepare_string('[b]Ich bin so geil, komm und fick mich![/b]', 'comment_text');

?>