<h1>Places</h1>
<h2>Projects</h2>
<!-- BEGIN projects_bloc -->
<a href="{project_link}">{project_name}</a><br/>
<!-- END projects_bloc -->
<h2>Places for project : {active_project_name}</h2>
<!-- BEGIN edit_bloc -->
<table >
<tr class="head_class">
<td colspan="2">Type</td>
<td>Place</td>
<td colspan="2">actions</td>
</tr>
<!-- BEGIN types_bloc -->
<tr class="{tr_class}">
<td><form name="doc_types-{type_id_value}" method="POST"></td>
<td>{project}{type_desc}</td>
<td>{select}</td>
<td><!-- BEGIN action_bloc --><input type="submit" name ="{action_field}" value="{action_value}"/><!-- END action_bloc --></td>
<td></form></td>
</tr>
<!-- END types_bloc -->
</table>
<br/>
<!-- END edit_bloc -->

<br/>
