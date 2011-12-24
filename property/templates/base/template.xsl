<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit_template">
				<xsl:apply-templates select="edit_template"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="edit_hour">
				<xsl:apply-templates select="edit_hour"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="list_template_hour">
				<xsl:apply-templates select="list_template_hour"></xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"></xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list">

		<xsl:apply-templates select="menu"></xsl:apply-templates> 
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="chapter_filter"></xsl:call-template>
				</td>
				<td>
					<xsl:call-template name="user_id_filter"></xsl:call-template>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_template"></xsl:apply-templates>
			<xsl:choose>
				<xsl:when test="values_template[template_id]!=''">					
					<xsl:apply-templates select="values_template"></xsl:apply-templates>
				</xsl:when>
			</xsl:choose>	
		</table>
		<table align="left">
			<xsl:choose>
				<xsl:when test="lookup !=''">
					<xsl:apply-templates select="table_done"></xsl:apply-templates>
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="table_add"></xsl:apply-templates>
				</xsl:otherwise>
			</xsl:choose>
		</table>
	</xsl:template>

	<xsl:template match="table_add">
		<tr>
			<td height="50">
				<xsl:variable name="add_action"><xsl:value-of select="add_action"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_add"><xsl:value-of select="lang_add"></xsl:value-of></xsl:variable>
				<form method="post" action="{$add_action}">
					<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_add_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="table_done">
		<tr>
			<td height="50">
				<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
				<form method="post" action="{$done_action}">
					<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_done_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="table_header_template">
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_template_id"><xsl:value-of select="sort_template_id"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_template_id}"><xsl:value-of select="lang_template_id"></xsl:value-of></a>
			</td>
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"></xsl:value-of></a>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_descr"></xsl:value-of>
			</td>
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_chapter"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_owner"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_entry_date"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="//lookup !=''">
					<td class="th_text" width="5%" align="center">
						<xsl:value-of select="lang_select"></xsl:value-of>
					</td>
				</xsl:when>
				<xsl:otherwise>
					<td class="th_text" width="5%" align="center">
						<xsl:value-of select="lang_view"></xsl:value-of>
					</td>
					<td class="th_text" width="5%" align="center">
						<xsl:value-of select="lang_edit"></xsl:value-of>
					</td>
					<td class="th_text" width="5%" align="center">
						<xsl:value-of select="lang_delete"></xsl:value-of>
					</td>
				</xsl:otherwise>
			</xsl:choose>
		</tr>
	</xsl:template>

	<xsl:template match="values_template">
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_edit_statustext"><xsl:value-of select="lang_edit_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"></xsl:value-of></xsl:variable>
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"></xsl:value-of>
					</xsl:when>
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>row_off</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>row_on</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>

			<td align="right">
				<xsl:value-of select="template_id"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="name"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="descr"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="chapter"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="owner"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="entry_date"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="//lookup !=''">
					<xsl:variable name="form_action_select"><xsl:value-of select="form_action_select"></xsl:value-of></xsl:variable>
					<form method="post" action="{$form_action_select}">
						<td valign="top">
							<input type="hidden" name="template_id" value="{template_id}"></input>
							<input type="hidden" name="workorder_id" value="{workorder_id}"></input>
							<xsl:variable name="lang_select"><xsl:value-of select="lang_select"></xsl:value-of></xsl:variable>
							<input type="submit" name="select" value="{$lang_select}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_select_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</form> 
				</xsl:when>
				<xsl:otherwise>
					<td align="center">
						<xsl:variable name="link_view"><xsl:value-of select="link_view"></xsl:value-of></xsl:variable>
						<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"></xsl:value-of></a>
					</td>
					<td align="center">
						<xsl:variable name="link_edit"><xsl:value-of select="link_edit"></xsl:value-of></xsl:variable>
						<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"></xsl:value-of></a>
					</td>
					<td align="center">
						<xsl:variable name="link_delete"><xsl:value-of select="link_delete"></xsl:value-of></xsl:variable>
						<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"></xsl:value-of></a>
					</td>
				</xsl:otherwise>
			</xsl:choose>
		</tr>
	</xsl:template>


	<xsl:template match="list_template_hour">

		<xsl:apply-templates select="menu"></xsl:apply-templates> 
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"></xsl:call-template>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_template_hour"></xsl:apply-templates>
			<xsl:choose>
				<xsl:when test="values_template_hour[counter]!=''">
					<xsl:apply-templates select="values_template_hour"></xsl:apply-templates>
				</xsl:when>
			</xsl:choose>						
		</table>
		<hr noshade="noshade" width="100%" align="center" size="1"></hr>
		<table align="left">
			<xsl:apply-templates select="table_add"></xsl:apply-templates>
			<xsl:apply-templates select="table_done"></xsl:apply-templates>
		</table>

	</xsl:template>

	<xsl:template match="table_header_template_hour">
		<xsl:variable name="sort_billperae"><xsl:value-of select="sort_billperae"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_building_part"><xsl:value-of select="sort_building_part"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_record"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_building_part}"><xsl:value-of select="lang_building_part"></xsl:value-of></a>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_code"></xsl:value-of>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_descr"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_unit"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_billperae}"><xsl:value-of select="lang_billperae"></xsl:value-of></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values_template_hour">
		<xsl:variable name="lang_edit_statustext"><xsl:value-of select="lang_edit_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="new_grouping=1">
				<tr>
					<td class="th_text" align="center" colspan="10" width="100%">
						<xsl:value-of select="grouping_descr"></xsl:value-of>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"></xsl:value-of>
					</xsl:when>
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>row_off</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>row_on</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<td align="right">
				<xsl:value-of select="record"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="building_part"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="code"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="hours_descr"></xsl:value-of>
				<br></br>
				<xsl:value-of select="remark"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="unit"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="billperae"></xsl:value-of>
			</td>
			<td align="center">
				<xsl:variable name="link_edit"><xsl:value-of select="link_edit"></xsl:value-of></xsl:variable>
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"></xsl:value-of></a>
			</td>
			<td align="center">
				<xsl:variable name="link_delete"><xsl:value-of select="link_delete"></xsl:value-of></xsl:variable>
				<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"></xsl:value-of></a>
			</td>
		</tr>
	</xsl:template>


<!-- add / edit  -->

	<xsl:template match="edit_template">
		<div align="left">

			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
				<form method="post" name="form" action="{$form_action}">

					<xsl:choose>
						<xsl:when test="value_template_id !=''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_template_id"></xsl:value-of>
								</td>
								<td class="th_text">
									<xsl:value-of select="value_template_id"></xsl:value-of>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_chapter"></xsl:value-of>
						</td>
						<td class="th_text">
							<xsl:call-template name="chapter_select"></xsl:call-template>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_name"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_name_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_descr"></xsl:value-of>
						</td>
						<td>
							<textarea cols="60" rows="4" name="values[descr]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_descr_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_descr"></xsl:value-of>		
							</textarea>
						</td>
					</tr>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<xsl:choose>
					<xsl:when test="value_template_id !=''">
						<tr>
							<td height="50">
								<xsl:variable name="add_action"><xsl:value-of select="add_action"></xsl:value-of></xsl:variable>
								<xsl:variable name="lang_add"><xsl:value-of select="lang_add"></xsl:value-of></xsl:variable>
								<form method="post" action="{$add_action}">
									<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_add_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</form>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<tr>
					<td>
						<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>



	<xsl:template xmlns:php="http://php.net/xsl" match="edit_hour">
		<script type="text/javascript">
			self.name="first_Window";
			function ns3420_lookup()
			{
			Window1=window.open('<xsl:value-of select="ns3420_link"></xsl:value-of>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>
		<div align="left">

			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
				<form method="post" name="form" action="{$form_action}">

					<xsl:choose>
						<xsl:when test="value_hour_id !=''">
							<xsl:choose>
								<xsl:when test="value_activity_num =''">
									<tr>
										<td>
											<xsl:value-of select="lang_copy_hour"></xsl:value-of>
										</td>
										<td>
											<input type="checkbox" name="values[copy_hour]" value="True" onMouseout="window.status='';return true;">
												<xsl:attribute name="onMouseover">
													<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_copy_hour_statustext"></xsl:value-of>
													<xsl:text>'; return true;</xsl:text>
												</xsl:attribute>
											</input>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>

							<tr>
								<td valign="top">
									<xsl:value-of select="lang_hour_id"></xsl:value-of>
								</td>
								<td class="th_text">
									<xsl:value-of select="value_hour_id"></xsl:value-of>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_activity_num !=''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_activity_num"></xsl:value-of>
								</td>
								<td class="th_text">
									<xsl:value-of select="value_activity_num"></xsl:value-of>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_template"></xsl:value-of>
						</td>
						<td class="th_text">
							<xsl:value-of select="value_template_name"></xsl:value-of>
							<xsl:text> [ </xsl:text>
							<xsl:value-of select="value_template_id"></xsl:value-of>
							<xsl:text> ]</xsl:text>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_activity_num=''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_chapter"></xsl:value-of>
								</td>
								<td class="th_text">
									<xsl:call-template name="chapter_select"></xsl:call-template>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_grouping"></xsl:value-of>
								</td>
								<td class="th_text">
									<xsl:call-template name="grouping_select"></xsl:call-template>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_new_grouping"></xsl:value-of>
								</td>
								<td>
									<input type="text" name="values[new_grouping]" value="{value_new_grouping}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_new_grouping_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>			
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'building part')"></xsl:value-of>
						</td>
						<td>
							<select name="values[building_part_id]">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'select building part')"></xsl:value-of>
								</xsl:attribute>
								<option value="0">
									<xsl:value-of select="php:function('lang', 'select building part')"></xsl:value-of>
								</option>
								<xsl:apply-templates select="building_part_list/options"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_activity_num !=''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_ns3420"></xsl:value-of>
								</td>
								<td class="th_text">
									<xsl:value-of select="value_ns3420_id"></xsl:value-of>
									<input type="hidden" name="ns3420_id" value="{value_ns3420_id}"></input>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_descr"></xsl:value-of>
								</td>
								<td>
									<textarea cols="60" rows="4" name="values[descr]" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_descr_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
										<xsl:value-of select="value_descr"></xsl:value-of>		
									</textarea>
								</td>
							</tr>
						</xsl:when>
						<xsl:otherwise>
							<tr>
								<td valign="top">
									<a href="javascript:ns3420_lookup()" onMouseover="window.status='{lang_ns3420_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_ns3420"></xsl:value-of></a>
								</td>
								<td valign="top">
									<input type="text" name="ns3420_id" value="{value_ns3420_id}" onClick="ns3420_lookup();" readonly="readonly">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_ns3420_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr>
								<td>
								</td>
								<td>
									<textarea cols="40" rows="4" name="ns3420_descr" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_descr_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
										<xsl:value-of select="value_descr"></xsl:value-of>		
									</textarea>

								</td>
							</tr>
							<tr>
								<td>
									<xsl:value-of select="lang_tolerance"></xsl:value-of>
								</td>
								<td>
									<xsl:call-template name="tolerance_select"></xsl:call-template>
								</td>
							</tr>
						</xsl:otherwise>
					</xsl:choose>

					<tr>
						<td>
							<xsl:value-of select="lang_unit"></xsl:value-of>
						</td>
						<td>
							<xsl:call-template name="unit_select"></xsl:call-template>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_dim_d"></xsl:value-of>
						</td>
						<td>
							<xsl:call-template name="dim_d_select"></xsl:call-template>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_quantity"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[quantity]" value="{value_quantity}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_quantity_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_billperae"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[billperae]" value="{value_billperae}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_billperae_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_total_cost"></xsl:value-of>
						</td>
						<td>
							<xsl:value-of select="value_total_cost"></xsl:value-of>
							<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_remark"></xsl:value-of>
						</td>
						<td>
							<textarea cols="60" rows="4" name="values[remark]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_remark_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_remark"></xsl:value-of>		
							</textarea>
						</td>
					</tr>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>

	<xsl:template match="options">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"></xsl:attribute>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of>
		</option>
	</xsl:template>
