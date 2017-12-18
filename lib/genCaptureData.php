<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class genCaptureData {

	protected $capture_data_id;
	protected $capture_id;
	protected $order_no;
	protected $data_id;
	protected $data_value;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $cid, $ordno) {
		if ($res = $db->query(
				"select capture_data_id,capture_id,order_no,
				data_id,data_value
				from d_gen_capture_data
				where capture_id = $1
				  and order_no = $2",
				array($cid, $ordno))) {
			$this->capture_data_id = $res[0]['capture_data_id'];
			$this->capture_id = $res[0]['capture_id'];
			$this->order_no = $res[0]['order_no'];
			$this->data_id = $res[0]['data_id'];
			$this->data_value = $res[0]['data_value'];
			$this->valid_record = true;
			return $this->capture_data_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $cid, $ordno, $datid, $value) {
		$res = $db->query("select nextval('d_gen_capture_data_capture_data_id_seq') id");
		$this->capture_data_id=$res[0]['id'];
		$db->execute(
			"insert into d_gen_capture_data (capture_data_id, capture_id,
				order_no, data_id, data_value)
				values ($1, $2, $3, $4, $5)",
				array($this->capture_data_id, $cid,
					$ordno, $datid, $value));
		$this->capture_id = $cid;
		$this->order_no = $ordno;
		$this->data_id = $datid;
		$this->data_value = $value;
		$this->valid_record = true;
		return $this->capture_data_id;
	}

	function update($db, $cdid, $cid, $ordno, $datid, $value) {
		if (!$this->valid_record or ($cdid != $this->capture_data_id))
			$cdid = $this->select($db, $cid, $ordno);
		if ($value != $this->data_value) {
			$db->execute(
				"update d_gen_capture_data set
					data_value = $3
					where capture_data_id = $1
					  and order_no = $2",
					array($cdid, $ordno, $value));
			return 1;
		} else
			return 0;
	}

	function delete($db, $cdid, $ordno) {
		$db->execute(
			"delete from d_gen_capture_data
				where capture_data_id = $1
				  and order_no = $2",
			array($cdid, $ordno));
	}

}

?>
