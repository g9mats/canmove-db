/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_ori_experiment
(
	experiment_id		serial primary key,
	animal_id			integer not null references d_ori_animal (animal_id),
	capture_id			integer not null references d_ori_capture (capture_id),
	setup_id			integer not null references p_ori_setup (setup_id),
	experiment_no		integer not null,
	experiment_type		varchar(50) not null references r_ori_experiment_type (experiment_type),
	cage_top_diameter	integer,
	cage_height			integer,
	sensor_type			varchar(50) references r_ori_sensor_type (sensor_type),
	data_processing		varchar(50) references r_ori_data_processing (data_processing),
	data_format			varchar(50) references r_ori_data_format (data_format),
	latitude			float not null,
	longitude			float not null,
	location			varchar(50),
	operator_id			integer not null references r_person (person_id),
	measurement_time	timestamp with time zone,
	remark				varchar(500),
unique (animal_id,capture_id,experiment_no)
);
insert into x_table (name) select distinct 'd_ori_experiment' from x_table
where not exists (select 1 from x_table where name='d_ori_experiment');
update x_table set version='1.0' where name='d_ori_experiment';

create index d_ori_experiment_animal_id_key
on d_ori_experiment (
	animal_id
);
create index d_ori_experiment_capture_id_key
on d_ori_experiment (
	capture_id
);
create index d_ori_experiment_setup_id_key
on d_ori_experiment (
	setup_id
);
