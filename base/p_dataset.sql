/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_dataset
(
	dataset_id			serial primary key,
	project_id			integer not null references p_project (project_id),
	title				varchar(50) not null,
	data_type			varchar(4) not null references r_data_type (data_type),
	storage_type		varchar(4) not null references r_storage_type (storage_type),
	method				varchar(100),
	site				varchar(100),
	start_date			date,
	end_date			date,
	animal_num			integer not null,
	track_num			integer not null,
	animal_db			integer not null,
	track_db			integer not null,
	data_location		varchar(20),
	contact_id			integer references r_person (person_id),
	public				boolean not null default false,
	release_date		date,
	remark				text
);
insert into x_table (name) select distinct 'p_dataset' from x_table
where not exists (select 1 from x_table where name='p_dataset');
update x_table set version='1.0' where name='p_dataset';

create index p_dataset_project_id_key
on p_dataset (
	project_id
);
create index p_dataset_title_key
on p_dataset (
	title
);
