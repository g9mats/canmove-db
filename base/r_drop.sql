/*
	Creator: Mats J Svensson, CAnMove
	Drop all reference tables
*/

\i r_nbl_drop.sql
\i r_ori_drop.sql
\i r_orn_drop.sql
\i r_trr_drop.sql

drop table r_beaufort;
drop table r_bird_name;
drop table r_device_model;
drop table r_identity;
drop table r_migration_phase;
drop table r_person;

drop table r_taxon;
drop table r_taxon_synonym;

drop table r_data_alias;
drop table r_data;

drop table r_data_subset;
drop table r_data_type;
drop table r_storage_type;
