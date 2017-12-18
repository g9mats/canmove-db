// Creator: Mats J Svensson, CAnMove

function ctx_file_info_gen(ajax_file) {
	var file_id=document.getElementById("file_id");
	var device_id=document.getElementById("device_id");
	var period=document.getElementById("period");
	var version=document.getElementById("version");
	var varset=document.getElementById("varset");
	var remark=document.getElementById("remark");

	if (file_id.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("file_id="+file_id.value);
		var res=JSON.parse(http.responseText);
		device_id.value=res.device_id;
		period.value=res.period;
		version.value=res.version;
		varset.value=res.varset;
		remark.value=res.remark;
		}
}
