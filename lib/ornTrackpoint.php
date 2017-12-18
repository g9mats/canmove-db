<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class ornTrackpoint {

	protected $trackpoint_id;
	protected $track_id;
	protected $trackpoint_no;
	protected $elapsed_time;
	protected $x;
	protected $y;
	protected $z;
	protected $air_density;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $tid, $tpno) {
		if ($res = $db->query(
				"select trackpoint_id,track_id,trackpoint_no,elapsed_time,
				x,y,z,air_density
				from d_orn_trackpoint
				where track_id = $1
				  and trackpoint_no = $2",
				array($tid, $tpno))) {
			$this->trackpoint_id = $res[0]['trackpoint_id'];
			$this->track_id = $res[0]['track_id'];
			$this->trackpoint_no = $res[0]['trackpoint_no'];
			$this->elapsed_time = $res[0]['elapsed_time'];
			$this->x = $res[0]['x'];
			$this->y = $res[0]['y'];
			$this->z = $res[0]['z'];
			$this->air_density = $res[0]['air_density'];
			$this->valid_record = true;
			return $this->trackpoint_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $tid, $tpno, $etime, $x, $y, $z, $adens) {
		$res = $db->query("select nextval('d_orn_trackpoint_trackpoint_id_seq') id");
		$this->trackpoint_id=$res[0]['id'];
		$db->execute(
			"insert into d_orn_trackpoint (trackpoint_id, track_id,
				trackpoint_no, elapsed_time, x, y, z, air_density)
				values ($1, $2, $3, $4, $5, $6, $7, $8)",
				array($this->trackpoint_id, $tid, $tpno, $etime,
					$x, $y, $z, $adens));
		$this->track_id = $tid;
		$this->trackpoint_no = $tpno;
		$this->elapsed_time = $etime;
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		$this->air_density = $adens;
		$this->valid_record = true;
		return $this->trackpoint_id;
	}

	function update($db, $tpid, $tid, $tpno, $etime, $x, $y, $z, $adens) {
		if (!$this->valid_record or ($tpid != $this->trackpoint_id))
			$tpid = $this->select($db, $tid, $tpno);
		if (($etime != $this->elapsed_time) or ($x != $this->x) or
			($y != $this->y) or ($z != $this->z) or
			($adens != $this->air_density)) {
			$db->execute(
				"update d_orn_trackpoint set
					elapsed_time = $2,
					x = $3,
					y = $4,
					z = $5,
					air_density = $6
					where trackpoint_id = $1",
					array($tpid, $etime, $x, $y, $z, $adens));
			return 1;
		} else
			return 0;
	}

	function delete($db, $tpid) {
		$db->execute(
			"delete from d_orn_trackpoint where trackpoint_id = $1",
			array($tpid));
	}

}

?>
