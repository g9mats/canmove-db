<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class oriPhase {

	protected $phase_id;
	protected $experiment_id;
	protected $phase_no;
	protected $start_time;
	protected $end_time;
	protected $middle_time;
	protected $remark;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $eid, $pno) {
		if ($res = $db->query(
				"select phase_id,experiment_id,phase_no,
				start_time,end_time,middle_time,remark
				from d_ori_phase
				where experiment_id = $1
				  and phase_no = $2",
				array($eid, $pno))) {
			$this->phase_id = $res[0]['phase_id'];
			$this->experiment_id = $res[0]['experiment_id'];
			$this->phase_no = $res[0]['phase_no'];
			$this->start_time = $res[0]['start_time'];
			$this->end_time = $res[0]['end_time'];
			$this->middle_time = $res[0]['middle_time'];
			$this->remark = $res[0]['remark'];
			$this->valid_record = true;
			return $this->phase_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $eid, $pno, $stime, $etime, $mtime, $rem) {
		$res = $db->query("select nextval('d_ori_phase_phase_id_seq') id");
		$this->phase_id=$res[0]['id'];
		$db->execute(
			"insert into d_ori_phase (phase_id, experiment_id, phase_no,
				start_time, end_time, middle_time, remark)
				values ($1, $2, $3, $4, $5, $6, $7)",
				array($this->phase_id, $eid, $pno,
					$stime, $etime, $mtime, $rem));
		$this->experiment_id = $eid;
		$this->phase_no = $pno;
		$this->start_time = $stime;
		$this->end_time = $etime;
		$this->middle_time = $mtime;
		$this->remark = $rem;
		$this->valid_record = true;
		return $this->phase_id;
	}

	function update($db, $pid, $eid, $pno, $stime, $etime, $mtime, $rem) {
		if (!$this->valid_record or ($pid != $this->phase_id))
			$pid = $this->select($db, $eid, $pno);
		if (($stime != $this->start_time) or ($etime != $this->end_time) or
			($mtime != $this->middle_time) or ($rem != $this->remark)) {
			$db->execute(
				"update d_ori_phase set
					start_time = $2,
					end_time = $3,
					middle_time = $4,
					remark = $5
					where phase_id = $1",
					array($pid, $stime, $etime, $mtime, $rem));
			return 1;
		} else
			return 0;
	}

	function delete($db, $pid) {
		$db->execute(
			"delete from d_ori_phase where phase_id = $1",
			array($eid));
	}

}

?>
