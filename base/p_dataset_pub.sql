/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_dataset_pub
(
	pub_dataset_id		serial primary key,
	publication_id		integer not null references p_publication (publication_id),
	dataset_id			integer not null references p_dataset (dataset_id)
);
insert into x_table (name) select distinct 'p_dataset_pub' from x_table
where not exists (select 1 from x_table where name='p_dataset_pub');
update x_table set version='1.0' where name='p_dataset_pub';

create index p_dataset_pub_publication_id_key
on p_dataset_pub (
	publication_id
);
create index p_dataset_pub_dataset_id_key
on p_dataset_pub (
	dataset_id
);
