/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_ori_count
(
	count_id			serial primary key,
	phase_id			integer not null references d_ori_phase (phase_id),
	version				integer not null default 1,
	funnel_line			varchar(1) not null,
	operator_id			integer not null references r_person (person_id),
	northern_sector		float not null default 1,
	s1_direction		float not null default 0,
	activity			integer,
	d1					float,
	s					float,
	r1					float,
	p1					float,
	d2a					float,
	d2b					float,
	r2					float,
	p2					float,
	direction			float,
	remark				varchar(500)
);
insert into x_table (name) select distinct 'd_ori_count' from x_table
where not exists (select 1 from x_table where name='d_ori_count');
update x_table set version='1.0' where name='d_ori_count';

create index d_ori_count_phase_id_key
on d_ori_count (
	phase_id
);
