/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_gen_capture
(
	serial_id			serial primary key,
	dataset_id			integer not null,
	animal				varchar,
	taxon				varchar,
	animal_remark		varchar,
	track_event			varchar,
	track_remark		varchar,
	capture_time		varchar,
	operator_id			varchar,
	latitude			varchar,
	longitude			varchar,
	location			varchar,
	capture_remark		varchar,
	c1					varchar,
	c2					varchar,
	c3					varchar,
	c4					varchar,
	c5					varchar,
	c6					varchar,
	c7					varchar,
	c8					varchar,
	c9					varchar,
	c10					varchar,
	c11					varchar,
	c12					varchar,
	c13					varchar,
	c14					varchar,
	c15					varchar,
	c16					varchar,
	c17					varchar,
	c18					varchar,
	c19					varchar,
	c20					varchar,
	c21					varchar,
	c22					varchar,
	c23					varchar,
	c24					varchar,
	c25					varchar,
	c26					varchar,
	c27					varchar,
	c28					varchar,
	c29					varchar,
	c30					varchar
);
insert into x_table (name) select distinct 'l_gen_capture' from x_table
where not exists (select 1 from x_table where name='l_gen_capture');
update x_table set version='1.0' where name='l_gen_capture';

create index l_gen_capture_dataset_id_key
on l_gen_capture (
	dataset_id,
	animal,
	capture_time
);
