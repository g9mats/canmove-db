/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_device_model
(
	device_model_id		serial primary key,
	model				varchar(50) not null,
	manufacturer		varchar(50),
	description			varchar(200),
	weight				float
);
insert into x_table (name) select distinct 'r_device_model' from x_table
where not exists (select 1 from x_table where name='r_device_model');
update x_table set version='1.0' where name='r_device_model';

create index r_device_model_model_key
on r_device_model (
	model
);
