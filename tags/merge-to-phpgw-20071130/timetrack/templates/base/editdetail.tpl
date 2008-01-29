<!-- BEGIN editdetail.tpl -->
<center><h3>{page_title}</h3></center>
<center><h4>{detailid_label} : {detailid_element}</h4></center>
<form method="POST" name="{formname}" action="{postlink}">
<center>
<input type=hidden name=n_detail_id value="{hidden_detail_id}">
<input type=hidden name=n_employee value="{hidden_employee_id}">
<input type=hidden name=n_job_id value="{hidden_job_id}">
<table border=0 width=65%>
<tr>
  <td>{employee_label}</td><td>{employee_element}</td>
</tr>
<tr>
  <td>{company_label}</td><td>{company_element}</td>
</tr>
<tr>
  <td>{jobnum_label}</td><td>{jobnum_element}</td>
</tr>
<tr>
  <td>{jobrev_label}</td><td>{jobrev_element}</td>
</tr>
<tr>
  <td>{summary_label}</td><td>{summary_element}</td>
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
  <td width="10%">
    {submit_update}
  </td>
  <td width="10%">
    {submit_new}
  </td>
  <td width="10%">
    {cancel_link}
  </td>
  <td align=right>
    {delete_link}
  </td>
</tr>
</table>
</center>
</form>
