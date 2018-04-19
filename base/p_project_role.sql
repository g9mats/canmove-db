/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_project_role
(
	project_role_id		serial primary key,
	project_id			integer not null references p_project (project_id),
	user_id				integer not null references r_person (person_id),
	user_role			varchar(1) not null,
	start_date			date,
	end_date			date,
unique (project_id, user_id)
);
insert into x_table (name) select distinct 'p_project_role' from x_table
where not exists (select 1 from x_table where name='p_project_role');
update x_table set version='1.0' where name='p_project_role';
