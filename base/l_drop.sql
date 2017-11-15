/*
	Creator: Mats J Svensson, CAnMove
	Drop all load tables
*/

\i l_gen_drop.sql
\i l_nbl_drop.sql
\i l_ori_drop.sql
\i l_orn_drop.sql

drop table l_load;
drop table l_file_log;
drop table l_file;
drop table l_database;
drop table l_column;
