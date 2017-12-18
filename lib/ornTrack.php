<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class ornTrack {

	protected $track_id;
	protected $session_id;
	protected $track_no;
	protected $start_time;
	protected $itis_tsn;
	protected $taxon;
	protected $species_no;
	protected $english_name;
	protected $swedish_name;
	protected $wind_direction;
	protected $wind_speed;
	protected $barometer;
	protected $temperature;
	protected $quantity;
	protected $sex;
	protected $age;
	protected $crop;
	protected $flight_style;
	protected $activity;
	protected $remark;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $sid, $tno) {
		if ($res = $db->query(
				"select track_id,session_id,track_no,start_time,
				itis_tsn,taxon,species_no,english_name,swedish_name,
				wind_direction,wind_speed,barometer,temperature,
				quantity,sex,age,crop,flight_style,activity,remark
				from d_orn_track
				where session_id = $1
				  and track_no = $2",
				array($sid, $tno))) {
			$this->track_id = $res[0]['track_id'];
			$this->session_id = $res[0]['session_id'];
			$this->track_no = $res[0]['track_no'];
			$this->start_time = $res[0]['start_time'];
			$this->itis_tsn = $res[0]['itis_tsn'];
			$this->taxon = $res[0]['taxon'];
			$this->species_no = $res[0]['species_no'];
			$this->english_name = $res[0]['english_name'];
			$this->swedish_name = $res[0]['swedish_name'];
			$this->wind_direction = $res[0]['wind_direction'];
			$this->wind_speed = $res[0]['wind_speed'];
			$this->barometer = $res[0]['barometer'];
			$this->temperature = $res[0]['temperature'];
			$this->quantity = $res[0]['quantity'];
			$this->sex = $res[0]['sex'];
			$this->age = $res[0]['age'];
			$this->crop = $res[0]['crop'];
			$this->flight_style = $res[0]['flight_style'];
			$this->activity = $res[0]['activity'];
			$this->remark = $res[0]['remark'];
			$this->valid_record = true;
			return $this->track_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $sid, $tno, $stime, $tsn, $tax, $sno, $ename, $sname,
					$wdir, $wspd, $bar, $temp, $qua, $sex, $age,
					$crop, $fstyle, $act, $rem) {
		$res = $db->query("select nextval('d_orn_track_track_id_seq') id");
		$this->track_id=$res[0]['id'];
		$db->execute(
			"insert into d_orn_track (track_id, session_id, track_no,
				start_time, itis_tsn, taxon,
				species_no, english_name, swedish_name,
				wind_direction, wind_speed, barometer, temperature,
				quantity, sex, age, crop, flight_style, activity, remark)
				values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10,
						$11, $12, $13, $14, $15, $16, $17, $18, $19, $20)",
				array($this->track_id, $sid, $tno, $stime, $tsn, $tax,
					$sno, $ename, $sname, $wdir, $wspd, $bar, $temp, $qua,
					$sex, $age, $crop, $fstyle, $act, $rem));
		$this->session_id = $sid;
		$this->track_no = $tno;
		$this->start_time = $stime;
		$this->itis_tsn = $tsn;
		$this->taxon = $tax;
		$this->species_no = $sno;
		$this->english_name = $ename;
		$this->swedish_name = $sname;
		$this->wind_direction = $wdir;
		$this->wind_speed = $wspd;
		$this->barometer = $bar;
		$this->temperature = $temp;
		$this->quantity = $qua;
		$this->sex = $sex;
		$this->age = $age;
		$this->crop = $crop;
		$this->flight_style = $fstyle;
		$this->activity = $act;
		$this->remark = $rem;
		$this->valid_record = true;
		return $this->track_id;
	}

	function update($db, $tid, $sid, $tno, $stime, $tsn, $tax,
					$sno, $ename, $sname, $wdir, $wspd, $bar, $temp, $qua,
					$sex, $age, $crop, $fstyle, $act, $rem) {
		if (!$this->valid_record or ($tid != $this->track_id))
			$tid = $this->select($db, $sid, $tno);
		if (($stime != $this->start_time) or ($tsn != $this->itis_tsn) or
			($tax != $this->taxon) or ($sno != $this->species_no) or
			($ename != $this->english_name) or
			($sname != $this->swedish_name) or
			($wdir != $this->wind_direction) or ($wspd != $this->wind_speed) or
			($bar != $this->barometer) or ($temp != $this->temperature) or
			($qua != $this->quantity) or ($sex != $this->sex) or
			($age != $this->age) or ($crop != $this->crop) or
			($fstyle != $this->flight_style) or ($act != $this->activity) or
			($rem != $this->remark)) {
			$db->execute(
				"update d_orn_track set
					start_time = $2,
					itis_tsn = $3,
					taxon = $4,
					species_no = $5,
					english_name = $6,
					swedish_name = $7,
					wind_direction = $8,
					wind_speed = $9,
					barometer = $10,
					temperature = $11,
					quantity = $12,
					sex = $13,
					age = $14,
					crop = $15,
					flight_style = $16,
					activity = $17,
					remark = $18
					where track_id = $1",
					array($tid, $stime, $tsn, $tax, $sno, $ename, $sname,
						$wdir, $wspd, $bar, $temp, $qua, $sex, $age, $crop,
						$fstyle, $act, $rem));
			return 1;
		} else
			return 0;
	}

	function delete($db, $tid) {
		$db->execute(
			"delete from d_orn_track where track_id = $1",
			array($tid));
	}

}

?>
