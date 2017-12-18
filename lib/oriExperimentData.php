<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class oriExperimentData {

	protected $experiment_data_id;
	protected $experiment_id;
	protected $order_no;
	protected $data_id;
	protected $data_value;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $eid, $ordno) {
		if ($res = $db->query(
				"select experiment_data_id,experiment_id,order_no,
				data_id,data_value
				from d_ori_experiment_data
				where experiment_id = $1
				  and order_no = $2",
				array($eid, $ordno))) {
			$this->experiment_data_id = $res[0]['experiment_data_id'];
			$this->experiment_id = $res[0]['experiment_id'];
			$this->order_no = $res[0]['order_no'];
			$this->data_id = $res[0]['data_id'];
			$this->data_value = $res[0]['data_value'];
			$this->valid_record = true;
			return $this->experiment_data_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $eid, $ordno, $datid, $value) {
		$res = $db->query("select nextval('d_ori_experiment_data_experiment_data_id_seq') id");
		$this->experiment_data_id=$res[0]['id'];
		$db->execute(
			"insert into d_ori_experiment_data (experiment_data_id, experiment_id,
				order_no, data_id, data_value)
				values ($1, $2, $3, $4, $5)",
				array($this->experiment_data_id, $eid,
					$ordno, $datid, $value));
		$this->experiment_id = $eid;
		$this->order_no = $ordno;
		$this->data_id = $datid;
		$this->data_value = $value;
		$this->valid_record = true;
		return $this->experiment_data_id;
	}

	function update($db, $edid, $eid, $ordno, $datid, $value) {
		if (!$this->valid_record or ($edid != $this->experiment_data_id))
			$edid = $this->select($db, $eid, $ordno);
		if ($value != $this->data_value) {
			$db->execute(
				"update d_ori_experiment_data set
					data_value = $3
					where experiment_data_id = $1
					  and order_no = $2",
					array($edid, $ordno, $value));
			return 1;
		} else
			return 0;
	}

	function delete($db, $edid, $ordno) {
		$db->execute(
			"delete from d_ori_experiment_data
				where experiment_data_id = $1
				  and order_no = $2",
			array($edid, $ordno));
	}

}

?>
