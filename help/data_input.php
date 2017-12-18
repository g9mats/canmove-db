<h4><b>Data Input</b></h4>
<p>
Since data in the CAnMove database is organized into modules, or storage types, that are designed to store data for one or more data types, every module also comes with a specific set of loading routines.
</p><p>
Often the research study produces a set of spreadsheets or text files containing a matrix of data. 
The data loading routines expects a tab separated text file so spreadsheets has  to be converted in advance.
The text files must have column headers with standard names to help the loading routine to identify what kind of data that follows.
As a general rule the columns can appear in any order but there are some exceptions that are documented in the specific module loading guide.
</p><p>
The data loading is conducted in 4-5 stages.
<ul>
<li>
Upload files<br/>
Files are first uploaded to the file archive on the server.
When uploading the file can be tagged.
Text files ready for loading should be tagged "final", but you can also upload other files like e.g. spreadsheets as "raw", not ready files as "interim" and documentation as "document".
</li><li>
Register files<br/>
Some tracking files contain tracking data but no information on which device that collected the data or which animal that wore the device.
Registration tags the file with these keys and maybe other keys as well.
</li><li>
Import files<br/>
The data is loaded from file into a temporary storage area in the database.
</li><li>
Validate data<br/>
Data in the temporary storage area is validated.
</li><li>
Load data<br/>
Data is transformed from the temporary storage into the appropriate set of tables in the CAnMove database.
</li>
</ul>
</p><p>
For more information on a specific data type follow links below.
</p><p>
<a href="<?php echo $DrAction ?>?topic=data_input_gen">GPS Logger Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_input_gen">Light Logger Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_input_gen">Micro Data Logger Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_input_nbl">Nano Biology Lab Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_input_ori">Orientation Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_input_orn">Ornithodolite Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_input_gen">Satellite Telemetry Data</a><br/>
</p><p>
Some types of data are loaded with special routines.
</p>
<a href="<?php echo $DrAction ?>?topic=data_input_trr">Tracking Radar Data</a><br/>
