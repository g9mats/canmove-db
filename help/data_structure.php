<h4><b>Data Structure</b></h4>
<p>
The data in the CAnMove database come from many different sources.
Hence the modular approach to the database design.
Each module is supported by a storage type in the form of a set of tables suitable for storing a certain data structure.
A module can be used for storing more than one type of data if they need a similar data structure.
E.g. light logger data and satellite telemetry data can both be stored in the Generic module.
</p><p>
But there are also common data structures that are the same for all data types.
This information is entered directly into the database using web screens.
</p><p>
Similar data from a research study are stored together in datasets and the description of them are stored in a common dataset object.
This object contains e.g. information on title, owner, how many animals and tracks, type of data, method, site, dates and contact person.
</p><p>
The dataset should consist of quite homogeneous data and can e.g. be a collection of a certain species, a certain time period or a geographical area.
The dataset owner can grant access to other users, but only on entire datasets, so this should be taken into consideration when dividing the data into datasets.
See <a href="<?php echo $DrAction ?>?topic=data_access">Data Access</a>.
</p><p>
Each dataset must belong to a previously created project that has a title and an owner.
A project can be used to document that there is a connection between several datasets that may or may not be of the same data type. 
</p><p>
For more information on a specific data type follow links below.
</p>
<a href="<?php echo $DrAction ?>?topic=data_structure_gen">GPS Logger Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_structure_gen">Light Logger Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_structure_gen">Micro Data Logger Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_structure_nbl">Nano Biology Lab Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_structure_ori">Orientation Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_structure_orn">Ornithodolite Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_structure_gen">Satellite Telemetry Data</a><br/>
<a href="<?php echo $DrAction ?>?topic=data_structure_trr">Tracking Radar Data</a><br/>
