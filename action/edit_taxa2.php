<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$dataset_id=$_POST['dataset_id'];
if ($dataset_id=="") {
	echo "<p>You must specify a dataset.</p>";
	return;
}
?>

<p>
Edit taxa for a dataset.
</p>

<div id="taxa"></div>

<script>

var path="<?php echo $WebRoot; ?>/db/action/edit_taxa_";
var dataset_id=<?php echo $dataset_id; ?>;

function select_taxa() {
	var tab=document.getElementById("taxa");
	var txt="";

	http=new XMLHttpRequest();
	http.open("POST", path+"select.php", false);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.send("dataset_id="+dataset_id);
	var res=JSON.parse(http.responseText);
	txt="<table><tr><th>No</th><th>Taxon</th><th>Remark</th></tr>";
	for (i=0; i<res.taxon.length; i++) {
		txt+="<tr>";
		txt+="<td><input id='i"+i+"' hidden value='"+res.taxon[i].taxon_id+"' size='5'>"+(i+1)+"</input></td>";
		txt+="<td><input id='n"+i+"' value='"+res.taxon[i].taxon+"' size='35' maxlength='60'></input></td>";
		txt+="<td><input id='r"+i+"' value='"+res.taxon[i].remark+"' size='30'></input></td>";
		txt+="<td><button onclick='update_taxon("+i+")'>Save</button></td>";
		txt+="<td><button onclick='delete_taxon("+i+")'>Del</button></td>";
		txt+="</tr>";
	}
	txt+="<td></td>";
	txt+="<td><input id='newname' type='text' size='35' maxlength='60'></input></td>";
	txt+="<td><input id='newremark' type='text' size='30'></input></td>";
	txt+="<td><button onclick='insert_taxon()'>Ins</button></td>";
	txt+="</table>";
	tab.innerHTML = txt;
}

function insert_taxon (i) {
	var name=document.getElementById("newname");
	var remark=document.getElementById("newremark");

	http=new XMLHttpRequest();
	http.open("POST", path+"insert.php", false);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.send("dataset_id="+dataset_id+
		"&name="+name.value+"&remark="+remark.value);
	var res=JSON.parse(http.responseText);
	if (res.errmsg == "")
		select_taxa();
	else
		alert (res.errmsg);
}

function update_taxon (i) {
	var taxon_id=document.getElementById("i"+i);
	var name=document.getElementById("n"+i);
	var remark=document.getElementById("r"+i);

	http=new XMLHttpRequest();
	http.open("POST", path+"update.php", false);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.send("taxon_id="+taxon_id.value+"&dataset_id="+dataset_id+
		"&name="+name.value+"&remark="+remark.value);
	var res=JSON.parse(http.responseText);
	if (res.errmsg == "")
		select_taxa();
	else
		alert (res.errmsg);
}

function delete_taxon (i) {
	var taxon_id=document.getElementById("i"+i);

	http=new XMLHttpRequest();
	http.open("POST", path+"delete.php", false);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.send("taxon_id="+taxon_id.value);
	var res=JSON.parse(http.responseText);
	if (res.errmsg == "")
		select_taxa();
	else
		alert (res.errmsg);
}

(function() {
	select_taxa();
})()
</script>
