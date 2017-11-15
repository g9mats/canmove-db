/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_nbl_trackpoint
(
	serial_id			serial primary key,
	dataset_id			integer not null,
	recording			varchar,
	setup				varchar,
	replicate			varchar,
	recording_time		varchar,
	file				varchar,
	file_name			varchar,
	track				varchar,
	taxon				varchar,
	animal_label		varchar,
	frame				varchar,
	time				varchar,
	x					varchar,
	y					varchar,
	z					varchar,
	c1					varchar,
	c2					varchar,
	c3					varchar,
	c4					varchar,
	c5					varchar,
	c6					varchar,
	c7					varchar,
	c8					varchar,
	c9					varchar,
	c10					varchar
);
insert into x_table (name) select distinct 'l_nbl_trackpoint' from x_table
where not exists (select 1 from x_table where name='l_nbl_trackpoint');
update x_table set version='1.0' where name='l_nbl_trackpoint';

create unique index l_nbl_trackpoint_recording_key
on l_nbl_trackpoint (
	dataset_id,
	recording,
	track,
	time
);

create unique index l_nbl_trackpoint_setup_key
on l_nbl_trackpoint (
	dataset_id,
	setup,
	replicate,
	track,
	time
);

create unique index l_nbl_trackpoint_file_key
on l_nbl_trackpoint (
	dataset_id,
	recording,
	file,
	frame,
	track
);
