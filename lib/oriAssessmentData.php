<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class oriAssessmentData {

	protected $assessment_data_id;
	protected $assessment_id;
	protected $order_no;
	protected $data_id;
	protected $data_value;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $assid, $ordno) {
		if ($res = $db->query(
				"select assessment_data_id,assessment_id,order_no,
				data_id,data_value
				from d_ori_assessment_data
				where assessment_id = $1
				  and order_no = $2",
				array($assid, $ordno))) {
			$this->assessment_data_id = $res[0]['assessment_data_id'];
			$this->assessment_id = $res[0]['assessment_id'];
			$this->order_no = $res[0]['order_no'];
			$this->data_id = $res[0]['data_id'];
			$this->data_value = $res[0]['data_value'];
			$this->valid_record = true;
			return $this->assessment_data_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $assid, $ordno, $datid, $value) {
		$res = $db->query("select nextval('d_ori_assessment_data_assessment_data_id_seq') id");
		$this->assessment_data_id=$res[0]['id'];
		$db->execute(
			"insert into d_ori_assessment_data (assessment_data_id, assessment_id,
				order_no, data_id, data_value)
				values ($1, $2, $3, $4, $5)",
				array($this->assessment_data_id, $assid,
					$ordno, $datid, $value));
		$this->assessment_id = $assid;
		$this->order_no = $ordno;
		$this->data_id = $datid;
		$this->data_value = $value;
		$this->valid_record = true;
		return $this->assessment_data_id;
	}

	function update($db, $edid, $assid, $ordno, $datid, $value) {
		if (!$this->valid_record or ($edid != $this->assessment_data_id))
			$edid = $this->select($db, $assid, $ordno);
		if ($value != $this->data_value) {
			$db->execute(
				"update d_ori_assessment_data set
					data_value = $3
					where assessment_data_id = $1
					  and order_no = $2",
					array($edid, $ordno, $value));
			return 1;
		} else
			return 0;
	}

	function delete($db, $edid, $ordno) {
		$db->execute(
			"delete from d_ori_assessment_data
				where assessment_data_id = $1
				  and order_no = $2",
			array($edid, $ordno));
	}

}

?>
