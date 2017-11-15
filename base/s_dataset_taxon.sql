/*
	Creator: Mats J Svensson, CAnMove
*/

create or replace function s_dataset_taxon (integer, varchar = '; ')
	returns varchar as $$
declare
	i integer = 0;
	taxon_str varchar = '';
	get_taxon cursor is
		select
			taxon
		from p_taxon
		where dataset_id = $1
		order by taxon;
begin
	for row in get_taxon loop
		if i > 0 then
			taxon_str = taxon_str||$2;
		end if;
		taxon_str = taxon_str||row.taxon;
		i = i + 1;
	end loop;
	return taxon_str;
end;
$$ language plpgsql;
