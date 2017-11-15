/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_person
(
	person_id			serial primary key,
	first_name			varchar(50) not null,
	last_name			varchar(50) not null,
	drupal_id			integer unique,
	remark				text,
	start_date			date,
	end_date			date
);
insert into x_table (name) select distinct 'r_person' from x_table
where not exists (select 1 from x_table where name='r_person');
update x_table set version='1.0' where name='r_person';
