<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class oriAssessment {

	protected $assessment_id;
	protected $animal_id;
	protected $capture_id;
	protected $assessment_no;
	protected $assessment_time;
	protected $operator_id;
	protected $remark;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $aid, $cid, $assno) {
		if ($res = $db->query(
				"select assessment_id,animal_id,capture_id,
				assessment_no,assessment_time,
				operator_id,remark
				from d_ori_assessment
				where animal_id = $1
				  and capture_id = $2
				  and assessment_no = $3",
				array($aid, $cid, $assno))) {
			$this->assessment_id = $res[0]['assessment_id'];
			$this->animal_id = $res[0]['animal_id'];
			$this->capture_id = $res[0]['capture_id'];
			$this->assessment_no = $res[0]['assessment_no'];
			$this->assessment_time = $res[0]['assessment_time'];
			$this->operator_id = $res[0]['operator_id'];
			$this->remark = $res[0]['remark'];
			$this->valid_record = true;
			return $this->assessment_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $aid, $cid, $assno, $asstime, $oid, $rem) {
		$res = $db->query("select nextval('d_ori_assessment_assessment_id_seq') id");
		$this->assessment_id=$res[0]['id'];
		$db->execute(
			"insert into d_ori_assessment (assessment_id, animal_id,
				capture_id, assessment_no, assessment_time,
				operator_id, remark)
				values ($1, $2, $3, $4, $5, $6, $7)",
				array($this->assessment_id, $aid,
					$cid, $assno, $asstime, $oid, $rem));
		$this->animal_id = $aid;
		$this->capture_id = $cid;
		$this->assessment_no = $assno;
		$this->assessment_time = $asstime;
		$this->operator_id = $oid;
		$this->remark = $rem;
		$this->valid_record = true;
		return $this->assessment_id;
	}

	function update($db, $assid, $aid, $cid, $assno, $asstime, $oid, $rem) {
		if (!$this->valid_record or ($assid != $this->assessment_id))
			$assid = $this->select($db, $aid, $cid, $assno);
		if (($asstime != $this->assessment_time) or
		    ($oid != $this->operator_id) or
			($rem != $this->remark)) {
			$db->execute(
				"update d_ori_assessment set
					assessment_time = $2,
					operator_id = $3,
					remark = $4
					where assessment_id = $1",
					array($assid, $asstime, $oid, $rem));
			return 1;
		} else
			return 0;
	}

	function delete($db, $assid) {
		$db->execute(
			"delete from d_ori_assessment where assessment_id = $1",
			array($assid));
	}

}

?>
