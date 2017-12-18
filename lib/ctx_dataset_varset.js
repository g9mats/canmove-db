// Creator: Mats J Svensson, CAnMove

function ctx_dataset_varset(ajax_file) {
	var dataset_id=document.getElementById("dataset_id");
	var data_subset=document.getElementById("data_subset");
	var varset=document.getElementById("varset");
	var opt;

	while (varset.length > 0)
		varset.remove(0);
	if (dataset_id.value!="" && data_subset.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("dataset_id="+dataset_id.value+
					"&data_subset="+data_subset.value);
		var res=JSON.parse(http.responseText);
		for (i=0; i<res.varset.length; i++) {
			opt=document.createElement("option");
			opt.value=res.varset[i].varset;
			opt.text=res.varset[i].varset;
			varset.add(opt);
		}
	}
}
