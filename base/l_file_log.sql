/*
	Creator: Mats J Svensson, CAnMove
*/

create table l_file_log
(
	file_log_id			serial primary key,
	file_id				integer not null references l_file (file_id),
	log_action			varchar(10) not null,
	log_time			timestamp not null
);
insert into x_table (name) select distinct 'l_file_log' from x_table
where not exists (select 1 from x_table where name='l_file_log');
update x_table set version='1.0' where name='l_file_log';

create index l_file_log_file_id_key
on l_file_log (
	file_id,
	log_time
);
