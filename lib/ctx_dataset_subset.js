// Creator: Mats J Svensson, CAnMove

function ctx_dataset_subset(ajax_file) {
	var dataset_id=document.getElementById("dataset_id");
	var data_subset=document.getElementById("data_subset");
	var opt;

	while (data_subset.length > 0)
		data_subset.remove(0);
	if (dataset_id.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("dataset_id="+dataset_id.value);
		var res=JSON.parse(http.responseText);
		for (i=0; i<res.data_subset.length; i++) {
			opt=document.createElement("option");
			opt.value=res.data_subset[i].data_subset;
			opt.text=res.data_subset[i].subset_name;
			data_subset.add(opt);
		}
	}
}
