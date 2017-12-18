// Creator: Mats J Svensson, CAnMove

function ctx_phase_index(ajax_file) {
	var device_id=document.getElementById("device_id");
	var version=document.getElementById("version");
	var phase_type=document.getElementById("phase_type");
	var phase_index=document.getElementById("phase_index");

	if (phase_type.value!="") {
		http=new XMLHttpRequest();
		http.open("POST", ajax_file, false);
		//Send the proper header information along with the request
		http.setRequestHeader("Content-type",
			"application/x-www-form-urlencoded");
		http.send("device_id="+device_id.value+
					"&version="+version.value+
					"&phase_type="+phase_type.value);
		var res=JSON.parse(http.responseText);
		phase_index.value=res.phase_index;
		}
}
