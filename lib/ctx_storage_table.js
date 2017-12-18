// Creator: Mats J Svensson, CAnMove

function ctx_subset_table(ajax_file) {
	var storage_type=document.getElementById("storage_type");
	var table_name=document.getElementById("table_name");
	var opt;

	while (table_name.length > 0)
		table_name.remove(0);
	if (storage_type.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("storage_type="+storage_type.value);
		var res=JSON.parse(http.responseText);
		for (i=0; i<res.table_name.length; i++) {
			opt=document.createElement("option");
			opt.value=res.table_name[i].table_name;
			opt.text=res.table_name[i].table_name;
			table_name.add(opt);
		}
	}
}
