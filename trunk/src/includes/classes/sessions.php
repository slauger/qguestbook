<?php
/**
 * Zuständig für Benutzersystem und Sessions
 * Wird erst später in den Einsatz kommen
 */

Class qSessions
{
	private $user_session_id;
	
	public function __construct()
	{
		if (valid_var($_SESSION['session_id'])) {
			$this->user_session_id = $_SESSION['session_id'];
		}
		
		if (!$this->session_logged_in($this->user_session_id)) {
			$this->session_generate();
		}
	}
	
	public function session_generate()
	{
		
	}
	
	public function session_logged_in($user_session = '')
	{
		if (empty($user_session)) {
			$user_session = $this->user_session_id;
		}
		
	}
	
	public function session_kill()
	{
		global $db;
		session_destroy();
		$sql = 'UPDATE ' . USERS_TABLE . '
			SET user_session = \'\'
				WHERE user_session = ' . $db->sql_escape($this->user_session_id) . '
			LIMIT 1';
		$db->sql_query($sql);
	}
	
	public function session_kill_all()
	{
		
	}
	
	public function session_update()
	{
		
	}
}

Class qUsers
{
	
	public function user_exists($username, $password)
	{
		global $db;
		$sql = 'SELECT COUNT(´users_id´)
			FROM ' . POSTS_TABLE . '
				WHERE user_name = ' . $db->sql_escape($username) . '
				AND user_pass = ' . $db->sql_escape($password) . '
			LIMIT 1';
		
		if (!$users = $db->sql_result($sql, 0)) {
			message_die('', '');
		}
		
		if (!$db->sql_result($result, 0)) {
			return false;
		}
	}
	
	public function user_access_page($user_id)
	{
		if (REQUIRED_AUTH_LEVEL > USER_ANONYMOUS) {
			if (!$sessions->session_logged_in()) {
				header('Location: ' . PAGE_ADMIN_LOGIN);
				exit;
			}
			if (!$sessions->session_user_level) {
				return false;
			}
		}
	}
}

?>