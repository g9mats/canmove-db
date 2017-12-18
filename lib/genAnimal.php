<?php
// Creator: Mats J Svensson, CAnMove

error_reporting(E_ALL);

class genAnimal {

	protected $animal_id;
	protected $dataset_id;
	protected $animal;
	protected $itis_tsn;
	protected $taxon;
	protected $remark;
	protected $valid_record;

	function __construct() {
		$this->valid_record = false;
	}

	function select($db, $did, $tid) {
		if ($res = $db->query(
				"select animal_id,dataset_id,animal,
				itis_tsn,taxon,remark
				from d_gen_animal
				where dataset_id = $1
				  and animal = $2",
				array($did, $tid))) {
			$this->animal_id = $res[0]['animal_id'];
			$this->dataset_id = $res[0]['dataset_id'];
			$this->animal = $res[0]['animal'];
			$this->itis_tsn = $res[0]['itis_tsn'];
			$this->taxon = $res[0]['taxon'];
			$this->remark = $res[0]['remark'];
			$this->valid_record = true;
			return $this->animal_id;
		} else {
			$this->valid_record = false;
			return -1;
		}
	}

	function insert($db, $did, $tid, $tsn, $tax, $rem) {
		$res = $db->query("select nextval('d_gen_animal_animal_id_seq') id");
		$this->animal_id=$res[0]['id'];
		$db->execute(
			"insert into d_gen_animal (animal_id, dataset_id, animal,
				itis_tsn, taxon, remark)
				values ($1, $2, $3, $4, $5, $6)",
				array($this->animal_id, $did, $tid,
					$tsn, $tax, $rem));
		$this->dataset_id = $did;
		$this->animal = $tid;
		$this->itis_tsn = $tsn;
		$this->taxon = $tax;
		$this->remark = $rem;
		$this->valid_record = true;
		return $this->animal_id;
	}

	function update($db, $aid, $did, $tid, $tsn, $tax, $rem) {
		if (!$this->valid_record or ($aid != $this->animal_id))
			$aid = $this->select($db, $did, $tid);
		if (($tsn != $this->itis_tsn) or ($tax != $this->taxon) or
			($rem != $this->remark)) {
			$db->execute(
				"update d_gen_animal set
					itis_tsn = $2,
					taxon = $3,
					remark = $4
					where animal_id = $1",
					array($aid, $tsn, $tax, $rem));
			return 1;
		} else
			return 0;
	}

	function delete($db, $aid) {
		$db->execute(
			"delete from d_gen_animal where animal_id = $1",
			array($aid));
	}

}

?>
