
select
	p.link_title,
	s.link_title,
	u.alias
from
	drupal_menu_links p,
	drupal_menu_links s,
	drupal_node n,
	drupal_url_alias u
where p.mlid = s.plid
 and s.link_title = n.title
 and 'node/'||n.nid = u.source
 and p.menu_name='menu-action-menu'
 and s.menu_name='menu-action-menu'
order by p.weight,s.weight;
