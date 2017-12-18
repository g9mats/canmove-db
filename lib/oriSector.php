<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class oriSector {

	protected $sector_id;
	protected $count_id;
	protected $sector;
	protected $amount;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $cid, $sec) {
		if ($res = $db->query(
				"select sector_id,count_id,sector,amount
				from d_ori_sector
				where count_id = $1
				  and sector = $2",
				array($cid, $sec))) {
			$this->sector_id = $res[0]['sector_id'];
			$this->count_id = $res[0]['count_id'];
			$this->sector = $res[0]['sector'];
			$this->amount = $res[0]['amount'];
			$this->valid_record = true;
			return $this->sector_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $cid, $sec, $amount) {
		$res = $db->query("select nextval('d_ori_sector_sector_id_seq') id");
		$this->sector_id=$res[0]['id'];
		$db->execute(
			"insert into d_ori_sector (sector_id, count_id, sector, amount)
				values ($1, $2, $3, $4)",
				array($this->sector_id, $cid, $sec, $amount));
		$this->count_id = $cid;
		$this->sector = $sec;
		$this->amount = $amount;
		$this->valid_record = true;
		return $this->sector_id;
	}

	function update($db, $sid, $cid, $sec, $amount) {
		if (!$this->valid_record or ($sid != $this->sector_id))
			$sid = $this->select($db, $cid, $sec);
		if ($amount != $this->amount) {
			$db->execute(
				"update d_ori_sector set
					amount = $2
					where sector_id = $1",
					array($sid, $amount));
			return 1;
		} else
			return 0;
	}

	function delete($db, $sid) {
		$db->execute(
			"delete from d_ori_sector where sector_id = $1",
			array($sid));
	}

}

?>
