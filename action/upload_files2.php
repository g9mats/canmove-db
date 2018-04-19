<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
$data_subset=$_POST['data_subset'];
$data_status=$_POST['data_status'];
$tz=$_POST['tz'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
if ($data_subset=="") {
	echo "<p>You must specify a data subset.</p>";
	return;
}
if ($data_status=="") {
	echo "<p>You must specify data status.</p>";
	return;
}
if ($data_status=="final") {
	if ($tz=="") {
		echo "<p>You must specify a time zone.</p>";
		return;
	}
} else {
	$tz="-";
}
require_once $DBRoot."/lib/DBLink.php";

$sql_exist="
select file_id
from l_file
where dataset_id = $1
  and data_status = $2
  and data_subset = $3
  and original_name = $4
";

$sql_id="
select nextval('l_file_file_id_seq') file_id
";

$sql_ins="
insert into l_file (
	file_id,dataset_id,data_status,data_subset,time_zone,
	original_name,archive_name,upload_time
) values ($1, $2, $3, $4, $5, $6, $7, date_trunc('second',localtimestamp))
";

$sql_upd="
update l_file set
	upload_time = date_trunc('second',localtimestamp),
	registered = false,
	reg_time = null,
	imported = false,
	imp_time = null,
	validated = false,
	val_time = null,
	loaded = false,
	load_time = null,
	deleted = false,
	del_time = null,
	time_zone = $2
where file_id = $1
";

$sql_log="
insert into l_file_log (
	file_id,log_action,log_time
) values ($1, 'U', date_trunc('second',localtimestamp))
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

require $DBRoot."/lib/get_storage_type.php";
$path=$DataRoot."/".$storage_type."/".$dataset_id."/".$data_status;
$temp=umask(2);
if (!is_dir($path))
	mkdir ($path, 0770, true);

foreach ($_FILES["file"]["name"] as $i => $oname) {
	if ($_FILES["file"]["error"][$i] > 0) {
		echo "Error: ".$_FILES["file"]["error"][$i]."<br/>";
	} else {
		if ($res=$db->query($sql_exist,
				array($dataset_id,$data_status,$data_subset,$oname))) {
			$op="update";
		} else {
			$op="insert";
			$res=$db->query($sql_id);
		}
		$file_id=$res[0]['file_id'];
		$lname=$_FILES["file"]["tmp_name"][$i];
		$aname=$data_subset[0].$file_id;
		/*
		echo "i=".$i."<br/>";
		echo "umask=".umask()."<br/>";
		echo "op=".$op."<br/>";
		echo "file_id=".$file_id."<br/>";
		echo "dataset_id=".$dataset_id."<br/>";
		echo "data_status=".$data_status."<br/>";
		echo "data_subset=".$data_subset."<br/>";
		echo "lname=".$lname."<br/>";
		echo "oname=".$oname."<br/>";
		echo "aname=".$aname."<br/>";
		echo "path=".$path."<br/>";
		*/
		if (move_uploaded_file($lname, $path."/".$aname)) {
			chmod ($path."/".$aname, 0660);
			if ($op=="insert") {
				$res=$db->execute($sql_ins,
					array($file_id,$dataset_id,$data_status,$data_subset,
						$tz,$oname,$aname));
				echo "Uploaded new file: ".$oname."<br/>";
			} else {
				$res=$db->execute($sql_upd, array($file_id,$tz));
				echo "Uploaded updated file: ".$oname."<br/>";
			}
			$res=$db->execute($sql_log, array($file_id));
		} else {
				$temp_arr = array();
				$temp_arr = error_get_last();
				echo "Unable to upload file: ".$oname."<br/>";
				echo $temp_arr['message'];
				echo "<br/>";
		}
	}
}

$db->disconnect();
?>
