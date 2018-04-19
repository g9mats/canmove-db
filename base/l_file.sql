/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_file
(
	file_id				serial primary key,
	dataset_id			integer not null references p_dataset (dataset_id),
	data_status			varchar(10) not null,
	data_subset			varchar(15) not null,
	time_zone			varchar not null,
	device_id			integer,
	period				varchar(15) not null default '-',
	version				integer not null default 1,
	varset				varchar(10) not null default '-',
	original_name		varchar(50),
	archive_name		varchar(50),
	upload_time			timestamp with time zone,
	registered			boolean not null default false,
	reg_time			timestamp with time zone,
	imported			boolean not null default false,
	imp_time			timestamp with time zone,
	validated			boolean not null default false,
	val_time			timestamp with time zone,
	loaded				boolean not null default false,
	load_time			timestamp with time zone,
	deleted				boolean not null default false,
	del_time			timestamp with time zone,
	imported_data		boolean not null default false,
	loaded_data			boolean not null default false,
	remark				text
);
insert into x_table (name) select distinct 'l_file' from x_table
where not exists (select 1 from x_table where name='l_file');
update x_table set version='1.0' where name='l_file';

create index l_file_dataset_id_key
on l_file (
	dataset_id,
	data_status,
	data_subset,
	device_id,
	period,
	version,
	varset
);
