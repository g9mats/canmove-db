<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class ornWindProfile {

	protected $wind_profile_id;
	protected $session_id;
	protected $wind_profile_no;
	protected $start_time;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $sid, $wpno) {
		if ($res = $db->query(
				"select wind_profile_id,session_id,wind_profile_no,
				start_time
				from d_orn_wind_profile
				where session_id = $1
				  and wind_profile_no = $2",
				array($sid, $wpno))) {
			$this->wind_profile_id = $res[0]['wind_profile_id'];
			$this->session_id = $res[0]['session_id'];
			$this->wind_profile_no = $res[0]['wind_profile_no'];
			$this->start_time = $res[0]['start_time'];
			$this->valid_record = true;
			return $this->wind_profile_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $sid, $wpno, $stime) {
		$res = $db->query("select nextval('d_orn_wind_profile_wind_profile_id_seq') id");
		$this->wind_profile_id=$res[0]['id'];
		$db->execute(
			"insert into d_orn_wind_profile (wind_profile_id, session_id,
				wind_profile_no, start_time)
				values ($1, $2, $3, $4)",
				array($this->wind_profile_id, $sid, $wpno, $stime));
		$this->session_id = $sid;
		$this->wind_profile_no = $wpno;
		$this->start_time = $stime;
		$this->valid_record = true;
		return $this->wind_profile_id;
	}

	function update($db, $wpid, $sid, $wpno, $stime) {
		if (!$this->valid_record or ($wpid != $this->wind_profile_id))
			$wpid = $this->select($db, $sid, $wpno);
		if ($stime != $this->start_time) {
			$db->execute(
				"update d_orn_wind_profile set
					start_time = $2
					where wind_profile_id = $1",
					array($wpid, $sid));
			return 1;
		} else
			return 0;
	}

	function delete($db, $wpid) {
		$db->execute(
			"delete from d_orn_wind_profile where wind_profile_id = $1",
			array($wpid));
	}

}

?>
