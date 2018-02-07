<h4><b>Data Output</b></h4>
<p>
Since data in the CAnMove database is organized into modules, or storage types, that are designed to store data for one or more data types, every module also comes with a specific set of output routines.
</p><p>
In general you can output data in three ways.
<ul><li>
Download Files<br/>
Any file that has been uploaded to the server can be downloaded again without any changes to its content.
So the system can be used as a file archive for any types of data or documentation.
Not just the bits of data that can be loaded into the actual CAnMove database.
</li><li>
Export Data<br/>
You can download data subsets from one dataset into XML files (can be read by e.g. Excel).
Data content will be the same as what was loaded but the validation process may have e.g. turned some variables into upper case or translated values into unique codes.
</li><li>
Pool Data<br/>
Similar to the Export Data routines but you can pool data from many datasets and also customize which variables to include in the output.
</li></ul>
</p><p>
For more information on a specific data type follow links below.
</p><p>
<a href="<?php echo $DrAction ?>?topic=data_output_gen">GPS Logger Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_output_gen">Light Logger Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_output_gen">Micro Data Logger Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_output_nbl">Nano Biology Lab Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_output_ori">Orientation Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_output_orn">Ornithodolite Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_output_gen">Satellite Telemetry Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_output_trr">Tracking Radar Data</a><br/>
</p>
