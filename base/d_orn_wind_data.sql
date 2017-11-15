/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_orn_wind_data
(
	wind_data_id		serial primary key,
	wind_profile_id		integer not null references d_orn_wind_profile (wind_profile_id),
	wind_data_no		integer not null,
	z_height			float not null,
	x_speed				float not null,
	y_speed				float not null,
	wind_direction		float not null,
	wind_speed			float not null,
unique (wind_profile_id, wind_data_no)
);
insert into x_table (name) select distinct 'd_orn_wind_data' from x_table
where not exists (select 1 from x_table where name='d_orn_wind_data');
update x_table set version='1.0' where name='d_orn_wind_data';
