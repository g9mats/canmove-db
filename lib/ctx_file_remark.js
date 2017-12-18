// Creator: Mats J Svensson, CAnMove

function ctx_file_remark(ajax_file) {
	var file_id=document.getElementById("file_id");
	var remark=document.getElementById("remark");

	if (file_id.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("file_id="+file_id.value);
		var res=JSON.parse(http.responseText);
		remark.value=res.remark;
		}
}
