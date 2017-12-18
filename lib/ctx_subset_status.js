// Creator: Mats J Svensson, CAnMove

function ctx_subset_status(ajax_file) {
	var dataset_id=document.getElementById("dataset_id");
	var data_subset=document.getElementById("data_subset");
	var data_status=document.getElementById("data_status");
	var opt;

	while (data_status.length > 0)
		data_status.remove(0);
	if (dataset_id.value!="" && data_subset.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("dataset_id="+dataset_id.value+
					"&data_subset="+data_subset.value);
		var res=JSON.parse(http.responseText);
		for (i=0; i<res.data_status.length; i++) {
			opt=document.createElement("option");
			opt.value=res.data_status[i].data_status;
			opt.text=res.data_status[i].status_name;
			data_status.add(opt);
		}
	}
}
