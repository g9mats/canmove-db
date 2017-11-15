/*
	Creator: Mats J Svensson, CAnMove
*/

create table p_publication
(
	publication_id		serial primary key,
	title				varchar(50) not null,
	remark				text
);
insert into x_table (name) select distinct 'p_publication' from x_table
where not exists (select 1 from x_table where name='p_publication');
update x_table set version='1.0' where name='p_publication';

create index p_publication_title_key
on p_publication (
	title
);
