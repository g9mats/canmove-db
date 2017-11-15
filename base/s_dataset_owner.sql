/*
	Creator: Mats J Svensson, CAnMove
*/

create or replace function s_dataset_owner (integer, varchar = '; ')
	returns varchar as $$
declare
	i integer = 0;
	name_str varchar = '';
	get_owner cursor is
		select
			p.first_name||' '||p.last_name as name
		from p_dataset_role a, r_person p
		where a.user_id = p.person_id
		  and a.dataset_id = $1
		  and a.user_role = 'O'
		order by p.last_name, p.first_name;
begin
	for row in get_owner loop
		if i > 0 then
			name_str = name_str||$2;
		end if;
		name_str = name_str||row.name;
		i = i + 1;
	end loop;
	return name_str;
end;
$$ language plpgsql;

grant execute on function s_dataset_owner(integer, varchar) to canmove_ws;
