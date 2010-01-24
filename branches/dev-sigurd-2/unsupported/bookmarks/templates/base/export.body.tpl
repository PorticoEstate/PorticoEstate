<form method="post" action="{FORM_ACTION}">
 <table border=0 bgcolor="#EEEEEE" align="center" width=60%>
 <tr>
  <td colspan=2>Choisissez les catégories que voulez exporter<br>&nbsp;
  </td>
 </tr>
 <tr>
   <td>
  {input_categories}
   </td>
 </tr>
 <tr>
  <td colspan=2>Choisissez le type de fichier vers lequel vous voulez exporter les signets<br>&nbsp;
  </td>
 </tr>
 <tr>
   <td>
  	<select name="exporttype"><option>Netscape/Mozilla</option><option>XBEL</option></select>
   </td>
 </tr>
 <tr>
  <td colspan=2 align=right>
   <input type="submit" name="export" value="Exporter Signets">
  </td>
 </tr>
</table>
</form>
