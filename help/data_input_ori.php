<h4><b>Generic Data Input</b></h4>
<p>
Data is loaded in five data subsets.
Captures, Experiments, Counts, Estimations and Assessments.
Spreadsheet templates can be downloaded from the database menu under Support - Download Template.
</p><p>
Data must have standarized headers that can be found in the menu as well: Support - List All Variables.
In the template files the headers are colour coded, one for each object.
Darker colours means the variable is mandatory and lighter means it is optional.
There is also help tags on the headers.
</p><p>
Headers can appear in any order.
So even if it makes it easier to put the data in a logical order the templates can be seen as only a suggestion that might be helpful.
</p><p>
If the same animal is captured more than once several capture records will of cource be created in the database but animal variables will only be read from the first row.
The value of the Sex variable will e.g. only be read the first time a specific Animal Id shows up in the capture data.
You could repeat the animal variable values or leave them blank as you like in the following rows.
They will be ignored anyway.
</p><p>
In Experiments the Setup is supposed to be a short descriptive name that is practical to use when talking about the dataset.
There should be a direct one to one relation from the Setup values to unique combinations of Condition values.
</p><p>
Experiment No is used when more than one experiment is conducted on the same animal.
Phase No is used when you want to split the experiment in several time intervals and register the animal activity for each of them separately in the database. 
They are optional but if any of them are left out they will be assigned the value 1 in the database.
</p><p>
Counts is for registration of how many scratches the animal has made in each sector of the circular cage.
</p><p>
Estimations is used in experiments where the operator estimates the animal activites in terms of how much activity, how concentrated, in what direction and so forth.
</p><p>
Yet another data subset, Activity log, will probably be implemented in the future where some kind of technical solution can register every single activity and store it along with a timestamp.
</p><p>
All files that will be loaded into the database has to be saved in a tab separated text format before upload to the file server archive and must be tagged as final in the upload process.
</p>
