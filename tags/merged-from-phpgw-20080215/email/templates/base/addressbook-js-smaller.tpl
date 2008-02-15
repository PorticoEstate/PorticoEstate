<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
         
  <meta http-equiv="CONTENT-TYPE" content="text/html; charset={charset}">
  <title>{title}</title>
                  
  <script src="%7Binclude_link%7D"> </script>
</head>
  <body
 onload="setUpVisual('{main_form_name}','nameselect','searchautocomplete');">
 {V_addressbook_record} 	
<table style="width: 750px;" cellpadding="0" border="1" cellspacing="0">
     <thead> 	<tr>
 	<td valign="top" align="center" bgcolor="#336699" width="30%">		 		<font
 size="-1" face="Helvetica, Arial, sans-serif" color="#ffffff">			 			{lang_select_cats} 
		</font> 		<font color="#ffffff">		 			<br>
 		</font>		 		</td>
 		<td valign="top" align="center" bgcolor="#336699">		               			
    <div align="center">			 			      <font
 face="Helvetica, Arial, sans-serif" size="-1" color="#ffffff"> 				    
 Select users  			      </font>			 			      <font color="#ffffff">				 				
     <br>
 				</font>		 			</div>
 		<font color="#ffffff">			 		<br>
 		</font>		 		</td>
 		<td valign="top"
 style="text-align: center; background-color: rgb(51,102,153);"
 rowspan="1" colspan="3"> 	      
    <div align="center"><font face="Helvetica, Arial, sans-serif"
 size="-1" color="#ffffff"> 		Destination Options  	      </font>			 	  
   <font color="#ffffff">			 	      <br>
 	      </font>		 	      </div>
 	      </td>
 	      </tr>
     <tr bgcolor="#cccccc">
       <td colspan="5" valign="middle" width="600">&nbsp;</td>
     </tr>
     <tr>
     
    <form action="{cats_action}" name="cats_form" method="post"></form>
       <td rowspan="1" width="100" valign="top" align="center"
 bgcolor="#cccccc">                    
    <select name="cat_id" size="20" onchange="this.form.submit()">
    <option value="-2">All (can be very slow)</option>
    </select>
       </td>
            
    <form name="{main_form_name}" action="{main_form_action}"
 method="post"
 onsubmit="     											mover.sortSelect('toselectbox[]');
										     mover.sortSelect('ccselectbox[]');
 										    mover.sortSelect('bccselectbox[]');
 										    mover.sortSelect('nameselect');
     "></form>
        <td colspan="1" width="100" valign="top" rowspan="1"
 bgcolor="#cccccc">                    
    <table width="100">
       <tbody>
 	<tr align="center">
 		<td width="50">			 			<input type="text" name="searchbox"
 value="{searchbox_value}">		 		</td>
 		<td width="50">		 		<br>
          </td>
 	</tr>
 	                           
      </tbody>                    
    </table>
                     
    <table width="20">
 	<tbody>
 	<tr>
 		<td width="80%"> 			<input type="text" name="searchautocomplete"
 value="{search_ac_value}" size="18"
 onkeyup="javascript:obj1.bldUpdate();">	 		</td>
 		<td width="20%">	                           	            
          <select name="filter" onchange="this.form.submit()">
          <option value="none" {global_is_selected=""> Global </option>
          <option value="mine" {mine_is_selected=""> Mine </option>
          <option value="user_only" {private_is_selected=""> Private </option>
          </select>
 		</td>
 	</tr>
 	                           
      </tbody>                    
    </table>
       <br>
                     
    <select name="nameselect" size="15" multiple=""
 onchange="      									if(this.form.viewmore.checked)
      										{
										if(mover.numberSelectedOptions('nameselect') == 1)
											{
												javascript:
												mover.selectAll('ccselectbox[]');
												mover.selectAll('toselectbox[]');
												mover.selectAll('bccselectbox[]');
												this.form.submit();
											}
										}">
<!-- BEGIN addressbook_names -->
    <option value="{name_option_value}" {name_option_selected="">{name_option_name}</option>
<!-- END addressbook_names -->
    </select>
 {V_hidden_emails_list}	 	<br>
 	<font face="Helvetica, Arial, sans-serif" size="-1"> 	<br>
 	More Data <input type="checkbox" {viewmore_checked="" name="viewmore"
 onclick="this.form.submit()">         	</font> 	</td>
  	<td rowspan="1" width="48" valign="top" align="center"
 bgcolor="#cccccc">					 		<br>
 		
    <p> 			<input type="button" name="tobutton" value="TO: "
 onclick="javascript:mover.moveSelectedOptions('nameselect','toselectbox[]');"> 
	     	</p>
 		<br>
 		<br>
 		<br>
  		
    <p> 			<input type="button" name="ccbutton" value="CC: "
 onclick="javascript:mover.moveSelectedOptions('nameselect','ccselectbox[]');"> 
		</p>
 		<br>
 		<br>
  		
    <p> 			<input type="button" name="bccbutton" value="BCC:"
 onclick="javascript:mover.moveSelectedOptions('nameselect','bccselectbox[]');"> 
		</p>
 		<br>
 		<br>
 		
    <hr> 		
    <p> 			<input type="button" name="nonebutton" value="None"
 onclick="javascript:mover.moveAll('toselectbox[]','nameselect'); mover.moveAll('ccselectbox[]','nameselect'); mover.moveAll('bccselectbox[]','nameselect');"> 
		</p>
 		
    <p> 			<br>
 		</p>
 	</td>
 	<td rowspan="1" width="30%" align="center" valign="top" colspan="1"
 bgcolor="#cccccc">              		
    <p>              			
    <select name="toselectbox[]" size="5" multiple=""
 onchange="      									if(this.form.viewmore.checked)
      										{
										if(mover.numberSelectedOptions('toselectbox[]') == 1)
											{
												javascript:
												mover.selectAll('ccselectbox[]');
												mover.selectAll('toselectbox[]');
												mover.selectAll('bccselectbox[]');
												this.form.submit();
											}
										}">
    </select>
 		</p>
  		
    <p>              			
    <select name="ccselectbox[]" size="5" multiple=""
 onchange="      									if(this.form.viewmore.checked)
      										{
										if(mover.numberSelectedOptions('ccselectbox[]') == 1)
											{
												javascript:
												mover.selectAll('ccselectbox[]');
												mover.selectAll('toselectbox[]');
												mover.selectAll('bccselectbox[]');
												this.form.submit();
											}
										}">
    </select>
 			<br>
 		</p>
  		
    <p style="margin-bottom: 0cm;">              			
    <select name="bccselectbox[]" size="5" multiple=""
 onchange="      											if(this.form.viewmore.checked)
      											{
											if(mover.numberSelectedOptions('ccselectbox[]') == 1)
												{
													javascript:
													mover.selectAll('ccselectbox[]');
													mover.selectAll('toselectbox[]');
													mover.selectAll('bccselectbox[]');
													this.form.submit();
												}
											}">
    </select>
 		</p>
 		&nbsp;               		
    <p> 		<input type="button" name="removeselectedbutton"
 value="&lt;- Remove selected"
 onclick="javascript:mover.moveSelectedOptions('toselectbox[]','nameselect'); mover.moveSelectedOptions('bccselectbox[]','nameselect'); mover.moveSelectedOptions('ccselectbox[]','nameselect');"></p>
 		<input type="submit" name="done" value="Done"
 onclick="javascript:
									mover.stringToTextbox(
										mover.selectToString('toselectbox[]'),
										window.opener.document.doit.to
											     );
									mover.stringToTextbox(
										mover.selectToString('bccselectbox[]'),
										window.opener.document.doit.bcc
											     );
									mover.stringToTextbox(
										mover.selectToString('ccselectbox[]'),
										window.opener.document.doit.cc
											     );
									window.close();
									 "> 
		
    <p></p>
 		 		<br>
 		<br>
 	</td>
 	</tr>
 		</thead>       
</table>
 <br>
 <br>
</body>
</html>
