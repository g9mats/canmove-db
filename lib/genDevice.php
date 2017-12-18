<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class genDevice {

	protected $device_id;
	protected $track_id;
	protected $parent_id;
	protected $device;
	protected $device_model_id;
	protected $device_attachment;
	protected $order_no;
	protected $start_time;
	protected $end_time;
	protected $remark;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $tid, $dev) {
		if ($res = $db->query(
				"select device_id,track_id,parent_id,device,device_model_id,
				device_attachment,order_no,start_time,end_time,remark
				from d_gen_device
				where track_id = $1
				  and device = $2
				  and order_no = $3",
				array($tid, $dev, $ordno))) {
			$this->device_id = $res[0]['device_id'];
			$this->track_id = $res[0]['track_id'];
			$this->parent_id = $res[0]['parent_id'];
			$this->device = $res[0]['device'];
			$this->device_model_id = $res[0]['device_model_id'];
			$this->device_attachment = $res[0]['device_attachment'];
			$this->order_no = $res[0]['order_no'];
			$this->start_time = $res[0]['start_time'];
			$this->end_time = $res[0]['end_time'];
			$this->remark = $res[0]['remark'];
			$this->valid_record = true;
			return $this->device_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $tid, $dev, $ordno) {
		$res = $db->query("select nextval('d_gen_device_device_id_seq') id");
		$this->device_id=$res[0]['id'];
		$db->execute(
			"insert into d_gen_device (device_id, track_id, device, order_no)
				values ($1, $2, $3, $4)",
				array($this->device_id, $tid, $dev, $ordno));
		$this->track_id = $tid;
		$this->parent_id = null;
		$this->device = $dev;
		$this->device_model_id = null;
		$this->device_attachment = null;
		$this->order_no = $ordno;
		$this->start_time = null;
		$this->end_time = null;
		$this->remark = null;
		$this->valid_record = true;
		return $this->device_id;
	}

	function update($db, $did, $tid, $dev, $pid, $devmid, $devatt,
						$stim, $etim, $rem) {
		if (!$this->valid_record or ($did != $this->device_id))
			$did = $this->select($db, $tid, $dev);
		if (($pid != $this->parent_id) || ($devmid != $this->device_model_id) ||
			($devatt != $this->device_attachment) ||
			($stim != $this->start_time) || ($etim != $this->end_time) ||
			($rem != $this->remark)) {
			$db->execute(
				"update d_gen_device set
					parent_id = $2,
					device_model_id = $3,
					device_attachment = $4,
					start_time = $5,
					end_time = $6,
					remark = $7
					where device_id = $1",
					array($did, $pid, $devmid, $devatt, $stim, $etim, $rem));
			return 1;
		} else
			return 0;
	}

	function select_att($db, $tid, $ordno, $att) {
		if ($res = $db->query(
				"select ".$att."
				from d_gen_device
				where track_id = $1
				  and order_no = $2",
				array($tid, $ordno))) {
			return $res[0][$att];
		} else {
			return "";
		}
	}

	function update_att($db, $tid, $ordno, $att, $val) {
		$old_val = $this->select_att($db, $tid, $ordno, $att);
		if ($val != $old_val) {
			$db->execute(
				"update d_gen_device set
					".$att." = $3
					where track_id = $1
					  and order_no = $2",
					array($tid, $ordno, $val));
			return 1;
		} else
			return 0;
	}

	function delete($db, $did) {
		$db->execute(
			"delete from d_gen_device where device_id = $1",
			array($did));
	}

}

?>
