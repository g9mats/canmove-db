/*
	Creator: Mats J Svensson, CAnMove
*/

create or replace function s_nvl (varchar,varchar)
	returns varchar as $$
begin
	if ($1 = '') then
		return $2;
	end if;
	return $1;
end;
$$ language plpgsql;
