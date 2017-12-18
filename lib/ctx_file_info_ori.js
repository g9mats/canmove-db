// Creator: Mats J Svensson, CAnMove

function ctx_file_info_ori(ajax_file) {
	var file_id=document.getElementById("file_id");
	var version=document.getElementById("version");
	var remark=document.getElementById("remark");

	if (file_id.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("file_id="+file_id.value);
		var res=JSON.parse(http.responseText);
		version.value=res.version;
		remark.value=res.remark;
		}
}
