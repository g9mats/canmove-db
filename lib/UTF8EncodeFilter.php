<?php 
class UTF8EncodeFilter extends php_user_filter
{
	function filter($in, $out, &$consumed, $closing) {
		while ($bucket = stream_bucket_make_writeable($in)) {
			$bucket->data = utf8_encode($bucket->data);
			$consumed += $bucket->datalen;
			stream_bucket_append($out, $bucket);
		}
		return PSFS_PASS_ON;
	}
}
stream_filter_register("utf8encode", "UTF8EncodeFilter")
    or die("Failed to register filter UTF8EncodeFilter");
?>
