/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_ori_capture
(
	capture_id			serial primary key,
	animal_id			integer not null references d_ori_animal (animal_id),
	capture_time		timestamp with time zone not null,
	latitude			float not null,
	longitude			float not null,
	location			varchar(50),
	operator_id			integer not null references r_person (person_id),
	remark				varchar(500)
);
insert into x_table (name) select distinct 'd_ori_capture' from x_table
where not exists (select 1 from x_table where name='d_ori_capture');
update x_table set version='1.0' where name='d_ori_capture';

create index d_ori_capture_animal_id_key
on d_ori_capture (
	animal_id
);
create index d_ori_capture_capture_time_key
on d_ori_capture (
	capture_time
);
create index d_ori_capture_location_key
on d_ori_capture (
	location
);
create index d_ori_capture_operator_id_key
on d_ori_capture (
	operator_id
);
