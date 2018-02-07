<?php
require "./canmove.inc";
if (isset($_GET['topic'])) {
	$topic=$_GET['topic'];
	require($DBRoot."/help/".$topic.".php");
} else {
?>
<style>
/* Tooltip container */
.tooltip {
	position: relative;
	display: inline-block;
	border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
}

/* Tooltip text */
.tooltip .tooltiptext {
	visibility: hidden;
	width: 120px;
	background-color: black;
	color: #fff;
	text-align: center;
	padding: 5px 0;
	border-radius: 6px;

	/* Position the tooltip text - see examples below! */
	position: absolute;
	z-index: 1;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
	visibility: visible;
}
</style>

<!--a href="<?php echo $DrAction ?>?topic=data_input">
<div class="tooltip">Help on Data Input
	<div class="tooltiptext">Data Input contains utilities for loading data</div>
</div>
</a>
<br/>
<a href="<?php echo $DrAction ?>?topic=data_input">Data Input</a>
<div class="tooltip">Help on Data Input
	<div class="tooltiptext">Data Input contains utilities for loading data</div>
</div>
<br/-->
<p>
This is the user help pages. Please select a help topic below.
</p>
<a href="<?php echo $DrAction ?>?topic=about_canmove">About CAnMove</a><br/>
<a href="<?php echo $DrAction ?>?topic=canmove_account">CAnMove Account</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_structure">Data Structure</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_access">Data Access</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_input">Data Input</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_output">Data Output</a><br/>
<a href="<?php echo $DrAction ?>?topic=system_requirements">System Requirements</a><br/>
<?php
}
?>
