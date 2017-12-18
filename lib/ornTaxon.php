<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class ornTaxon {

	protected $species_no;
	protected $taxon;
	protected $english_name;
	protected $swedish_name;
	protected $remark;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $sno) {
		if ($res = $db->query(
				"select species_no,taxon,
				english_name,swedish_name,remark
				from r_orn_taxon
				where species_no = $1",
				array($sno))) {
			$this->species_no = $res[0]['species_no'];
			$this->taxon = $res[0]['taxon'];
			$this->english_name = $res[0]['english_name'];
			$this->swedish_name = $res[0]['swedish_name'];
			$this->remark = $res[0]['remark'];
			$this->valid_record = true;
			return $this->species_no;
		} else {
			$this->valid_record = false;
			// return 0 since -1 is used for identifying balloon
			return 0;
		}
	}

	function insert($db, $sno, $tax, $ename, $sname, $rem) {
		$db->execute(
			"insert into r_orn_taxon (species_no, taxon,
				english_name, swedish_name, remark)
				values ($1, $2, $3, $4, $5)",
				array($sno, $tax,
					$ename, $sname, $rem));
		$this->species_no = $sno;
		$this->taxon = $tax;
		$this->english_name = $ename;
		$this->swedish_name = $sname;
		$this->remark = $rem;
		$this->valid_record = true;
		return $this->species_no;
	}

	function update($db, $sno, $tax, $ename, $sname, $rem) {
		if (!$this->valid_record or ($sno != $this->species_no))
			$sno = $this->select($db, $sno);
		if (($tax != $this->taxon) or ($ename != $this->english_name) or
			($sname != $this->swedish_name) or ($rem != $this->remark)) {
			$db->execute(
				"update r_orn_taxon set
					taxon = $2,
					english_name = $3,
					swedish_name = $4,
					remark = $5
					where species_no = $1",
					array($sno, $tax, $ename, $sname, $rem));
			return 1;
		} else
			return 0;
	}

	function delete($db, $sno) {
		$db->execute(
			"delete from r_orn_taxon where species_no = $1",
			array($sno));
	}

}

?>
