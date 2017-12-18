<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class genTrackData {

	protected $track_data_id;
	protected $track_id;
	protected $order_no;
	protected $data_id;
	protected $data_value;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $tid, $ordno) {
		if ($res = $db->query(
				"select track_data_id,track_id,order_no,
				data_id,data_value
				from d_gen_track_data
				where track_id = $1
				  and order_no = $2",
				array($tid, $ordno))) {
			$this->track_data_id = $res[0]['track_data_id'];
			$this->track_id = $res[0]['track_id'];
			$this->order_no = $res[0]['order_no'];
			$this->data_id = $res[0]['data_id'];
			$this->data_value = $res[0]['data_value'];
			$this->valid_record = true;
			return $this->track_data_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $tid, $ordno, $datid, $value) {
		$res = $db->query("select nextval('d_gen_track_data_track_data_id_seq') id");
		$this->track_data_id=$res[0]['id'];
		$db->execute(
			"insert into d_gen_track_data (track_data_id, track_id,
				order_no, data_id, data_value)
				values ($1, $2, $3, $4, $5)",
				array($this->track_data_id, $tid,
					$ordno, $datid, $value));
		$this->track_id = $tid;
		$this->order_no = $ordno;
		$this->data_id = $datid;
		$this->data_value = $value;
		$this->valid_record = true;
		return $this->track_data_id;
	}

	function update($db, $tdid, $tid, $ordno, $datid, $value) {
		if (!$this->valid_record or ($tdid != $this->track_data_id))
			$tdid = $this->select($db, $tid, $ordno);
		if ($value != $this->data_value) {
			$db->execute(
				"update d_gen_track_data set
					data_value = $4
					where track_data_id = $1
					  and order_no = $2",
					array($tdid, $ordno, $value));
			return 1;
		} else
			return 0;
	}

	function delete($db, $tdid, $ordno) {
		$db->execute(
			"delete from d_gen_track_data
				where track_data_id = $1
				  and order_no = $2",
			array($tdid, $ordno));
	}

}

?>
