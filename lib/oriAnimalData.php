<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class oriAnimalData {

	protected $animal_data_id;
	protected $animal_id;
	protected $order_no;
	protected $data_id;
	protected $data_value;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $aid, $ordno) {
		if ($res = $db->query(
				"select animal_data_id,animal_id,order_no,
				data_id,data_value
				from d_ori_animal_data
				where animal_id = $1
				  and order_no = $2",
				array($aid, $ordno))) {
			$this->animal_data_id = $res[0]['animal_data_id'];
			$this->animal_id = $res[0]['animal_id'];
			$this->order_no = $res[0]['order_no'];
			$this->data_id = $res[0]['data_id'];
			$this->data_value = $res[0]['data_value'];
			$this->valid_record = true;
			return $this->animal_data_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $aid, $ordno, $datid, $value) {
		$res = $db->query("select nextval('d_ori_animal_data_animal_data_id_seq') id");
		$this->animal_data_id=$res[0]['id'];
		$db->execute(
			"insert into d_ori_animal_data (animal_data_id, animal_id,
				order_no, data_id, data_value)
				values ($1, $2, $3, $4, $5)",
				array($this->animal_data_id, $aid,
					$ordno, $datid, $value));
		$this->animal_id = $aid;
		$this->order_no = $ordno;
		$this->data_id = $datid;
		$this->data_value = $value;
		$this->valid_record = true;
		return $this->animal_data_id;
	}

	function update($db, $adid, $aid, $ordno, $datid, $value) {
		if (!$this->valid_record or ($adid != $this->animal_data_id))
			$adid = $this->select($db, $aid, $ordno);
		if ($value != $this->data_value) {
			$db->execute(
				"update d_ori_animal_data set
					data_value = $3
					where animal_data_id = $1
					  and order_no = $2",
					array($adid, $ordno, $value));
			return 1;
		} else
			return 0;
	}

	function delete($db, $adid, $ordno) {
		$db->execute(
			"delete from d_ori_animal_data
				where animal_data_id = $1
				  and order_no = $2",
			array($adid, $ordno));
	}

}

?>
