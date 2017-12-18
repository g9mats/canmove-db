<?php
// Creator: Mats J Svensson, CAnMove

error_reporting (E_ALL);
$project_id=$_POST['project_id'];
if ($project_id=="") {
	echo "<p>You must specify a project.</p>";
	return;
}
require_once $DBRoot."/lib/DBLink.php";

$sql_pid="
select r.person_id, p.user_role
from r_person r, p_project_role p
where r.person_id = p.user_id
  and p.project_id = $1
  and r.drupal_id = $2
";

$sql="
select
	'A' as dummy,
	r.person_id,
	r.first_name||' '||r.last_name||'(Owner)' as name
from r_person r, p_project_role p
where r.person_id = p.user_id
  and p.project_id = $1
  and p.user_role = 'O'
  and 1 < (
  	select count(*) from p_project_role
	where project_id = $1
	  and user_role = 'O'
	)
union
select
	'A' as dummy,
	r.person_id,
	r.first_name||' '||r.last_name||'('||
	case p.user_role
		when 'A' then 'Admin'
		when 'R' then 'Read'
		else p.user_role
	end ||')' as name
from r_person r, p_project_role p
where r.person_id = p.user_id
  and p.project_id = $1
  and p.user_role <> 'O'
union
select
	'B' as dummy,
	r.person_id,
	r.first_name||' '||r.last_name as name
from r_person r left outer join p_project_role p on
	r.person_id = p.user_id
	and p.project_id = $1
where p.user_role is null
order by dummy, name
";

$db = new DBLink("localhost", $CMDatabase, $Username);
$db->connect();

// Get users person_id and user_role
$res = $db->query($sql_pid, array($project_id,$user->uid));
$pid = $res[0]['person_id'];
$role = $res[0]['user_role'];
?>

<p>
Select user and role in the listboxes.
</p>
<p>
If you pick a user with existing role it will be superseeded.
</p>

<!-- Form for selection of user and role -->
<form action="<?php echo $DrAction ?>" method="post">
	<input name="next_step" value="3" type="hidden" />
	<input name="project_id" value="<?php echo $project_id; ?>" type="hidden" />
	<table><tr>
	<td>User:</td>
	<td><select id="person_id" name="person_id" required="required" onchange="set_roles()">
		<option value="" selected>Select user</option>
<?php
	if ($res = $db->query($sql, array($project_id))) {
		foreach ($res as $row)
			echo "<option value='".$row['person_id']."'>".
				$row['name']."</option>";
	}
?>
	</select></td>
	</tr><tr>
	<td>Role:</td>
	<td><select id="user_role" name="user_role" required="required">
	</select></td>
	</tr><tr>
	<td></td>
	<td><button type="submit">Grant</button></td>
	</tr></table>
</form>

<script>
function set_roles(pid,role) {
	var person_id=document.getElementById("person_id");
	var user_role=document.getElementById("user_role");
	var opt;
	var pid=<?php echo $pid; ?>;
	var role=<?php echo '"'.$role.'"'; ?>;

	while (user_role.length > 0)
		user_role.remove(0);
	if (person_id.value!="") {
		if (person_id.value==pid) {
			opt=document.createElement("option");
			opt.value="O"; opt.text="Owner"; user_role.add(opt);
			if (role=="O") opt.selectedIndex="0";
			opt=document.createElement("option");
			opt.value="A"; opt.text="Admin"; user_role.add(opt);
			if (role=="A") user_role.selectedIndex="1";
		} else {
			opt=document.createElement("option");
			opt.value="O"; opt.text="Owner"; user_role.add(opt);
			opt=document.createElement("option");
			opt.value="A"; opt.text="Admin"; user_role.add(opt);
			opt=document.createElement("option");
			opt.value="R"; opt.text="Read"; user_role.add(opt);
			user_role.selectedIndex="2";
		}
	}
}
</script>

<?php $db->disconnect(); ?>
