/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_trr_track
(
	track_id			serial primary key,
	dataset_id			integer not null references p_dataset (dataset_id),
	start_time			timestamp not null,
	site_id				integer not null references r_trr_site (site_id),
	radar_id			integer not null references r_trr_radar (radar_id),
	track_type			char(1) not null references r_trr_track_type (track_type),
	operator_id			integer not null references r_person (person_id), 
	quality_rating		char(1) not null references r_trr_quality (quality_rating),
	overall_remark		text,
	edit_remark			text,
unique (dataset_id,start_time)
);
insert into x_table (name) select distinct 'd_trr_track' from x_table
where not exists (select 1 from x_table where name='d_trr_track');
update x_table set version='1.0' where name='d_trr_track';

create index d_trr_track_start_time_key
on d_trr_track (
	start_time
);
