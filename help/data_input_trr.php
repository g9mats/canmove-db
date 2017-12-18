<h4><b>Tracking Radar Data Input</b></h4>
<p>
There is no user operated loading routine.
</p><p>
The output from the Tracking Radar stations are directly stored in local databases.
The data structure of the Tracking radar module in the CAnMove database is very similar to the local databases.
So the loading routine is mostly about merging the data and to replace the unique object key values so that they can coexist in a single database.
</p><p>
The loading routine is designed to be operated by someone with database knowledge and with direct access to the databases involved.
</p>
