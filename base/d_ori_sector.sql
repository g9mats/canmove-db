/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_ori_sector
(
	sector_id			serial primary key,
	count_id			integer not null references d_ori_count (count_id),
	sector				integer not null,
	amount				integer not null
);
insert into x_table (name) select distinct 'd_ori_sector' from x_table
where not exists (select 1 from x_table where name='d_ori_sector');
update x_table set version='1.0' where name='d_ori_sector';

create index d_ori_sector_count_id_key
on d_ori_sector (
	count_id,
	sector
);
