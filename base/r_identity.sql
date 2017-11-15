/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_identity
(
	identity_id			serial primary key,
	id_type				varchar(4) not null unique,
	id_name				varchar(50) not null,
	remark				text
);
insert into x_table (name) select distinct 'r_identity' from x_table
where not exists (select 1 from x_table where name='r_identity');
update x_table set version='1.0' where name='r_identity';
