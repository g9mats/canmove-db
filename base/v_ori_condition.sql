/*
	Creator: Mats J Svensson, CAnMove
*/

create or replace view v_ori_condition as
select
	d.dataset_id,
	d.project_id,
	d.title,
	d.data_type,
	d.storage_type,
	x.context_id,
	x.context,
	c.condition_id,
	c.condition_type,
	c.condition_value
from
	p_dataset d,
	p_ori_context x,
	p_ori_condition c
where d.dataset_id = x.dataset_id
  and x.context_id = c.context_id
;
