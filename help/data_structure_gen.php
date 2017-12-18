<h4><b>Generic Data Structure</b></h4>
<p>
The Generic module is intended for light logger data, GPS logger data, satellite telemetry data and any kind of data collected over time with a micro data logger attached to an animal.
Like accelerometers and pressure sensors.
The tracking is often conducted over a longer time period and a vast geographical area.
</p><p>
The capture data subset is all about the overhead information and contains the animal, capture, track and device objects.
This information is often entered into a spreadsheet during capture activities or copied from paper notes afterwards.
</p><p>
The actual tracking data is stored in the two data subsets trackpoint and datapoint.
The trackpoint object will probably be the most used and is designed for storing tuples of time, latitude and longitude as well as speed, course and altitude if GPS technology is used.
Any other time series of data can be stored in the general datapoint object.
This information is often text files that contains data that has been extracted from various devices.
One text file for every tracking.
</p><p>
For data loading instructions see
<a href="<?php echo $DrAction ?>?topic=data_input">Data Input</a>
.
</p>
