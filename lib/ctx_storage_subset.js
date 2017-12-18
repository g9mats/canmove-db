// Creator: Mats J Svensson, CAnMove

function ctx_storage_subset(ajax_file) {
	var storage_type=document.getElementById("storage_type");
	var data_subset=document.getElementById("data_subset");
	var opt;

	while (data_subset.length > 0)
		data_subset.remove(0);
	if (storage_type.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("storage_type="+storage_type.value);
		var res=JSON.parse(http.responseText);
		for (i=0; i<res.data_subset.length; i++) {
			opt=document.createElement("option");
			opt.value=res.data_subset[i].data_subset;
			opt.text=res.data_subset[i].subset_name;
			data_subset.add(opt);
		}
	}
}
