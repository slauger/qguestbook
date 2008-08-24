<?php
/**
* qDatabase_PostgreSQL
*
* The PostgreSQL Interface for qGuestbook.
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

$database_class = 'qDatabase_PostgreSQL';

Class qDatabase_PostgreSQL
{
	private $latest_query = '';
	private $num_queries = 0;

	public function __construct($server, $username, $password, $database, $persistency = false)
	{
		$connection_string = "host=$server"
		
		if ($port = @strstr($server, ':'))
		{
			$connection_string .= " port=$port";
		}
		
		$connection_string .= " dbname=$database user=$username password=$password";
		
		if ($persistency == true)
		{
			if (!$this->connection_id = pg_pconnect($connection_string))
			{
				$error = $this->sql_error();
				die('SQLite Error ' . $error['code'] . ': ' . $error['error']);
			}
		} else {
			if (!$this->connection_id = pg_connect($connection_string))
			{
				$error = $this->sql_error();
				die('SQLite Error ' . $error['code'] . ': ' . $error['error']);
			}
		}
		return true;
	}

	public function sql_server_info()
	{
		$info = pg_version($this->connection_id);
		return 'PostgreSQL ' . $info['client'];
	}

	public function sql_num_queries()
	{
		return $this->num_queries;
	}

	public function sql_array_type($type)
	{
		switch ($type)
		{
			case 'BOTH':
				$type = PGSQL_BOTH;
			break;
			case 'ASSOC':
				$type = PGSQL_ASSOC;
			break;
			case 'NUM':
				$type = PGSQL_NUM;
			break;
			default:
				$type = PGSQL_BOTH;
			break;
		}
		return $type;
	}

	// Nicht mehr nötig, Verbindung besteht bereits.
	public function sql_connect($database, $persistency = false)
	{
		return true;
	}

	// Ebenfalls nicht mehr nötig
	public function sql_select($database)
	{
		return true;
	}

	public function sql_close()
	{
		return @pg_close($this->connection_id);
	}

	public function sql_error()
	{
		return array(
			'code' => "",
			'error' => pg_last_error($this->connection_id),
			'sql' => $this->latest_query,
		);
	}

	public function sql_query($sql)
	{
		unset($this->query_result);
		unset($this->latest_query);
		if(!empty($sql))
		{
			$this->latest_query = $sql;
			$this->num_queries++;
			$query_result = @pg_query($sql, $this->connection_id);
		}
		if(isset($query_result) && $query_result)
		{
			return $query_result;
		}
	}

	public function sql_fetchrow($sql, $type = 'BOTH')
	{
		if (!$this->sql_numrows($sql))
		{
			return false;
		}
		$type = $this->sql_array_type($type);
		return @pg_fetch_array($sql, $type);
	}

	public function sql_numrows($sql)
	{
		return @pg_num_rows($sql);
	}

	public function sql_result($sql, $row = 0, $field = '')
	{
		if (!empty($field))
		{
			// return @sqlite_free_result($sql, $row, $field);
		}
		return @pg_get_result($sql);
	}

	public function sql_free_result($sql)
	{
		return @pg_free_result($sql);
	}

	public function sql_escape($string)
	{
		if (@get_magic_quotes_gpc())
		{
			$string = stripslashes($string);
		}
		if (!is_numeric($string))
		{
			$string = '\'' . pg_escape_string($string) . '\'';
		}
		return $string;
	}
}

?>