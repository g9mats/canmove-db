/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_project
(
	project_id			serial primary key,
	title				varchar(50) not null unique,
	remark				text,
	start_date			date,
	end_date			date
);
insert into x_table (name) select distinct 'p_project' from x_table
where not exists (select 1 from x_table where name='p_project');
update x_table set version='1.0' where name='p_project';

create index p_project_start_date_key
on p_project (
	start_date
);
create index p_project_end_date_key
on p_project (
	end_date
);
