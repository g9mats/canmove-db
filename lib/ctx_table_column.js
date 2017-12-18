// Creator: Mats J Svensson, CAnMove

function ctx_table_column(ajax_file) {
	var table_name=document.getElementById("table_name");
	var column_name=document.getElementById("column_name");
	var opt;

	while (column_name.length > 0)
		column_name.remove(0);
	if (table_name.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("table_name="+table_name.value);
		var res=JSON.parse(http.responseText);
		for (i=0; i<res.column_name.length; i++) {
			opt=document.createElement("option");
			opt.value=res.column_name[i].column_name;
			opt.text=res.column_name[i].column_name;
			column_name.add(opt);
		}
	}
}
