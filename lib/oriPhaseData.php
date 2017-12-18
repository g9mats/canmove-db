<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class oriPhaseData {

	protected $phase_data_id;
	protected $phase_id;
	protected $order_no;
	protected $data_id;
	protected $data_value;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $pid, $ordno) {
		if ($res = $db->query(
				"select phase_data_id,phase_id,order_no,
				data_id,data_value
				from d_ori_phase_data
				where phase_id = $1
				  and order_no = $2",
				array($pid, $ordno))) {
			$this->phase_data_id = $res[0]['phase_data_id'];
			$this->phase_id = $res[0]['phase_id'];
			$this->order_no = $res[0]['order_no'];
			$this->data_id = $res[0]['data_id'];
			$this->data_value = $res[0]['data_value'];
			$this->valid_record = true;
			return $this->phase_data_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $pid, $ordno, $datid, $value) {
		$res = $db->query("select nextval('d_ori_phase_data_phase_data_id_seq') id");
		$this->phase_data_id=$res[0]['id'];
		$db->execute(
			"insert into d_ori_phase_data (phase_data_id, phase_id,
				order_no, data_id, data_value)
				values ($1, $2, $3, $4, $5)",
				array($this->phase_data_id, $pid,
					$ordno, $datid, $value));
		$this->phase_id = $pid;
		$this->order_no = $ordno;
		$this->data_id = $datid;
		$this->data_value = $value;
		$this->valid_record = true;
		return $this->phase_data_id;
	}

	function update($db, $pdid, $pid, $ordno, $datid, $value) {
		if (!$this->valid_record or ($pdid != $this->phase_data_id))
			$pdid = $this->select($db, $pid, $ordno);
		if ($value != $this->data_value) {
			$db->execute(
				"update d_ori_phase_data set
					data_value = $3
					where phase_data_id = $1
					  and order_no = $2",
					array($pdid, $ordno, $value));
			return 1;
		} else
			return 0;
	}

	function delete($db, $pdid, $ordno) {
		$db->execute(
			"delete from d_ori_phase_data
				where phase_data_id = $1
				  and order_no = $2",
			array($pdid, $ordno));
	}

}

?>
