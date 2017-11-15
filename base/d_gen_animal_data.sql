/*
	Creator: Mats J Svensson, CAnMove
*/

create table d_gen_animal_data
(
	animal_data_id		serial primary key,
	animal_id			integer not null references d_gen_animal (animal_id),
	order_no			integer not null,
	data_id				integer not null references r_data (data_id),
	data_value			varchar(100)
);
insert into x_table (name) select distinct 'd_gen_animal_data' from x_table
where not exists (select 1 from x_table where name='d_gen_animal_data');
update x_table set version='1.0' where name='d_gen_animal_data';

create index d_gen_animal_data_order_no_key
on d_gen_animal_data (
	animal_id,
	order_no
);
create index d_gen_animal_data_data_id_key
on d_gen_animal_data (
	data_id
);
