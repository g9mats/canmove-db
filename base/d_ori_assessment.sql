/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_ori_assessment
(
	assessment_id		serial primary key,
	animal_id			integer not null references d_ori_animal (animal_id),
	capture_id			integer not null references d_ori_capture (capture_id),
	assessment_no		integer not null,
	assessment_time		timestamp with time zone,
	operator_id			integer not null references r_person (person_id),
	remark				varchar(500),
unique (animal_id,capture_id,assessment_no)
);
insert into x_table (name) select distinct 'd_ori_assessment' from x_table
where not exists (select 1 from x_table where name='d_ori_assessment');
update x_table set version='1.0' where name='d_ori_assessment';

create index d_ori_assessment_animal_id_key
on d_ori_assessment (
	animal_id
);
create index d_ori_assessment_capture_id_key
on d_ori_assessment (
	capture_id
);
