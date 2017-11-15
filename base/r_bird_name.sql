/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_bird_name
(
	ref_no				integer primary key,
	taxon				varchar(60) not null,
	eng_name			varchar(50) not null,
	swe_name			varchar(50) not null
);
insert into x_table (name) select distinct 'r_bird_name' from x_table
where not exists (select 1 from x_table where name='r_bird_name');
update x_table set version='1.0' where name='r_bird_name';

create index r_bird_name_taxon_key
on r_bird_name (
	taxon
);
create index r_bird_name_eng_name_key
on r_bird_name (
	eng_name
);
create index r_bird_name_swe_name_key
on r_bird_name (
	swe_name
);
