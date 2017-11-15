/*
	Creator: Mats J Svensson, CAnMove
*/

create table r_taxon
(
	tsn					integer primary key,
	complete_name		varchar(100) not null,
	unit_name1			varchar(35) not null,
	unit_name2			varchar(35),
	unit_name3			varchar(35),
	parent_tsn			integer not null,
	rank_id				integer not null,
	name_usage			varchar(7) not null,
	unaccept_reason		varchar(50),
	credibility_rtng	varchar(40) not null,
	update_date			date not null
);
insert into x_table (name) select distinct 'r_taxon' from x_table
where not exists (select 1 from x_table where name='r_taxon');
update x_table set version='1.0' where name='r_taxon';

create index r_taxon_complete_name_key
on r_taxon (
	complete_name
);

create index r_taxon_parent_tsn_key
on r_taxon (
	parent_tsn
);
