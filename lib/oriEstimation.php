<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class oriEstimation {

	protected $estimation_id;
	protected $phase_id;
	protected $version;
	protected $operator_id;
	protected $activity;
	protected $concentration;
	protected $direction;
	protected $modality;
	protected $act_plus_conc;
	protected $remark;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $pid, $ver) {
		if ($res = $db->query(
				"select estimation_id,phase_id,
				version,operator_id,activity,concentration,
				direction,modality,act_plus_conc,remark
				from d_ori_estimation
				where phase_id = $1
				  and version = $2",
				array($pid, $ver))) {
			$this->estimation_id = $res[0]['estimation_id'];
			$this->phase_id = $res[0]['phase_id'];
			$this->version = $res[0]['version'];
			$this->operator_id = $res[0]['operator_id'];
			$this->activity = $res[0]['activity'];
			$this->concentration = $res[0]['concentration'];
			$this->direction = $res[0]['direction'];
			$this->modality = $res[0]['modality'];
			$this->act_plus_conc = $res[0]['act_plus_conc'];
			$this->remark = $res[0]['remark'];
			$this->valid_record = true;
			return $this->estimation_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $pid, $ver, $oid, $act, $conc,
						$dir, $mod, $apc, $rem) {
		$res = $db->query
				("select nextval('d_ori_estimation_estimation_id_seq') id");
		$this->estimation_id=$res[0]['id'];
		$db->execute(
			"insert into d_ori_estimation (estimation_id, phase_id,
				version, operator_id, activity, concentration,
					direction, modality, act_plus_conc, remark)
				values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)",
				array($this->estimation_id, $pid, $ver, $oid,
						$act, $conc, $dir, $mod, $apc, $rem));
		$this->phase_id = $pid;
		$this->version = $ver;
		$this->operator_id = $oid;
		$this->activity = $act;
		$this->concentration = $conc;
		$this->direction = $dir;
		$this->modality = $mod;
		$this->act_plus_conc = $apc;
		$this->remark = $rem;
		$this->valid_record = true;
		return $this->estimation_id;
	}

	function update($db, $eid, $pid, $ver, $oid,
					$act, $conc, $dir, $mod, $apc, $rem) {
		if (!$this->valid_record or ($eid != $this->estimation_id))
			$eid = $this->select($db, $pid, $ver);
		if (($oid != $this->operator_id) or ($act != $this->activity) or
			($conc != $this->concentration) or
			($dir != $this->direction) or ($mod != $this->modality) or
			($apc != $this->act_plus_conc) or ($rem != $this->remark)) {
			$db->execute(
				"update d_ori_estimation set
					operator_id = $2,
					activity = $3,
					concentration = $4,
					direction = $5,
					modality = $6,
					act_plus_conc = $7,
					remark = $8
					where estimation_id = $1",
					array($eid, $oid,
						$act, $conc, $dir, $mod, $apc, $rem));
			return 1;
		} else
			return 0;
	}

	function delete($db, $eid) {
		$db->execute(
			"delete from d_ori_estimation where estimation_id = $1",
			array($eid));
	}

}

?>
