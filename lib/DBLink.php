<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class DBLink {

	protected $hostname;
	protected $database;
	protected $username;
	protected $password;
	protected $dblink;

	function __construct($host, $db, $user, $pass = "") {
		$this->hostname = $host;
		$this->database = $db;
		$this->username = $user;
		$this->password = $pass;
	}

	function connect() {
		global $user;
		$connect_str = 
			"host=".$this->hostname.
			" dbname=".$this->database.
			" user=".$this->username;
		if ($this->password != "")
			$connect_str .= " password=".$this->password;
		$this->dblink = pg_connect($connect_str)
			or die (pg_last_error()."\n");
		if (isset($user->uid) && ($user->uid != "")) {
			$sql = "select time_zone from r_person where drupal_id = $1";
			$result = pg_query_params($this->dblink, $sql, array($user->uid))
				or die ($sql."\n".pg_last_error()."\n");
			$row = pg_fetch_assoc($result);
			$sql = "set time zone '".$row['time_zone']."'";
			$result = pg_query($this->dblink, $sql)
				or die ($sql."\n".pg_last_error()."\n");
			pg_free_result($result);
		}
	}

	function disconnect() {
		if (isset ($this->dblink))
			pg_close($this->dblink);
		else
			pg_close();
	}

	function execute($sql, $params = array()) {
		if (!isset ($this->dblink)) $this->connect();
		$result = pg_query_params($this->dblink, $sql, $params)
			or die ($sql."\n".pg_last_error()."\n");
		return ($result);
	}

	function query($sql, $params = array()) {
		if (!isset ($this->dblink)) $this->connect();
		$result = pg_query_params($this->dblink, $sql, $params)
			or die ($sql."\n".pg_last_error()."\n");
		if ($result) {
			$returnArray = pg_fetch_all($result);
			if (!$returnArray)
				$returnArray = array();
		} else
			$returnArray = array();
		pg_free_result($result);
		return $returnArray;
	}

}
?>
