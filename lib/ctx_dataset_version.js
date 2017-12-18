// Creator: Mats J Svensson, CAnMove

function ctx_dataset_version(ajax_file) {
	var dataset_id=document.getElementById("dataset_id");
	var data_subset=document.getElementById("data_subset");
	var version=document.getElementById("version");
	var opt;

	while (version.length > 0)
		version.remove(0);
	if (dataset_id.value!="" && data_subset.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("dataset_id="+dataset_id.value+
					"&data_subset="+data_subset.value);
		var res=JSON.parse(http.responseText);
		for (i=0; i<res.version.length; i++) {
			opt=document.createElement("option");
			opt.value=res.version[i].version;
			opt.text=res.version[i].version;
			version.add(opt);
		}
	}
}
