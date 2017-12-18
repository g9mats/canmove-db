<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class ornLocation {

	protected $location_id;
	protected $location;
	protected $latitude;
	protected $longitude;
	protected $altitude;
	protected $declination;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $loc) {
		if ($res = $db->query(
				"select location_id,location,latitude,longitude,
				altitude,declination
				from r_orn_location
				where location = $1",
				array($loc))) {
			$this->location_id = $res[0]['location_id'];
			$this->location = $res[0]['location'];
			$this->latitude = $res[0]['latitude'];
			$this->longitude = $res[0]['longitude'];
			$this->altitude = $res[0]['altitude'];
			$this->declination = $res[0]['declination'];
			$this->valid_record = true;
			return $this->location_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $loc, $lat, $long, $alt, $decl) {
		$res = $db->query("select nextval('r_orn_location_location_id_seq') id");
		$this->location_id=$res[0]['id'];
		$db->execute(
			"insert into r_orn_location (location_id, location,
				latitude, longitude, altitude, declination)
				values ($1, $2, $3, $4, $5, $6)",
				array($this->location_id, $loc, $lat, $long,
					$alt, $decl));
		$this->location = $loc;
		$this->latitude = $lat;
		$this->longitude = $long;
		$this->altitude = $alt;
		$this->declination = $decl;
		$this->valid_record = true;
		return $this->location_id;
	}

	function update($db, $lid, $loc, $lat, $long, $alt, $decl) {
		if (!$this->valid_record or ($lid != $this->location_id))
			$lid = $this->select($db, $loc);
		if (($lat != $this->latitude) or ($long != $this->longitude) or
			($alt != $this->altitude) or ($decl != $this->declination)) {
			$db->execute(
				"update r_orn_location set
					latitude = $2,
					longitude = $3,
					altitude = $4,
					declination = $5
					where location_id = $1",
					array($lid, $lat, $long, $alt, $decl));
			return 1;
		} else
			return 0;
	}

	function delete($db, $lid) {
		$db->execute(
			"delete from r_orn_location where location_id = $1",
			array($lid));
	}

}

?>
