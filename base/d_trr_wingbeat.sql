/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_trr_wingbeat (
	wingbeat_id			serial primary key,
	track_id			integer not null references d_trr_track (track_id),
	start_time			timestamp with time zone not null,
	duration			integer not null,
	sense				char(1) not null,
	file_name			varchar(200) not null,
	wbfpeakfq			float not null,
	bffpeakfq			float not null,
	wbfpeakpower		integer not null,
	bffpeakpower		integer not null,
	wbf_lo_limit		float not null,
	wbf_hi_limit		float not null,
	bff_lo_limit		float not null,
	bff_hi_limit		float not null,
	coeff_a_zero		float not null,
	fsamp				float not null,
	fftsize				integer not null
);
insert into x_table (name) select distinct 'd_trr_wingbeat' from x_table
where not exists (select 1 from x_table where name='d_trr_wingbeat');
update x_table set version='1.0' where name='d_trr_wingbeat';

create index d_trr_wingbeat_track_id_key
on d_trr_wingbeat (
	track_id
);
