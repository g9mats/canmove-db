/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_nbl_recording
(
	recording_id		serial primary key,
	dataset_id			integer not null references p_dataset (dataset_id),
	recording			integer not null,
	setup_id			integer not null references p_nbl_setup (setup_id),
	replicate			integer not null,
	recording_time		timestamp not null,
	remark				varchar(500)
);
insert into x_table (name) select distinct 'd_nbl_recording' from x_table
where not exists (select 1 from x_table where name='d_nbl_recording');
update x_table set version='1.0' where name='d_nbl_recording';

create unique index d_nbl_recording_recording_key
on d_nbl_recording (
	dataset_id,
	recording
);
create unique index d_nbl_recording_replicate_key
on d_nbl_recording (
	dataset_id,
	setup_id,
	replicate
);
create index d_nbl_recording_recording_time_key
on d_nbl_recording (
	recording_time
);
