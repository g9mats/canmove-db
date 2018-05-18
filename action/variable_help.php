<p>
The database is designed to dynamically copy with new variables but needs information on their attributes to know how to handle them.
This can be managed with the variable routines in this menu.
</p>
<p>
The tables that can store new variable data ends in _data and contain a column named data_value.
A special case is condition variables that can be stored in tables that ends in _condition and contain a column named condition_value.
</p>
<p>
If you stick to these two table/column combinations and also keep Column Type=Variable, Mandatory=No and Load Name=c, your are free to specify the rest of the attributes to your liking.
Otherwise it would need some changes to the software as well.
</p>
<p>
Also remember to choose good names of a general nature that can be reused in a meningful way in new datasets in the future as well.
</p>
