/*
	Creator: Mats J Svensson, CAnMove
*/

create or replace view v_ori_setup as
select
	d.dataset_id,
	d.project_id,
	d.title,
	d.data_type,
	d.storage_type,
	s.setup_id,
	s.setup
from
	p_dataset d,
	p_ori_setup s
where d.dataset_id = s.dataset_id
;
