/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_ori_count
(
	count_id			serial primary key,
	dataset_id			integer not null,
	animal				varchar not null,
	capture_time		varchar not null,
	experiment_no		integer not null,
	phase_no			integer not null,
	version				integer not null default 1,
	funnel_line			varchar,
	operator_id			varchar,
	northern_sector     varchar,
	s1_direction        varchar,
	activity			varchar,
	d1					varchar,
	s					varchar,
	r1					varchar,
	p1					varchar,
	d2a					varchar,
	d2b					varchar,
	r2					varchar,
	p2					varchar,
	direction			varchar,
	count_remark		varchar,
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
	c24					varchar
);
insert into x_table (name) select distinct 'l_ori_count' from x_table
where not exists (select 1 from x_table where name='l_ori_count');
update x_table set version='1.0' where name='l_ori_count';

create index l_ori_count_dataset_id_key
on l_ori_count (
	dataset_id,
	animal,
	capture_time,
	experiment_no,
	phase_no
);
