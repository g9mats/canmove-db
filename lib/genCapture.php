<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class genCapture {

	protected $capture_id;
	protected $animal_id;
	protected $capture_time;
	protected $latitude;
	protected $longitude;
	protected $location;
	protected $operator_id;
	protected $remark;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $aid, $time) {
		if ($res = $db->query(
				"select capture_id,animal_id,capture_time,
				latitude,longitude,location,operator_id,remark
				from d_gen_capture
				where animal_id = $1
				  and capture_time = $2",
				array($aid, $time))) {
			$this->capture_id = $res[0]['capture_id'];
			$this->animal_id = $res[0]['animal_id'];
			$this->capture_time = $res[0]['capture_time'];
			$this->latitude = $res[0]['latitude'];
			$this->longitude = $res[0]['longitude'];
			$this->location = $res[0]['location'];
			$this->operator_id = $res[0]['operator_id'];
			$this->remark = $res[0]['remark'];
			$this->valid_record = true;
			return $this->capture_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $aid, $time, $lat, $long, $loc, $oid, $rem) {
		$res = $db->query("select nextval('d_gen_capture_capture_id_seq') id");
		$this->capture_id=$res[0]['id'];
		$db->execute(
			"insert into d_gen_capture (capture_id, animal_id,
				capture_time, latitude, longitude, location,
				operator_id, remark)
				values ($1, $2, $3, $4, $5, $6, $7, $8)",
				array($this->capture_id, $aid,
					$time, $lat, $long, $loc,
					$oid, $rem));
		$this->animal_id = $aid;
		$this->capture_time = $time;
		$this->latitude = $lat;
		$this->longitude = $long;
		$this->location = $loc;
		$this->operator_id = $oid;
		$this->remark = $rem;
		$this->valid_record = true;
		return $this->capture_id;
	}

	function update($db, $cid, $aid, $time, $lat, $long, $loc, $oid, $rem) {
		if (!$this->valid_record or ($cid != $this->capture_id))
			$cid = $this->select($db, $aid, $time);
		if (($lat != $this->latitude) or ($long != $this->longitude) or
			($loc != $this->location) or ($oid != $this->operator_id) or
			($rem != $this->remark)) {
			$db->execute(
				"update d_gen_capture set
					latitude = $2,
					longitude = $3,
					location = $4,
					operator_id = $5,
					remark = $6
					where capture_id = $1",
					array($cid, $lat, $long, $loc, $oid, $rem));
			return 1;
		} else
			return 0;
	}

	function delete($db, $cid) {
		$db->execute(
			"delete from d_gen_capture where capture_id = $1",
			array($cid));
	}

}

?>
