/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_nbl_track_data
(
	track_data_id		serial primary key,
	track_id			integer not null references d_nbl_track (track_id),
	order_no			integer not null,
	data_id				integer not null references r_data (data_id),
	data_value			varchar(100)
);
insert into x_table (name) select distinct 'd_nbl_track_data' from x_table
where not exists (select 1 from x_table where name='d_nbl_track_data');
update x_table set version='1.0' where name='d_nbl_track_data';

create index d_nbl_track_data_order_no_key
on d_nbl_track_data (
	track_id,
	order_no
);
create index d_nbl_track_data_data_id_key
on d_nbl_track_data (
	data_id
);
