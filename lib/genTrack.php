<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class genTrack {

	protected $track_id;
	protected $animal_id;
	protected $start_capture_id;
	protected $end_capture_id;
	protected $start_time;
	protected $end_time;
	protected $remark;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $aid, $scid) {
		if ($res = $db->query(
				"select track_id,animal_id,start_capture_id,end_capture_id,
				start_time,end_time,remark
				from d_gen_track
				where animal_id = $1
				  and start_capture_id = $2",
				array($aid, $scid))) {
			$this->track_id = $res[0]['track_id'];
			$this->animal_id = $res[0]['animal_id'];
			$this->start_capture_id = $res[0]['start_capture_id'];
			$this->end_capture_id = $res[0]['end_capture_id'];
			$this->start_time = $res[0]['start_time'];
			$this->end_time = $res[0]['end_time'];
			$this->remark = $res[0]['remark'];
			$this->valid_record = true;
			return $this->track_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $aid, $scid, $rem) {
		$res = $db->query("select nextval('d_gen_track_track_id_seq') id");
		$this->track_id=$res[0]['id'];
		$db->execute(
			"insert into d_gen_track (track_id, animal_id, start_capture_id,
				remark)
				values ($1, $2, $3, $4)",
				array($this->track_id, $aid, $scid, $rem));
		$this->animal_id = $aid;
		$this->start_capture_id = $scid;
		$this->remark = $rem;
		$this->valid_record = true;
		return $this->track_id;
	}

	function update_capture($db, $tid, $ecid) {
		if ($res = $db->query("select end_capture_id from d_gen_track
							where track_id = $1",
							array($tid))) {
			$this->end_capture_id = $res[0]['end_capture_id'];
			if ($ecid != $this->end_capture_id) {
				$db->execute(
					"update d_gen_track set
						end_capture_id = $2
						where track_id = $1",
						array($tid, $ecid));
				return 1;
			}
		}
		return 0;
	}

	function update($db, $tid, $aid, $scid, $rem) {
		if (!$this->valid_record or ($tid != $this->track_id))
			$tid = $this->select($db, $aid, $scid);
		if ($rem != $this->remark) {
			$db->execute(
				"update d_gen_track set
					remark = $2
					where track_id = $1",
					array($tid, $rem));
			return 1;
		} else
			return 0;
	}

	function delete($db, $tid) {
		$db->execute(
			"delete from d_gen_track where track_id = $1",
			array($tid));
	}

}

?>
