/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_trr_activity
(
	activity_id			serial primary key,
	dataset_id			integer not null references p_dataset (dataset_id),
	site_id				integer not null references r_trr_site (site_id),
	change_type			varchar(15) not null,
	change_time			timestamp not null
);
insert into x_table (name) select distinct 'd_trr_activity' from x_table
where not exists (select 1 from x_table where name='d_trr_activity');
update x_table set version='1.0' where name='d_trr_activity';

create index d_trr_activity_dataset_id_key
on d_trr_activity (
	dataset_id,
	change_time
);
