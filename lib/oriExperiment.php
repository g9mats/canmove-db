<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class oriExperiment {

	protected $experiment_id;
	protected $animal_id;
	protected $capture_id;
	protected $setup_id;
	protected $experiment_no;
	protected $experiment_type;
	protected $cage_top_diameter;
	protected $cage_height;
	protected $sensor_type;
	protected $data_processing;
	protected $data_format;
	protected $latitude;
	protected $longitude;
	protected $location;
	protected $operator_id;
	protected $measurement_time;
	protected $remark;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $aid, $cid, $eno) {
		if ($res = $db->query(
				"select experiment_id,animal_id,capture_id,setup_id,
				experiment_no,experiment_type,cage_top_diameter,cage_height,
				sensor_type,data_processing,data_format,
				latitude,longitude,location,operator_id,
				measurement_time,remark
				from d_ori_experiment
				where animal_id = $1
				  and capture_id = $2
				  and experiment_no = $3",
				array($aid, $cid, $eno))) {
			$this->experiment_id = $res[0]['experiment_id'];
			$this->animal_id = $res[0]['animal_id'];
			$this->capture_id = $res[0]['capture_id'];
			$this->setup_id = $res[0]['setup_id'];
			$this->experiment_no = $res[0]['experiment_no'];
			$this->experiment_type = $res[0]['experiment_type'];
			$this->cage_top_diameter = $res[0]['cage_top_diameter'];
			$this->cage_height = $res[0]['cage_height'];
			$this->sensor_type = $res[0]['sensor_type'];
			$this->data_processing = $res[0]['data_processing'];
			$this->data_format = $res[0]['data_format'];
			$this->latitude = $res[0]['latitude'];
			$this->longitude = $res[0]['longitude'];
			$this->location = $res[0]['location'];
			$this->operator_id = $res[0]['operator_id'];
			$this->measurement_time = $res[0]['measurement_time'];
			$this->remark = $res[0]['remark'];
			$this->valid_record = true;
			return $this->experiment_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $aid, $cid, $sid, $eno, $etype,
					$ctop, $cheight, $sens, $dproc, $dform,
					$lat, $long, $loc, $oid, $mtime, $rem) {
		$res = $db->query("select nextval('d_ori_experiment_experiment_id_seq') id");
		$this->experiment_id=$res[0]['id'];
		$db->execute(
			"insert into d_ori_experiment (experiment_id, animal_id,
				capture_id, setup_id, experiment_no, experiment_type,
				cage_top_diameter, cage_height,
				sensor_type, data_processing, data_format,
				latitude, longitude, location, operator_id,
				measurement_time, remark)
				values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12,
						$13, $14, $15, $16, $17)",
				array($this->experiment_id, $aid, $cid, $sid, $eno, $etype,
					$ctop, $cheight, $sens, $dproc, $dform,
					$lat, $long, $loc, $oid, $mtime, $rem));
		$this->animal_id = $aid;
		$this->capture_id = $cid;
		$this->setup_id = $sid;
		$this->experiment_no = $eno;
		$this->experiment_type = $etype;
		$this->cage_top_diameter = $ctop;
		$this->cage_height = $cheight;
		$this->sensor_type = $sens;
		$this->data_processing = $dproc;
		$this->data_format = $dform;
		$this->latitude = $lat;
		$this->longitude = $long;
		$this->location = $loc;
		$this->operator_id = $oid;
		$this->measurement_time = $mtime;
		$this->remark = $rem;
		$this->valid_record = true;
		return $this->experiment_id;
	}

	function update($db, $eid, $aid, $cid, $sid, $eno, $etype,
					$ctop, $cheight, $sens, $dproc, $dform,
					$lat, $long, $loc, $oid, $mtime, $rem) {
		if (!$this->valid_record or ($eid != $this->experiment_id))
			$eid = $this->select($db, $aid, $cid, $eno);
		if (($sid != $this->setup_id) or ($etype != $this->experiment_type) or
			($ctop != $this->cage_top_diameter) or
			($cheight != $this->cage_height) or
			($sens != $this->sensor_type) or
			($dproc != $this->data_processing) or
			($dform != $this->data_format) or
			($lat != $this->latitude) or ($long != $this->longitude) or
			($loc != $this->location) or ($oid != $this->operator_id) or
			($mtime != $this->measurement_time) or ($rem != $this->remark)) {
			$db->execute(
				"update d_ori_experiment set
					setup_id = $2,
					experiment_type = $3,
					cage_top_diameter = $4,
					cage_height = $5,
					sensor_type = $6,
					data_processing = $7,
					data_format = $8,
					latitude = $9,
					longitude = $10,
					location = $11,
					operator_id = $12,
					measurement_time = $13,
					remark = $14
					where experiment_id = $1",
					array($eid, $sid, $etype, $ctop, $cheight, $sens,
						$dproc, $dform, $lat, $long, $loc, $oid,
						$mtime, $rem));
			return 1;
		} else
			return 0;
	}

	function delete($db, $eid) {
		$db->execute(
			"delete from d_ori_experiment where experiment_id = $1",
			array($eid));
	}

}

?>
