This database system was developed for the CAnMove project at the Biological
Institution at Lund University. It was designed to manage animal movement data
of various types.

SOFTWARE
========
The system uses the following software:
- Red Hat Enterprise Linux 7
- PostgreSQL 9.5
- Apache 2.4
- PHP 5.4
- Drupal 7

A web service has also been developed using additional software:
- Java 8
- Tomcat 7
- Axis2

FILE STRUCTURE
==============
The installation is structured into two file trees. One of them is accessible
from the web (via Drupal) here after called <WEB> and the other will be called
<HOME>. They contain the following sub directories.

<WEB>			Resides at /var/www/html/db as default
<WEB>/action	Most of the PHP code in here
<WEB>/check		Special check routines for tracking radar data at LU
<WEB>/download	Excel templates for data loading executed by users
<WEB>/form		Special loading routines for tracking radar data at LU
<WEB>/help		User help
<WEB>/lib		Common PHP routines

<HOME>			Resides at /home/sys/canmove as default
<HOME>/base		Scripts for creating the database
<HOME>/cron		Scripts intended for scheduled nightly execution
<HOME>/data		File archive tree
<HOME>/doc		System documentation
<HOME>/etl		Data loading routines executed manually by DBA
<HOME>/lib		Soft link pointing to <WEB>/lib
<HOME>/source	Tree with all catalogs from both trees to maintain git repo
<HOME>/sync		Reference data update routines executed manually by DBA
<HOME>/util		Utilites to simplify the daily work for the developer
<HOME>/ws		Web Service software/configuration/logs (used by WRAM at SLU)

CONFIGURATION
=============
Copy <WEB>/lib/settings.template to <WEB>/lib/settings.inc and edit the content
to fit your needs. Adjust server names, file trees and databases.
Also create soft links with the name canmove.inc pointing to settings.inc in
the following catalogs.
<WEB>/action		ln -s ../lib/settings.inc canmove.inc
<WEB>/help			ln -s ../lib/settings.inc canmove.inc
<HOME>/cron			ln -s ../lib/settings.inc canmove.inc
<DRUPAL software>	ln -s <WEB>/lib/settings.inc canmove.inc

DATABASE
========
The CAnMove data is stored in a set of tables totally separated from Drupal
tables. No Drupal modules are used for handling CAnMove data. As seen in the
configuration file it is recommended to create two PostgreSQL databases. One
for Drupal (default drupal) and one for CAnMove data (default canmove).

To create the CAnMove database objects, goto <HOME>/base, logon to the canmove
database and run all_create.sql. This script will create all objects taking
into consideration any dependencies between objects. All objects has its own
script file with the same name and a sql suffix. The objects are grouped into
types by use of a prefix letter.

d	Actual movement data
i	Used by the WRAM interface
l	Loading information. Log of all data files uploaded and also work space.
p	Meta data like project, dataset and access information
r	Reference data like e.g. species
s	Stored functions
v	Views, just a few to simplify queries in psql
x	System internal objects

Objects can also have a second three letter prefix. This is a module code that
referres to a certain type of data storage. Objects without this second prefix
are general to all storage types.

gen		Generic module for any kind of micro data loggers
nbl		Nano Biology Lab
ori		Orientation
orn		Ornithodolite
trr		Tracking Radar

To make an initial load of some of the reference tables run the dump file
<HOME>/etl/r_data/r_canmove.pgd in psql.

SYSTEM CONTROL TABLES
=====================
Information on some tables that are rather part of the system and controls
its behaviour than part of the CAnMove database

r_person
	Administration of persons/connections to accounts. See more instructions
	on screen DBA Access/Create Person.
r_data/r_data_alias
	Administration of variables and aliases. See more instructions on screen
	DBA Management/Create Variable.
r_data_subset
	Data subset identities and name shown to users. Use psql.
r_data_type
	Data type identities and name shown to users. Use psql.
r_storage_type
	Storage type identities and name shown to users. Use psql.
x_context
	Contols the behaviour of many routines. See more instruction on screen
	System Development/Create Context.
x_file_name_template
	Register Files templates. Use psql.
x_table
	Intended for table version control, not really used other than in the
	scripts in <HOME>/base.
x_table_header
	To produce prettier listing of variables. Use psql.

DRUPAL
======
The Drupal system is used to a limited extent. It is only used for menus,
authentication and for overall display layout.

The module Nodeaccess is used to restrict access to menu options.
The modules Administration menu and Administration theme are used to make
development easier but they are not necessary.
The modules CAS and phpCAS are used to facilitate single sign on with the
LUCAT database as the central authentication repository. But it is still
possible to use independant Drupal accounts.

The LUBartik theme is created as a copy of the Bartik theme and an extra
line has been added to the node template to include a file, drupal.php,
that adds content to the body of every node.
Drupal.php uses the Drupal URL alias to make sure that a PHP file with the
same name is included and also adds an index to the name if serveral steps are
required to complete the task at hand. This extension to the theme gives
several benefits.
- You only need to create one Drupal node even if the task needs more than one
screen. Without cluttering the code with a big if-then-else statement.
- You can use your favourite editor instead of the one provided by Drupal.
- You can use command line utilities to look for search patterns across all
of the code.
- It makes it easy to copy code from development to production.
- It makes it easier to upgrade Drupal although you still need to handle the
"empty" nodes and their menu links.

For more details of Drupal installation see separate file drupal.txt.

All files has not been uploaded to GitHub since they are specific to the
installation at LU. For more information on those see management.txt in
this catalog.
