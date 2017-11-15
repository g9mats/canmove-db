/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_gen_trackpoint
(
	trackpoint_id		serial primary key,
	dataset_id			integer not null,
	device_id			integer not null,
	period				varchar not null default '-',
	version				integer not null default 1,
	log_time			varchar,
	latitude			varchar,
	longitude			varchar,
	quality				varchar,
	speed				varchar,
	course				varchar,
	altitude			varchar,
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
insert into x_table (name) select distinct 'l_gen_trackpoint' from x_table
where not exists (select 1 from x_table where name='l_gen_trackpoint');
update x_table set version='1.0' where name='l_gen_trackpoint';

create index l_gen_trackpoint_dataset_id_key
on l_gen_trackpoint (
	dataset_id,
	device_id,
	period,
	version,
	log_time
);
