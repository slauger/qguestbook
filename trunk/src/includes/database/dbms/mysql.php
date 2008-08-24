<?php
/**
* qDatabase_MySQL
*
* The MySQL Interface for qGuestbook.
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
* @subpackage database
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

$database_class = 'qDatabase_MySQL';

Class qDatabase_MySQL
{
	private $latest_query = '';
	private $num_queries = 0;

	public function __construct($server, $username, $password, $database, $persistency = false)
	{
		if (!$this->connection_id = $this->sql_connect($server, $username, $password, $persistency)) {
			$error = $this->sql_error();
			die('MySQL Error ' . $error['code'] . ': ' . $error['error']);
		}

		if (!$this->sql_select($database)) {
			$error = $this->sql_error();
			die('MySQL Error ' . $error['code'] . ': ' . $error['error']);
		}
		return true;
	}

	public function sql_server_info()
	{
		return 'MySQL ' . mysql_get_server_info($this->connection_id);
	}

	public function sql_num_queries()
	{
		return $this->num_queries;
	}

	public function sql_array_type($type)
	{
		switch ($type) {
			case 'BOTH':
				$type = MYSQL_BOTH;
			break;
			case 'ASSOC':
				$type = MYSQL_ASSOC;
			break;
			case 'NUM':
				$type = MYSQL_NUM;
			break;
			default:
				$type = MYSQL_BOTH;
			break;
		}
		return $type;
	}

	public function sql_connect($server, $username, $password, $persistency = false)
	{
		if (isset($persistency) && $persistency == true) {
			return @mysql_pconnect($server, $username, $password);
		}
		return @mysql_connect($server, $username, $password);
	}

	public function sql_select($database)
	{
		return @mysql_select_db($database, $this->connection_id);
	}

	public function sql_close()
	{
		return @mysql_close($this->connection_id);
	}

	public function sql_error()
	{
		return array(
			'code' => mysql_errno($this->connection_id),
			'error' => mysql_error($this->connection_id),
			'sql' => $this->latest_query,
		);
	}

	public function sql_query($sql)
	{
		unset($this->query_result);
		unset($this->latest_query);
		if(!empty($sql)) {
			$this->latest_query = $sql;
			$this->num_queries++;
			$query_result = @mysql_query($sql, $this->connection_id);
		}
		if(isset($query_result) && $query_result) {
			return $query_result;
		}
	}

	public function sql_fetchrow($sql, $type = 'BOTH')
	{
		if (!$this->sql_numrows($sql)) {
			return false;
		}
		$type = $this->sql_array_type($type);
		return @mysql_fetch_array($sql, $type);
	}

	public function sql_numrows($sql)
	{
		return @mysql_num_rows($sql);
	}

	public function sql_result($sql, $row = 0, $field = '')
	{
		if (!empty($field)) {
			return @mysql_result($sql, $row, $field);
		}
		return @mysql_result($sql, $row);
	}

	public function sql_free_result($sql)
	{
		return @mysql_free_result($sql);
	}

	public function sql_escape($string, $quotes = true)
	{
		if (@get_magic_quotes_gpc()) {
			$string = stripslashes($string);
		}
		if (!is_numeric($string) && $quotes) {
			$string = '\'' . mysql_real_escape_string($string) . '\'';
		}
		return $string;
	}

	public function sql_split_dump($file)
	{
		if (!file_exists($file) || !is_readable($file)) {
			return false;
		}
		
		// Keywords die vorkommen muessen
		$keywords = array('ALTER', 'CREATE', 'DELETE', 'DROP', 'INSERT', 'REPLACE', 'SELECT', 'SET', 'TRUNCATE', 'UPDATE', 'USE');
		
		$file = array_filter(file($file), create_function('$line', 'return strpos(ltrim($line), "--") !== 0;'));
		$regexp = sprintf('/\s*;\s*(?=(%s)\b)/s', implode('|', $keywords));
		$splitter = preg_split($regexp, implode("\r\n", $file));
		$splitter = array_map(create_function('$line', 'return preg_replace("/[\s;]*$/", "", $line);'), $splitter);
		$return = array_filter($splitter, create_function('$line', 'return !empty($line);'));

		return $return;
	}
}

?>