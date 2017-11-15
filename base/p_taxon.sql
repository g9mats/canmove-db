/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_taxon
(
	taxon_id			serial primary key,
	dataset_id			integer not null references p_dataset (dataset_id),
	itis_tsn			integer not null,
	taxon				varchar(60) not null,
	remark				text,
unique (dataset_id, taxon)
);
insert into x_table (name) select distinct 'p_taxon' from x_table
where not exists (select 1 from x_table where name='p_taxon');
update x_table set version='1.0' where name='p_taxon';

create index p_taxon_itis_tsn
on p_taxon (
	itis_tsn
);
create index p_taxon_taxon_key
on p_taxon (
	taxon
);
