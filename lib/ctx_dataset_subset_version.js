// Creator: Mats J Svensson, CAnMove

function ctx_dataset_subset_version (ajax_file) {
	var dataset_id=document.getElementById("dataset_id");
	var data_subset=document.getElementById("data_subset");
	var opt;

	if (dataset_id.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("dataset_id="+dataset_id.value+
					"&data_subset="+data_subset.value);
		var res=JSON.parse(http.responseText);
		return (res.versions[0].versions);
	}
}
