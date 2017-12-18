<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class ornSession {

	protected $session_id;
	protected $dataset_id;
	protected $location;
	protected $session_time;
	protected $file_id;
	protected $latitude;
	protected $longitude;
	protected $altitude;
	protected $declination;
	protected $height_datum;
	protected $height_source;
	protected $wind_source;
	protected $anemometer_height;
	protected $taxa_file;
	protected $activity_file;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $did, $loc, $stime) {
		if ($res = $db->query(
				"select session_id,dataset_id,location,session_time,
				file_id,latitude,longitude,altitude,declination,
				height_datum,height_source,wind_source,anemometer_height,
				taxa_file,activity_file
				from d_orn_session
				where dataset_id = $1
				  and location = $2
				  and session_time = $3",
				array($did, $loc, $stime))) {
			$this->session_id = $res[0]['session_id'];
			$this->dataset_id = $res[0]['dataset_id'];
			$this->location = $res[0]['location'];
			$this->session_time = $res[0]['session_time'];
			$this->file_id = $res[0]['file_id'];
			$this->latitude = $res[0]['latitude'];
			$this->longitude = $res[0]['longitude'];
			$this->altitude = $res[0]['altitude'];
			$this->declination = $res[0]['declination'];
			$this->height_datum = $res[0]['height_datum'];
			$this->height_source = $res[0]['height_source'];
			$this->wind_source = $res[0]['wind_source'];
			$this->anemometer_height = $res[0]['anemometer_height'];
			$this->taxa_file = $res[0]['taxa_file'];
			$this->activity_file = $res[0]['activity_file'];
			$this->valid_record = true;
			return $this->session_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $did, $loc, $stime, $fid, $lat, $long, $alt, $decl,
					$hdat, $hsrc, $wsrc, $ane, $tfile, $afile) {
		$res = $db->query("select nextval('d_orn_session_session_id_seq') id");
		$this->session_id=$res[0]['id'];
		$db->execute(
			"insert into d_orn_session (session_id, dataset_id, location,
				session_time, file_id, latitude, longitude, altitude,
				declination, height_datum, height_source, wind_source,
				anemometer_height, taxa_file, activity_file)
				values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10,
						$11, $12, $13, $14, $15)",
				array($this->session_id, $did, $loc, $stime,
					$fid, $lat, $long, $alt, $decl, $hdat, $hsrc,
					$wsrc, $ane, $tfile, $afile));
		$this->dataset_id = $did;
		$this->location = $loc;
		$this->session_time = $stime;
		$this->file_id = $fid;
		$this->latitude = $lat;
		$this->longitude = $long;
		$this->altitude = $alt;
		$this->declination = $decl;
		$this->height_datum = $hdat;
		$this->height_source = $hsrc;
		$this->wind_source = $wsrc;
		$this->anemometer_height = $ane;
		$this->taxa_file = $tfile;
		$this->activity_file = $afile;
		$this->valid_record = true;
		return $this->session_id;
	}

	function update($db, $sid, $did, $loc, $stime,
					$fid, $lat, $long, $alt, $decl, $hdat, $hsrc,
					$wsrc, $ane, $tfile, $afile) {
		if (!$this->valid_record or ($sid != $this->session_id))
			$sid = $this->select($db, $did, $loc, $stime);
		if (($fid != $this->file_id) or ($lat != $this->latitude) or
			($long != $this->longitude) or ($alt != $this->altitude) or
			($decl != $this->declination) or ($hdat != $this->height_datum) or
			($hsrc != $this->height_source) or ($wsrc != $this->wind_source) or
			($ane != $this->anemometer_height) or
			($tfile != $this->taxa_file) or
			($afile != $this->activity_file)) {
			$db->execute(
				"update d_orn_session set
					file_id = $2,
					latitude = $3,
					longitude = $4,
					altitude = $5,
					declination = $6,
					height_datum = $7,
					height_source = $8,
					wind_source = $9,
					anemometer_height = $10,
					taxa_file = $11,
					activity_file = $12
					where session_id = $1",
					array($sid, $fid, $lat, $long, $alt, $decl,
						$hdat, $hsrc, $wsrc, $ane, $tfile, $afile));
			return 1;
		} else
			return 0;
	}

	function delete($db, $sid) {
		$db->execute(
			"delete from d_orn_session where session_id = $1",
			array($sid));
	}

}

?>
