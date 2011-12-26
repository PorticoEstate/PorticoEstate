  <!-- $Id$ -->
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="add">
				<xsl:apply-templates select="add"/>
			</xsl:when>
			<xsl:when test="history">
				<xsl:apply-templates select="history"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="list">
		<xsl:apply-templates select="menu"/>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form method="post" name="form_search" action="{$form_action}">
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<tr>
					<td align="left">
						<xsl:call-template name="cat_select"/>
					</td>
				</tr>
				<tr>
					<td align="left">
						<xsl:call-template name="select_part_of_town"/>
						<xsl:call-template name="filter_select"/>
						<xsl:variable name="lang_search">
							<xsl:value-of select="lang_search"/>
						</xsl:variable>
						<input type="submit" name="submit_search" value="{$lang_search}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_search_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td colspan="3" width="100%">
						<xsl:call-template name="nextmatchs"/>
					</td>
				</tr>
			</table>
		</form>
		<xsl:variable name="update_action">
			<xsl:value-of select="update_action"/>
		</xsl:variable>
		<form method="post" name="form" action="{$update_action}">
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header"/>
				<xsl:choose>
					<xsl:when test="values[entity_id]!=''">
						<xsl:apply-templates select="values"/>
						<tr>
							<td/>
							<td/>
							<td/>
							<td/>
							<td/>
							<td/>
							<td class="th_text" align="right">
								<xsl:value-of select="sum_initial_value"/>
							</td>
							<td class="th_text" align="right">
								<xsl:value-of select="sum_value"/>
							</td>
							<td/>
							<td/>
							<td/>
							<td/>
							<td/>
							<td align="center">
								<xsl:variable name="img_check">
									<xsl:value-of select="img_check"/>
								</xsl:variable>
								<a href="javascript:check_all_checkbox('values[update]')">
									<img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"/>
								</a>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
			</table>
			<table width="50%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_update"/>
			</table>
		</form>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header">
		<tr class="th">
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_district"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_part_of_town"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_entity_id"/>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_investment_id"/>
			</td>
			<td class="th_text" width="10%">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="10%">
				<xsl:value-of select="lang_entity_name"/>
			</td>
			<td class="th_text" width="20%" align="center">
				<xsl:value-of select="lang_initial_value"/>
			</td>
			<td class="th_text" width="20%" align="center">
				<xsl:value-of select="lang_value"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_last_index"/>
			</td>
			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_write_off"/>
			</td>
			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_date"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_index_count"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_history"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_select"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values">
		<xsl:variable name="lang_history_statustext">
			<xsl:value-of select="lang_history_statustext"/>
		</xsl:variable>
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"/>
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
				<input type="hidden" name="values[entity_id][{counter}]" value="{entity_id}"/>
				<input type="hidden" name="values[investment_id][{counter}]" value="{investment_id}"/>
				<input type="hidden" name="values[initial_value][{counter}]" value="{initial_value_ex}"/>
				<input type="hidden" name="values[value][{counter}]" value="{value_ex}"/>
				<xsl:value-of select="district_id"/>
			</td>
			<td align="left">
				<xsl:value-of select="part_of_town"/>
			</td>
			<td align="right">
				<xsl:value-of select="entity_id"/>
			</td>
			<td align="right">
				<xsl:value-of select="investment_id"/>
			</td>
			<td align="left">
				<xsl:value-of select="descr"/>
			</td>
			<td align="left">
				<xsl:value-of select="entity_name"/>
			</td>
			<td align="right">
				<xsl:value-of select="initial_value"/>
			</td>
			<td align="right">
				<xsl:value-of select="value"/>
			</td>
			<td align="right">
				<xsl:value-of select="this_index"/>
			</td>
			<td align="right">
				<xsl:value-of select="this_write_off"/>
			</td>
			<td align="right">
				<xsl:value-of select="date"/>
			</td>
			<td align="center">
				<xsl:value-of select="index_count"/>
			</td>
			<xsl:choose>
				<xsl:when test="is_admin=1">
					<td align="center">
						<xsl:variable name="link_history">
							<xsl:value-of select="link_history"/>
						</xsl:variable>
						<a href="{$link_history}" onMouseover="window.status='{$lang_history_statustext}';return true;" onMouseout="window.status='';return true;">
							<xsl:value-of select="lang_history"/>
						</a>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="value_ex !=0">
								<input type="checkbox" name="values[update][{counter}]" value="{counter}" onMouseout="window.status='';return true;">
								</input>
							</xsl:when>
							<xsl:otherwise>
								<xsl:text/>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_update">
		<tr>
			<td>
				<xsl:value-of select="lang_new_index"/>
				<input type="text" name="values[new_index]" size="12" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_new_index_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td>
				<input type="text" id="values_date" name="values[date]" size="10" value="{value_date}" readonly="readonly" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_date_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<img id="values_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"/>
			</td>
			<td height="50">
				<xsl:variable name="lang_update">
					<xsl:value-of select="lang_update"/>
				</xsl:variable>
				<input type="submit" name="submit_update" value="{$lang_update}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_update_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_add">
		<tr>
			<td height="50">
				<xsl:variable name="add_action">
					<xsl:value-of select="add_action"/>
				</xsl:variable>
				<xsl:variable name="lang_add">
					<xsl:value-of select="lang_add"/>
				</xsl:variable>
				<form method="post" action="{$add_action}">
					<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_add_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_done">
		<tr>
			<td height="50">
				<xsl:variable name="done_action">
					<xsl:value-of select="done_action"/>
				</xsl:variable>
				<xsl:variable name="lang_done">
					<xsl:value-of select="lang_done"/>
				</xsl:variable>
				<form method="post" action="{$done_action}">
					<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_done_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<!-- History -->
	<xsl:template match="history">
		<xsl:apply-templates select="menu"/>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="entity_type!=''">
					<tr>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="lang_entity_type"/>
						</td>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="entity_type"/>
						</td>
						<td width="50%">
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="entity_id!=''">
					<tr>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="lang_entity_id"/>
						</td>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="entity_id"/>
						</td>
						<td width="50%">
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="investment_id!=''">
					<tr>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="lang_investment_id"/>
						</td>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="investment_id"/>
						</td>
						<td width="50%">
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</table>
		<xsl:variable name="update_action">
			<xsl:value-of select="update_action"/>
		</xsl:variable>
		<form method="post" name="form" action="{$update_action}">
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<input type="hidden" name="values[update][0]" value="0"/>
				<input type="hidden" name="values[entity_id][0]" value="{entity_id}"/>
				<input type="hidden" name="values[investment_id][0]" value="{investment_id}"/>
				<xsl:apply-templates select="table_header_history"/>
				<xsl:apply-templates select="values_history"/>
			</table>
			<table width="50%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_update"/>
			</table>
		</form>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_done"/>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header_history">
		<tr class="th">
			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_initial_value"/>
			</td>
			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_value"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_last_index"/>
			</td>
			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_write_off"/>
			</td>
			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_date"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_index_count"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values_history">
		<xsl:variable name="lang_delete_statustext">
			<xsl:value-of select="lang_delete_statustext"/>
		</xsl:variable>
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"/>
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
				<xsl:value-of select="initial_value"/>
			</td>
			<td align="right">
				<xsl:value-of select="value"/>
			</td>
			<td align="right">
				<xsl:value-of select="this_index"/>
			</td>
			<td align="right">
				<xsl:value-of select="this_write_off"/>
			</td>
			<td align="right">
				<xsl:value-of select="date"/>
			</td>
			<td align="center">
				<xsl:value-of select="index_count"/>
			</td>
			<xsl:choose>
				<xsl:when test="is_admin=16 and current_index=1">
					<td align="center">
						<xsl:variable name="link_delete">
							<xsl:value-of select="link_delete"/>
						</xsl:variable>
						<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;">
							<xsl:value-of select="lang_delete"/>
						</a>
						<input type="hidden" name="values[initial_value][0]" value="{initial_value_ex}"/>
						<input type="hidden" name="values[value][0]" value="{value_ex}"/>
					</td>
				</xsl:when>
				<xsl:otherwise>
					<td>
						<xsl:text/>
					</td>
				</xsl:otherwise>
			</xsl:choose>
		</tr>
	</xsl:template>

	<!-- New template-->
	<!-- add -->
	<xsl:template match="add">
		<script type="text/javascript">
			self.name="first_Window";
			function location_lookup()
			{
				Window1=window.open('<xsl:value-of select="location_link"/>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}
		</script>
		<div align="left">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="form_action">
					<xsl:value-of select="form_action"/>
				</xsl:variable>
				<form method="post" action="{$form_action}" name="form">
					<xsl:call-template name="location_form"/>
					<tr>
						<td>
							<xsl:value-of select="lang_write_off_period"/>
						</td>
						<td>
							<xsl:call-template name="cat_select"/>
							<xsl:text>  </xsl:text>
							<xsl:value-of select="lang_new"/>
							<input type="text" name="values[new_period]" value="{value_new_period}" size="3" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_new_period_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_type"/>
						</td>
						<td>
							<xsl:call-template name="filter_select"/>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_amount"/>
						</td>
						<td>
							<input type="text" name="values[initial_value]" value="{value_inital_value}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_value_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_date"/>
						</td>
						<td>
							<input type="text" id="values_date" name="values[date]" size="10" value="{value_date}" readonly="readonly" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_date_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<img id="values_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"/>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_descr"/>
						</td>
						<td>
							<input type="text" name="values[descr]" value="{value_descr}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_name_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save">
								<xsl:value-of select="lang_save"/>
							</xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<xsl:variable name="done_action">
							<xsl:value-of select="done_action"/>
						</xsl:variable>
						<xsl:variable name="lang_done">
							<xsl:value-of select="lang_done"/>
						</xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>
