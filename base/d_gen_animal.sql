/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_gen_animal
(
	animal_id			serial primary key,
	dataset_id			integer not null references p_dataset (dataset_id),
	animal				varchar(10),
	itis_tsn			integer not null,
	taxon				varchar(60) not null,
	remark				varchar(500)
);
insert into x_table (name) select distinct 'd_gen_animal' from x_table
where not exists (select 1 from x_table where name='d_gen_animal');
update x_table set version='1.0' where name='d_gen_animal';

create index d_gen_animal_dataset_id_key
on d_gen_animal (
	dataset_id
);
create index d_gen_animal_animal_key
on d_gen_animal (
	animal
);
create index d_gen_animal_itis_tsn_key
on d_gen_animal (
	itis_tsn
);
create index d_gen_animal_taxon_key
on d_gen_animal (
	taxon
);
