<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class oriCount {

	protected $count_id;
	protected $phase_id;
	protected $version;
	protected $funnel_line;
	protected $operator_id;
	protected $activity;
	protected $d1;
	protected $s;
	protected $r1;
	protected $p1;
	protected $d2a;
	protected $d2b;
	protected $r2;
	protected $p2;
	protected $direction;
	protected $remark;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $pid, $ver) {
		if ($res = $db->query(
				"select count_id,phase_id,
				version,funnel_line,operator_id,
				activity,d1,s,r1,p1,d2a,d2b,r2,p2,direction,remark
				from d_ori_count
				where phase_id = $1
				  and version = $2",
				array($pid, $ver))) {
			$this->count_id = $res[0]['count_id'];
			$this->phase_id = $res[0]['phase_id'];
			$this->version = $res[0]['version'];
			$this->funnel_line = $res[0]['funnel_line'];
			$this->operator_id = $res[0]['operator_id'];
			$this->activity = $res[0]['activity'];
			$this->d1 = $res[0]['d1'];
			$this->s = $res[0]['s'];
			$this->r1 = $res[0]['r1'];
			$this->p1 = $res[0]['p1'];
			$this->d2a = $res[0]['d2a'];
			$this->d2b = $res[0]['d2b'];
			$this->r2 = $res[0]['r2'];
			$this->p2 = $res[0]['p2'];
			$this->direction = $res[0]['direction'];
			$this->remark = $res[0]['remark'];
			$this->valid_record = true;
			return $this->count_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $pid, $ver, $line, $oid,
					$act, $d1, $s, $r1, $p1, $d2a, $d2b, $r2, $p2, $dir, $rem) {
		$res = $db->query("select nextval('d_ori_count_count_id_seq') id");
		$this->count_id=$res[0]['id'];
		$db->execute(
			"insert into d_ori_count (count_id, phase_id,
				version, funnel_line, operator_id,
					activity, d1, s, r1, p1,
					d2a, d2b, r2, p2, direction, remark)
				values ($1, $2, $3, $4, $5,
					$6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16)",
				array($this->count_id, $pid, $ver, $line, $oid,
					$act, $d1, $s, $r1, $p1, $d2a, $d2b, $r2, $p2, $dir, $rem));
		$this->phase_id = $pid;
		$this->version = $ver;
		$this->funnel_line = $line;
		$this->operator_id = $oid;
		$this->activity = $act;
		$this->d1 = $d1;
		$this->s = $s;
		$this->r1 = $r1;
		$this->p1 = $p1;
		$this->d2a = $d2a;
		$this->d2b = $d2b;
		$this->r2 = $r2;
		$this->p2 = $p2;
		$this->direction = $dir;
		$this->remark = $rem;
		$this->valid_record = true;
		return $this->count_id;
	}

	function update($db, $cid, $pid, $ver, $line, $oid,
					$act, $d1, $s, $r1, $p1, $d2a, $d2b, $r2, $p2, $dir, $rem) {
		if (!$this->valid_record or ($cid != $this->count_id))
			$cid = $this->select($db, $pid, $ver);
		if (($line != $this->funnel_line) or ($oid != $this->operator_id) or
			($act != $this->activity) or ($d1 != $this->d1) or
			($s != $this->s) or ($r1 != $this->r1) or
			($p1 != $this->p1) or ($d2a != $this->d2a) or
			($d2b != $this->d2b) or ($r2 != $this->r2) or
			($p2 != $this->p2) or ($dir != $this->direction) or
			($rem != $this->remark)) {
			$db->execute(
				"update d_ori_count set
					funnel_line = $2,
					operator_id = $3,
					activity = $4,
					d1 = $5,
					s = $6,
					r1 = $7,
					p1 = $8,
					d2a = $9,
					d2b = $10,
					r2 = $11,
					p2 = $12,
					direction = $13,
					remark = $14
					where count_id = $1",
					array($cid, $line, $oid,
						$act, $d1, $s, $r1, $p1, $d2a, $d2b,
						$r2, $p2, $dir, $rem));
			return 1;
		} else
			return 0;
	}

	function delete($db, $cid) {
		$db->execute(
			"delete from d_ori_count where count_id = $1",
			array($cid));
	}

}

?>
