/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_nbl_track
(
	track_id			serial primary key,
	recording_id		integer not null references d_nbl_recording (recording_id),
	track				integer not null,
	itis_tsn			integer not null,
	taxon				varchar(60) not null,
	animal_label		varchar(10),
	remark				varchar(500)
);
insert into x_table (name) select distinct 'd_nbl_track' from x_table
where not exists (select 1 from x_table where name='d_nbl_track');
update x_table set version='1.0' where name='d_nbl_track';

create index d_nbl_track_track_key
on d_nbl_track (
	recording_id,
	track
);
create index d_nbl_track_itis_tsn_key
on d_nbl_track (
	itis_tsn
);
create index d_nbl_track_taxon_key
on d_nbl_track (
	taxon
);
create index d_nbl_track_animal_label_key
on d_nbl_track (
	animal_label
);
