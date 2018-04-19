/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_orn_track
(
	track_id			serial primary key,
	session_id			integer not null references d_orn_session (session_id),
	track_no			integer not null,
	start_time			timestamp with time zone not null,
	itis_tsn			integer not null,
	taxon				varchar(60) not null,
	species_no			integer not null,
	english_name		varchar(30),
	swedish_name		varchar(30),
	wind_direction		integer not null,
	wind_speed			float not null,
	barometer			integer not null,
	temperature			float not null,
	quantity			integer,
	sex					integer,
	age					integer,
	crop				integer,
	flight_style		integer,
	activity			integer,
	remark				varchar(500),
unique (session_id, track_no)
);
insert into x_table (name) select distinct 'd_orn_track' from x_table
where not exists (select 1 from x_table where name='d_orn_track');
update x_table set version='1.0' where name='d_orn_track';

create index d_orn_track_start_time_key
on d_orn_track (
	start_time
);
create index d_orn_track_itis_tsn_key
on d_orn_track (
	itis_tsn
);
create index d_orn_track_taxon_key
on d_orn_track (
	taxon
);
