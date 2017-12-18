// Creator: Mats J Svensson, CAnMove

function ctx_status_file(ajax_file) {
	var dataset_id=document.getElementById("dataset_id");
	var data_subset=document.getElementById("data_subset");
	var data_status=document.getElementById("data_status");
	var file_arr=document.getElementById("file_arr[]");
	var opt;

	while (file_arr.length > 0)
		file_arr.remove(0);
	if (dataset_id.value!="" && data_subset.value!="" && data_status.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("dataset_id="+dataset_id.value+
					"&data_subset="+data_subset.value+
					"&data_status="+data_status.value);
		var res=JSON.parse(http.responseText);
		for (i=0; i<res.file.length; i++) {
			opt=document.createElement("option");
			opt.value=res.file[i].file_id;
			opt.text=res.file[i].original_name;
			opt.text+=" - "+res.file[i].upload_time;
			file_arr.add(opt);
		}
	}
}
