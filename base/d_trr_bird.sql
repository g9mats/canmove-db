/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_trr_bird
(
	bird_id				serial primary key,
	track_id			integer not null unique references d_trr_track (track_id),
	target_type			char(1) not null references r_trr_target (target_type),
	target_remark		text
);
insert into x_table (name) select distinct 'd_trr_bird' from x_table
where not exists (select 1 from x_table where name='d_trr_bird');
update x_table set version='1.0' where name='d_trr_bird';

create index d_trr_bird_target_type_key
on d_trr_bird (
	target_type
);
