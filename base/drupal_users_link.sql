/*
	Creator: Mats J Svensson, CAnMove
*/

create table drupal_users_link
(
	uid					integer not null unique,
	name				varchar(60) not null unique,
	mail				varchar(64) not null
);
insert into x_table (name) select distinct 'drupal_users_link' from x_table
where not exists (select 1 from x_table where name='drupal_users_link');
update x_table set version='1.0' where name='drupal_users_link';

create index drupal_users_link_mail_key
on drupal_users_link (
	mail
);
