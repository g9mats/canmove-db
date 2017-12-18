<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class ornWindData {

	protected $wind_data_id;
	protected $wind_profile_id;
	protected $wind_data_no;
	protected $z_height;
	protected $x_speed;
	protected $y_speed;
	protected $wind_direction;
	protected $wind_speed;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $wpid, $wdno) {
		if ($res = $db->query(
				"select wind_data_id,wind_profile_id,wind_data_no,
				z_height,x_speed,y_speed,wind_direction,wind_speed
				from d_orn_wind_data
				where wind_profile_id = $1
				  and wind_data_no = $2",
				array($wpid, $wdno))) {
			$this->wind_data_id = $res[0]['wind_data_id'];
			$this->wind_profile_id = $res[0]['wind_profile_id'];
			$this->wind_data_no = $res[0]['wind_data_no'];
			$this->z_height = $res[0]['z_height'];
			$this->x_speed = $res[0]['x_speed'];
			$this->y_speed = $res[0]['y_speed'];
			$this->wind_direction = $res[0]['wind_direction'];
			$this->wind_speed = $res[0]['wind_speed'];
			$this->valid_record = true;
			return $this->wind_data_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $wpid, $wdno, $z, $x, $y, $wdir, $wspeed) {
		$res = $db->query("select nextval('d_orn_wind_data_wind_data_id_seq') id");
		$this->wind_data_id=$res[0]['id'];
		$db->execute(
			"insert into d_orn_wind_data (wind_data_id, wind_profile_id,
				wind_data_no, z_height, x_speed, y_speed,
				wind_direction, wind_speed)
				values ($1, $2, $3, $4, $5, $6, $7, $8)",
				array($this->wind_data_id, $wpid, $wdno,
					$z, $x, $y, $wdir, $wspeed));
		$this->wind_profile_id = $wpid;
		$this->wind_data_no = $wdno;
		$this->z_height = $z;
		$this->x_speed = $x;
		$this->y_speed = $y;
		$this->wind_direction = $wdir;
		$this->wind_speed = $wspeed;
		$this->valid_record = true;
		return $this->wind_data_id;
	}

	function update($db, $wdid, $wpid, $wdno, $z, $x, $y, $wdir, $wspeed) {
		if (!$this->valid_record or ($wdid != $this->wind_data_id))
			$wdid = $this->select($db, $wpid, $wdno);
		if (($z != $this->z_height) or ($x != $this->x_speed) or
			($y != $this->y_speed) or ($wdir != $this->wind_direction) or
			($wspeed != $this->wind_speed)) {
			$db->execute(
				"update d_orn_wind_data set
					z_height = $2,
					x_speed = $3,
					y_speed = $4,
					wind_direction = $5,
					wind_speed = $6
					where wind_data_id = $1",
					array($wdid, $z, $x, $y, $wdir, $wspeed));
			return 1;
		} else
			return 0;
	}

	function delete($db, $wdid) {
		$db->execute(
			"delete from d_orn_wind_data where wind_data_id = $1",
			array($tpid));
	}

}

?>
