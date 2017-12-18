<h4><b>Generic Data Input</b></h4>
<p>
Data is loaded in three data subsets.
Captures, Trackpoints and Datapoints.
Spreadsheet templates can be downloaded from the database menu under Support - Download Template.
</p><p>
Data must have standarized headers that can be found in the menu as well: Support - List All Variables.
In the template files the headers are colour coded, one for each object.
Darker colours means the variable is mandatory and lighter means it is optional.
There is also help tags on the headers.
</p><p>
Headers can appear in any order with one exception.
Additional device variables in Captures must come after the Device Id because this is the only way for the loading routines to know which information that belongs to which device.
Appart from that it makes it easier to put the data in a logical order but the templates can be seen as only a suggestion that might be helpful.
</p><p>
If a device is attached when the animal is captured the Track Event value should be set to "START".
If an animal is re-captured the valued should be set to "END".
If a new device is attached then a second row with the same Capture Time and Track Event set to "START" should be inserted.
If an animal is captured and measured but no device attached or removed for whatever reason the Track Event value should be left empty.
A capture record with measurements will then be inserted into the database but a new track will not be created and it will not affect any ongoing track.
If there are several rows with the same Capture Time only measurements from the first row will be read into the database.
You could repeat the measurements values or leave them blank as you like in the following rows.
They will be ignored anyway.
</p><p>
It is the same story for the animal specific attributes.
The value of the Sex variable will e.g. only be read the first time a specific Animal Id shows up in the capture data.
</p><p>
The intention is to transfer the tracking data, Trackpoints and Datapoints, the shortest way from the tracking devices to the database with a mininum of manual efforts.
There is no need to actually store data in spreadsheet format unless you need that for other reasons.
So for the purpose of loading data to the database the templates may only serve as a description of the data format.
It is expected that the device dump routine produces one tab separated text file from each device but since you can load several files in one operation that will not be a problem.
</p><p>
Since tracking data dumps as a rule do not contain Animal Id and Device Id you have to tag the files with this information in a registration routine, after file upload and before import.
This also means that Captures has to be loaded first since the keys are picked from a list of key combinations stored in the database at the time of registration.
</p><p>
There is also support for period keys if the tracking data is devided into years, months or something else and a version key if e.g. you want to test different algorithms when converting data from devices to text files.
</p><p>
With a clever naming scheme on the files you can register many files in one operation using a file name template.
</p><p>
All files that will be loaded into the database has to be saved in a tab separated text format before upload to the file server archive and must be tagged as final in the upload process.
</p>
