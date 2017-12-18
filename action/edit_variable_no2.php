<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$storage_type=$_POST['storage_type'];
$data_subset=$_POST['data_subset'];
require_once $DBRoot."/lib/DBLink.php";

// SQL: select storage type and data subset names
$sql_name="
select
	s.storage_name,
	d.subset_name
from r_storage_type s, r_data_subset d
where s.storage_type = d.storage_type
  and s.storage_type = $1
  and d.data_subset = $2
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

if ($res=$db->query($sql_name, array($storage_type,$data_subset)))
	$storage_name=$res[0]['storage_name'];
	$subset_name=$res[0]['subset_name'];
?>

<h3><?php echo $storage_name." ".$subset_name; ?> Variables</h3>

<div id="vars"></div>

<script>

var path="<?php echo $WebRoot; ?>/db/action/edit_variable_no_";
var storage_type="<?php echo $storage_type; ?>";
var data_subset="<?php echo $data_subset; ?>";

function select_vars() {
	var tab=document.getElementById("vars");
	var txt="";

	http=new XMLHttpRequest();
	http.open("POST", path+"select.php", false);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.send("storage_type="+storage_type+"&data_subset="+data_subset);
	var res=JSON.parse(http.responseText);
	txt="<table><tr><th>Object</th><th>Mandatory</th><th>Header</th><th>Order No</th><th>";
	txt+="<button onclick='update_vars()'>Save</button>";
	txt+="</th></tr>";
	for (i=0; i<res.vararr.length; i++) {
		txt+="<tr>";
		txt+="<td>"+res.vararr[i].table_header+"</td>";
		txt+="<td>"+res.vararr[i].mandatory+"</td>";
		txt+="<td>"+res.vararr[i].header+"</td>";
		txt+="<td><input id='n"+i+"' value='";
		if (res.vararr[i].order_no != null)
			txt+=res.vararr[i].order_no;
		txt+="' size='5'></input></td>";
		txt+="<td><button onclick='update_var("+i+",false)'>Save</button></td>";
		txt+="<td id='i"+i+"' style='visibility:hidden'>"+res.vararr[i].data_id+"</td>";
		txt+="<td id='o"+i+"' style='visibility:hidden'>";
		if (res.vararr[i].order_no != null)
			txt+=res.vararr[i].order_no;
		txt+="</td>";
		txt+="</tr>";
	}
	txt+="</table>";
	tab.innerHTML = txt;
}

function update_vars () {
	var rows=document.getElementsByTagName("TR").length-1;
	var order_no,old_no,i,msg;

	for (i=0; i<rows; i++) {
		order_no=document.getElementById("n"+i);
		old_no=document.getElementById("o"+i);
		if (order_no !== old_no) {
			msg=update_var(i,true);
			if (msg!="") {
				alert (res.errmsg);
				break;
			}
		}
	}
	select_vars();
}

function update_var (i,all) {
	var data_id=document.getElementById("i"+i);
	var order_no=document.getElementById("n"+i);

	http=new XMLHttpRequest();
	http.open("POST", path+"update.php", false);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.send("data_id="+data_id.innerHTML+"&order_no="+order_no.value);
	var res=JSON.parse(http.responseText);
	if (all)
		return(res.errmsg)
	else {
		if (res.errmsg == "")
			select_vars();
		else
			alert (res.errmsg);
	}
}

(function() {
	select_vars();
})()
</script>
