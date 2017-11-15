/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_gen_capture
(
	capture_id			serial primary key,
	animal_id			integer not null references d_gen_animal (animal_id),
	capture_time		timestamp not null,
	latitude			float not null,
	longitude			float not null,
	location			varchar(50),
	operator_id			integer not null references r_person (person_id),
	remark				varchar(500)
);
insert into x_table (name) select distinct 'd_gen_capture' from x_table
where not exists (select 1 from x_table where name='d_gen_capture');
update x_table set version='1.0' where name='d_gen_capture';

create index d_gen_capture_animal_id_key
on d_gen_capture (
	animal_id
);
create index d_gen_capture_capture_time_key
on d_gen_capture (
	capture_time
);
create index d_gen_capture_location_key
on d_gen_capture (
	location
);
create index d_gen_capture_operator_id_key
on d_gen_capture (
	operator_id
);
