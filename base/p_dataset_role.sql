/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_dataset_role
(
	dataset_role_id		serial primary key,
	dataset_id			integer not null references p_dataset (dataset_id),
	user_id				integer not null references r_person (person_id),
	user_role			varchar(1) not null,
	start_date			date,
	end_date			date,
unique (dataset_id, user_id)
);
insert into x_table (name) select distinct 'p_dataset_role' from x_table
where not exists (select 1 from x_table where name='p_dataset_role');
update x_table set version='1.0' where name='p_dataset_role';
