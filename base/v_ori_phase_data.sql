/*
	Creator: Mats J Svensson, CAnMove
*/

create or replace view v_ori_phase_data as
select
	d.dataset_id,
	d.project_id,
	d.title,
	d.data_type,
	d.storage_type,
	a.animal_id,
	a.animal,
	a.itis_tsn,
	a.taxon,
	c.capture_id,
	c.capture_time,
	e.experiment_id,
	e.setup_id,
	e.experiment_no,
	e.experiment_type,
	p.phase_id,
	p.phase_no,
	pd.phase_data_id,
	pd.order_no,
	pd.data_id,
	pd.data_value
from
	p_dataset d,
	d_ori_animal a,
	d_ori_capture c,
	d_ori_experiment e,
	d_ori_phase p,
	d_ori_phase_data pd
where d.dataset_id = a.dataset_id
  and a.animal_id = c.animal_id
  and c.capture_id = e.capture_id
  and e.experiment_id = p.experiment_id
  and p.phase_id = pd.phase_id
;
