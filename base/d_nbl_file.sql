/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_nbl_file
(
	file_id				serial primary key,
	recording_id		integer not null references d_nbl_recording (recording_id),
	file				integer not null,
	file_name			varchar(50) not null,
	remark				varchar(500)
);
insert into x_table (name) select distinct 'd_nbl_file' from x_table
where not exists (select 1 from x_table where name='d_nbl_file');
update x_table set version='1.0' where name='d_nbl_file';

create index d_nbl_file_recording_id_key
on d_nbl_file (
	recording_id
);
create index d_nbl_file_file_key
on d_nbl_file (
	recording_id,
	file
);
create index d_nbl_file_file_name_key
on d_nbl_file (
	file_name
);
