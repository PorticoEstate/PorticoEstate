<!-- BEGIN addjobdetail.tpl -->
<center><h3>{page_title}</h3></center>
<center><h4>{fullname}</h4></center>
<form method="POST" name="{formname}" action="{postlink}">
<center>
<input type=hidden name=n_employee value="{hidden_name}">
<table border=0 width=65%>
<tr>
  <td>{company_label}</td><td>{company_element}</td>
</tr>
<tr>
  <td>{jobnum_label}</td><td>{jobnum_element}</td>
</tr>
<tr>
  <td>{catagory_label}</td><td>{catagory_element}</td>
</tr>
<tr>
  <td>{workdate_label}</td><td>{workdate_element}</td>
</tr>
<tr>
  <td>{starttime_label}</td><td>{starttime_element}</td>
</tr>
<tr>
  <td>{endtime_label}</td><td>{endtime_element}</td>
</tr>
<tr>
  <td>{hoursworked_label}</td><td>{hoursworked_element}</td>
</tr>
<tr>
  <td>{billable_label}</td><td>{billable_element}</td>
</tr>
<tr>
  <td>{comments_label}</td><td>{comments_element}</td>
</tr>
<tr>
  <td colspan=2>{submit_bar}</td>
</tr>
</table>
</center>
</form>
