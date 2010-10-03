<style>
	td
	{
		text-align: left;
	}

	.rate_scale td
	{
		text-align: center;
	}

	th.activetab
  {
	color:#000000;
	text-align: center;
	background-color:#D3DCE3;
	border-top-width : 2px;
	border-top-style : solid;
	border-top-color : Black;
	border-left-width : 2px;
	border-left-style : solid;
	border-left-color : Black;
	border-right-width : 2px;
	border-right-style : solid;
	border-right-color : Black;
  }

  th.inactivetab
  {
	color:#000000;
	text-align: center;
	background-color:#E8F0F0;
	border-width : 1px;
	border-style : solid;
	border-color : Black;
	border-bottom-width : 2px;
	border-bottom-style : solid;
	border-bottom-color : Black;
  }

  table.tabcontent
  {
	border-bottom-width : 2px;
	border-bottom-style : solid;
	border-bottom-color : Black;
	border-left-width : 2px;
	border-left-style : solid;
	border-left-color : Black;
	border-right-width : 2px;
	border-right-style : solid;
	border-right-color : Black;
  }

  .td_left { border-left : 1px solid Gray; border-top : 1px solid Gray; }
  .td_right { border-right : 1px solid Gray; border-top : 1px solid Gray; }

  div.activetab{ display:inline; }
  div.inactivetab{ display:none; }

	.graph_table td, .graph_table tr
	{
		padding: 0;
		margin: 0;
		vertical-align: bottom;
	}

  .graph_table div
  {
	  	padding: 0;
		margin: 0;
		font-size: 1px;	/* hack so that poor IE can show small divs */
		background-color: blue;
		border-left:1px solid black;
		border-top:1px solid black;	
		border-right:1px solid black;
	}

</style>
{message}
{search_tpl}
<div align="center">
	<script type="text/javascript">
		var tab = new Tabs(3,'activetab','inactivetab','tab','tabcontent','','','tabpage');
	</script>
		<table  width="100%" border="0" cellspacing="0" cellpadding="3" style='border:1px solid black'>
			<tr class="th">
				<td colspan=3>
					<div style='font:bold 12px sans-serif;position: relative'>
						{lang_article} {art_id}&nbsp;&nbsp;&nbsp;{img_stars}&nbsp;&nbsp;&nbsp;{lang_unpublished}
							<span style='width:100%; position: absolute;right:0; text-align:right;'>
								<a href="{href_printer}" TARGET="article_print"><img src="{img_printer}" alt="{alt_printer}" title="{alt_printer}"></a>&nbsp;&nbsp;<a href="{href_mail}"><img src="{img_mail}" alt="{alt_mail}" title="{alt_mail}"></a>
							</span>
					</div>
				</td>
			</tr>
			<tr>
				<td style="text-align:right"><span style='font:normal 12px sans-serif'>{lang_title}:</span></td>
				<td style='font:bold 13px sans-serif'>{title}</td>
				<td width=30%>{createdby}</td>
			</tr>
			<tr>
				<td style="text-align:right"><span style='font:normal 12px sans-serif'>{lang_topic}:</span></td>
				<td colspan=2 style='font:normal 12px sans-serif'>{topic}</td>
			</tr>
			<tr>
				<td width=1% style="text-align:right"><span style='font:normal 12px sans-serif'>{lang_category}:</span></td>
				<td><span>{links_cats}</span></td>
				<td>{last_modif}</td>
			</tr>
			<tr>
				<td style="text-align:right"><span style='font:normal 12px sans-serif'>{lang_keywords}:</span></td>
				<td colspan=2 style='font:normal 12px sans-serif'>{keywords}</td>
			</tr>
			<!-- BEGIN easy_question_block -->
			<tr bgcolor="{tr_bgcolor}">
				<td colspan=3>
					<form method="POST" action="{form_easy_q_action}">
						{lang_question_easy}&nbsp;&nbsp;&nbsp;
						<input type="hidden" name="feedback_query" value="{query}">
						<input type="submit" name="yes_easy" value="{lang_yes}">&nbsp;&nbsp;
						<input type="submit" name="no_easy" value="{lang_no}">
					</form>
				</td>
			</tr>
			<tr bgcolor="{tr_bgcolor}">
				<td colspan=3>(<i>{lang_please}</i>)</td>
			</tr>
			<!-- END easy_question_block -->
			<tr>
				<td colspan=3 style='padding:0'>
					<table width="100%" border="0" cellspacing="0" cellpading="0" >
						<tr>
							<th id="tab1" class="activetab" onclick="javascript:tab.display(1);"><a href="#" tabindex="0" accesskey="1" onfocus="tab.display(1);" onclick="tab.display(1); return(false);">&nbsp; {lang_article}</a></th>
							<th id="tab2" class="activetab" onclick="javascript:tab.display(2);"><a href="#" tabindex="0" accesskey="2" onfocus="tab.display(2);" onclick="tab.display(2); return(false);">&nbsp; {lang_linksfiles} &nbsp;</a></th>
							<th id="tab3" class="activetab" onclick="javascript:tab.display(3);"><a href="#" tabindex="0" accesskey="2" onfocus="tab.display(3);" onclick="tab.display(3); return(false);">&nbsp; {lang_history}&nbsp;</a></th>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<div id="tabcontent1" class="inactivetab">
			<table class="tabcontent" width="100%" style="padding: 10px"border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td colspan=3>{content}</td>
				</tr>
				<tr>
					<td colspan=3>
						<table border=0>
							<tr>
								<td>
									<!-- BEGIN edit_del_block -->
									<form method="POST" action ="{form_edit_art}">
										<input type="submit" name="edit_article" value="{lang_edit_art}">
									</form>
								</td>
								<td>
									<form method="POST" action ="{form_del_art}">
										&nbsp;&nbsp;<input type="submit" name="delete_article" value="{lang_delete_article}">
									</form>
									<!-- END edit_del_block -->
								</td>
								<td>
									<!-- BEGIN publish_btn_block -->
									<form method="POST" action ="{form_publish_art}">
										&nbsp;&nbsp;<input type="submit" name="publish_article" value="{lang_publish_article}">
									</form>
									<!-- END publish_btn_block -->
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="th">
					<td colspan=3><b>{lang_comments}</b></td>
				</tr>
				<tr>
					<td colspan=3>
						<ul>
						<!-- BEGIN comment_block -->
						<li><b>{comment_date} -	{comment_user}</b>&nbsp;&nbsp;{link_publish}&nbsp;&nbsp;{link_delete}<br>
						{comment_content}
						<!-- END comment_block -->
						</ul>
						<div>{link_more_comments}</div>
					</td>
				</tr>
				<tr>
					<form method='POST' action="{form_article_action}">
						<!-- BEGIN comment_form_block -->
						<td style='padding-top:20px;padding-left:20px' width=1%>
							<b>{lang_add_comments}:</b><br>
							<textarea cols=40 rows=5 name="comment_box"></textarea>
						</td>
						<!-- END comment_form_block -->
						<td>
							<table border=0><tr><td style="text-align:center" valign=bottom>
								<tr>
									<td>
										<table>
											<!-- BEGIN rating_form_block -->
											<tr>
												<td colspan=7>{lang_please_rate}:</td> 
											<tr class="rate_scale">
												<td></td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td></td>
											</tr>
											<tr>
												<td>{lang_poor}</td>
												<td><input id="Rate1" type="radio" name="Rate" value="1" /></td>
												<td><input id="Rate1" type="radio" name="Rate" value="2" /></td>
												<td><input id="Rate1" type="radio" name="Rate" value="3" /></td>
												<td><input id="Rate1" type="radio" name="Rate" value="4" /></td>
												<td><input id="Rate1" type="radio" name="Rate" value="5" /></td>
												<td>{lang_excellent}</td>
											</tr>
											<!-- END rating_form_block -->
											{submit_comment}
										</table>
									</td>
								</tr>
							</table>
						</td>
						<td style="text-align:center">
						<!-- BEGIN rating_graph_block -->
						{lang_average}: <b>{average_rating}</b><br>
						<b>{numpeople}</b> {lang_people}
						<center>
						<table class="graph_table" border=0 width=100 cellpadding=0 cellspacing=0>
							<tr>
								<td><div style='height:{bar_1}px'></div></td>
								<td><div style='height:{bar_2}px'></div></td>
								<td><div style='height:{bar_3}px'></div></td>
								<td><div style='height:{bar_4}px'></div></td>
								<td><div style='height:{bar_5}px'></div></td>
							</tr>
							<tr class="rate_scale">
								<td>1</td><td>2</td><td>3</td><td>4</td><td>5</td>
							</tr>
						</table>
						</center>
						<!-- END rating_graph_block -->
						</td>
					</form>
				</tr>
			</table>
		</div>

		<div id="tabcontent2" class="inactivetab">
			<table class="tabcontent" width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr class="th">
					<td><b>{lang_attached_files}</b></td>
				</tr>
				<tr>
					<td>
						<form method="POST" action="{form_del_action}">
							<ul>
							<!-- BEGIN file_item_block -->
							<li>
								{img_delete}
								<a href="{href_file}">{file_name}</a>: {file_comment}
							</li>
							<!-- END file_item_block -->
							</ul>
						</form>
					</td>
				</tr>
				<tr>
					<td>
						<!-- BEGIN file_upload_block -->
						<form method=POST name="file_form" action="{form_file_action}" enctype="multipart/form-data">
							{lang_attach_file}: <input type='file' name='new_file'>&nbsp;&nbsp;
							{lang_comment}: &nbsp;<input type="text" name="file_comment">&nbsp;&nbsp;
							<input type=submit name="upload" value="{lang_upload}">
						</form>
						<!-- END file_upload_block -->
					</td>
				</tr>
				<tr class="th">
					<td><b>{lang_related_articles}</b></td>
				</tr>
				<tr>
					<td>
						<form method="POST" action="{form_del_action}">
							<ul>
							<!-- BEGIN related_article_block -->
							<li>
								{img_delete}
								({related_id}) <a href="{href_related}">{title_related}</a>
							</li>
							<!-- END related_article_block -->
							</ul>
						</form>
					</td>
				</tr>
				<tr>
					<td>
						<!-- BEGIN related_article_add_block -->
						<form method="POST" name="add_article_form" action="{form_add_article_action}">
							&nbsp;&nbsp;&nbsp;{lang_add_related}: &nbsp;<input type="text" name="related_articles" size="40" value="" readonly>
							<input type="button" value="{lang_select_articles}" onClick="openpopup();">
							&nbsp;&nbsp;<input type="button" value="{lang_clear}" onClick="document.add_article_form.related_articles.value='';">
							&nbsp;&nbsp;<input type="submit" name="update_related" value="{lang_update}">
						</form>
						<!-- END related_article_add_block -->
					</td>
				</tr>
				<tr class=th>
					<td><b>{lang_links}</b></td>
				</tr>
				<tr>
					<td>
						<form method="POST" action="{form_del_action}">
							<ul>
							<!-- BEGIN links_block -->
							<li>
								{img_delete}
								<a href="{href_link}" target="external_link">{title_link}</a>
							</li>
							<!-- END links_block -->
							</ul>
						</form>
					</td>
				</tr>
				<tr>
					<td>
						<!-- BEGIN links_add_block -->
						<form method="POST" name="add_link_form" action="{form_add_link_action}">
							&nbsp;&nbsp;{lang_add_link}: &nbsp;<input type="text" name="url" size="40">
							&nbsp;{lang_title}: &nbsp;<input type="text" name="url_title" size="20">
							&nbsp;<input type="submit" name="submit_link" value="{lang_update}">
						</form>
						<!-- END links_add_block -->
					</td>
				</tr>
			</table>
		</div>

		<div id="tabcontent3" class="inactivetab">
			<table class="tabcontent" width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr class="th">
					<td><b>{lang_history}</b></td>
				</tr>
				<tr>
					<td>
						<table border=0 width=100%>
							<tr class="th">
								<td><b>{lang_date}</b></td><td><b>{lang_user}</b></td><td><b>{lang_action}</b></td>
							</tr>
							<!-- BEGIN history_line_block -->
							<tr bgcolor="{tr_color}">
								<td>{history_date}</td>
								<td>{history_user}</td>
								<td>{history_action}</td>
							</tr>
							<!-- END history_line_block -->
						</table>
					</td>
				</tr>
			</table>
		</div>

</div>

<!-- BEGIN img_delete_block -->
<a href="{href_del}"><img src="{img_src_del}" alt="{lang_delete}" title="{lang_delete}" style="width:16px; height:16px"></a>
<!-- END img_delete_block -->
