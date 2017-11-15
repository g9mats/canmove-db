/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_orn_wind_profile
(
	wind_profile_id		serial primary key,
	session_id			integer not null references d_orn_session (session_id),
	wind_profile_no		integer not null,
	start_time			timestamp not null,
unique (session_id, wind_profile_no)
);
insert into x_table (name) select distinct 'd_orn_wind_profile' from x_table
where not exists (select 1 from x_table where name='d_orn_wind_profile');
update x_table set version='1.0' where name='d_orn_wind_profile';

create index d_orn_wind_profile_start_time_key
on d_orn_wind_profile (
	start_time
);
