  <!-- $Id$ -->
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="list_alarm"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="list">
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
				<td align="left">
					<!--<xsl:call-template name="cat_filter"/> -->
				</td>
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<xsl:variable name="link_url">
			<xsl:value-of select="link_url"/>
		</xsl:variable>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<form name="form" method="post" action="{$link_url}">
				<xsl:apply-templates select="table_header"/>
				<xsl:apply-templates select="values"/>
				<xsl:apply-templates select="alter_alarm"/>
			</form>
			<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header">
		<xsl:variable name="sort_alarm_id" select="sort_alarm_id"/>
		<xsl:variable name="sort_next_run" select="sort_next_run"/>
		<xsl:variable name="sort_method" select="sort_method"/>
		<xsl:variable name="sort_user" select="sort_user"/>
		<tr class="th">
			<td width="10%" align="right">
				<a href="{$sort_alarm_id}" class="th_text">
					<xsl:value-of select="lang_alarm_id"/>
				</a>
			</td>
			<td width="10%">
				<a href="{$sort_next_run}" class="th_text">
					<xsl:value-of select="lang_next_run"/>
				</a>
			</td>
			<td width="10%" align="center">
				<xsl:value-of select="lang_times"/>
			</td>
			<td width="10%">
				<a href="{$sort_method}" class="th_text">
					<xsl:value-of select="lang_method"/>
				</a>
			</td>
			<td width="40%" align="center">
				<xsl:value-of select="lang_data"/>
			</td>
			<td width="5%" align="center">
				<xsl:value-of select="lang_enabled"/>
			</td>
			<td width="10%">
				<a href="{$sort_user}" class="th_text">
					<xsl:value-of select="lang_user"/>
				</a>
			</td>
			<td width="5%" align="center">
				<xsl:value-of select="lang_select"/>
			</td>
			<td width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values">
		<xsl:variable name="lang_view_statustext">
			<xsl:value-of select="lang_view_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_edit_statustext">
			<xsl:value-of select="lang_edit_statustext"/>
		</xsl:variable>
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
				<xsl:value-of select="id"/>
			</td>
			<td align="left">
				<xsl:value-of select="next_run"/>
			</td>
			<td align="center">
				<xsl:value-of select="times"/>
			</td>
			<td align="center">
				<xsl:value-of select="method"/>
			</td>
			<td align="left">
				<xsl:value-of select="data"/>
			</td>
			<td align="center">
				<xsl:value-of select="enabled"/>
			</td>
			<td align="left">
				<xsl:value-of select="user"/>
			</td>
			<td align="center">
				<input type="checkbox" name="values[alarm][{id}]" value="{id}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_select_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td align="center">
				<xsl:variable name="link_edit">
					<xsl:value-of select="link_edit"/>
				</xsl:variable>
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_edit"/>
				</a>
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
	<xsl:template match="alter_alarm">
		<tr height="20">
			<td align="right" valign="bottom" colspan="9">
				<xsl:variable name="lang_test_cron">
					<xsl:value-of select="lang_test_cron"/>
				</xsl:variable>
				<input type="submit" name="values[test_cron]" value="{$lang_test_cron}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_test_cron_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<xsl:variable name="lang_enable">
					<xsl:value-of select="lang_enable"/>
				</xsl:variable>
				<input type="submit" name="values[enable_alarm]" value="{$lang_enable}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_enable_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<xsl:variable name="lang_disable">
					<xsl:value-of select="lang_disable"/>
				</xsl:variable>
				<input type="submit" name="values[disable_alarm]" value="{$lang_disable}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_disable_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
				<xsl:variable name="lang_delete">
					<xsl:value-of select="lang_delete"/>
				</xsl:variable>
				<input type="submit" name="values[delete_alarm]" value="{$lang_delete}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_delete_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</xsl:template>

	<!-- add / edit -->
	<xsl:template match="edit">
		<xsl:variable name="edit_url">
			<xsl:value-of select="edit_url"/>
		</xsl:variable>
		<div align="left">
			<form name="form" method="post" action="{$edit_url}">
				<table cellpadding="2" cellspacing="2" width="79%" align="center">
					<xsl:choose>
						<xsl:when test="msgbox_data != ''">
							<tr>
								<td align="left" colspan="3">
									<xsl:call-template name="msgbox"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_async_id!=''">
							<tr>
								<td width="25%" align="left">
									<xsl:value-of select="lang_async_id"/>
								</td>
								<td width="75%" align="left">
									<xsl:value-of select="value_async_id"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td align="left">
							<xsl:value-of select="lang_method"/>
						</td>
						<td align="left">
							<xsl:variable name="lang_method_statustext">
								<xsl:value-of select="lang_method_statustext"/>
							</xsl:variable>
							<select name="values[method_id]" class="forms" onMouseover="window.status='{$lang_method_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value="">
									<xsl:value-of select="lang_no_method"/>
								</option>
								<xsl:apply-templates select="method_list"/>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top">
							<xsl:value-of select="lang_timing"/>
						</td>
						<td align="left">
							<table width="80%" cellpadding="2" cellspacing="2" align="left">
								<tr class="th">
									<td width="20%" align="center">
										<xsl:value-of select="lang_year"/>
									</td>
									<td width="20%">
										<xsl:value-of select="lang_month"/>
									</td>
									<td width="20%" align="center">
										<xsl:value-of select="lang_day"/>
									</td>
									<td width="20%" align="center">
										<xsl:value-of select="lang_dow"/>
									</td>
									<td width="20%" align="center">
										<xsl:value-of select="lang_hour"/>
									</td>
									<td width="20%" align="center">
										<xsl:value-of select="lang_minute"/>
									</td>
								</tr>
								<tr>
									<xsl:attribute name="class">
										<xsl:text>row_on</xsl:text>
									</xsl:attribute>
									<td>
										<input type="text" size="4" name="values[year]" value="{value_year}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_id_b_accounttext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td>
										<input type="text" size="4" name="values[month]" value="{value_month}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_id_b_accounttext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td>
										<input type="text" size="4" name="values[day]" value="{value_day}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_id_b_accounttext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td>
										<input type="text" size="4" name="values[dow]" value="{value_dow}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_id_b_accounttext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td>
										<input type="text" size="4" name="values[hour]" value="{value_hour}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_id_b_accounttext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td>
										<input type="text" size="4" name="values[min]" value="{value_minute}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_id_b_accounttext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr height="50">
						<td>
						</td>
						<td>
							<table>
								<tr>
									<td valign="bottom">
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
									<td valign="bottom">
										<xsl:variable name="lang_apply">
											<xsl:value-of select="lang_apply"/>
										</xsl:variable>
										<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_apply_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td align="right" valign="bottom">
										<xsl:variable name="lang_cancel">
											<xsl:value-of select="lang_cancel"/>
										</xsl:variable>
										<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_cancel_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</xsl:template>

	<!-- view -->
	<xsl:template match="view">
		<table cellpadding="2" cellspacing="2" width="79%" align="center">
			<tr class="row_off">
				<td width="19%">
					<xsl:value-of select="lang_time_created"/>
				</td>
				<td width="81%">
					<xsl:value-of select="value_date"/>
				</td>
			</tr>
			<tr class="row_on">
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:value-of select="value_cat"/>
				</td>
			</tr>
			<tr class="row_off">
				<td valign="top">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<xsl:value-of select="value_name"/>
				</td>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="done_action">
						<xsl:value-of select="done_action"/>
					</xsl:variable>
					<xsl:variable name="lang_done">
						<xsl:value-of select="lang_done"/>
					</xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseover="window.status='Back to the list.';return true;" onMouseout="window.status='';return true;"/>
					</form>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="list_alarm">
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
				<td align="left">
					<!--<xsl:call-template name="cat_filter"/> -->
				</td>
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_alarm"/>
			<xsl:apply-templates select="values_alarm"/>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header_alarm">
		<xsl:variable name="sort_alarm_id" select="sort_alarm_id"/>
		<xsl:variable name="sort_next_run" select="sort_next_run"/>
		<xsl:variable name="sort_method" select="sort_method"/>
		<xsl:variable name="sort_user" select="sort_user"/>
		<tr class="th">
			<td width="10%" align="right">
				<a href="{$sort_alarm_id}" class="th_text">
					<xsl:value-of select="lang_alarm_id"/>
				</a>
			</td>
			<td width="10%">
				<a href="{$sort_next_run}" class="th_text">
					<xsl:value-of select="lang_next_run"/>
				</a>
			</td>
			<!--<td width="10%" align="center">
<xsl:value-of select="lang_times"/>
</td>
-->
			<td width="40%" align="center">
				<xsl:value-of select="lang_data"/>
			</td>
			<td width="5%" align="center">
				<xsl:value-of select="lang_enabled"/>
			</td>
			<td width="10%">
				<a href="{$sort_user}" class="th_text">
					<xsl:value-of select="lang_user"/>
				</a>
			</td>
			<td width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values_alarm">
		<xsl:variable name="lang_view_statustext">
			<xsl:value-of select="lang_view_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_edit_statustext">
			<xsl:value-of select="lang_edit_statustext"/>
		</xsl:variable>
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
				<xsl:value-of select="id"/>
			</td>
			<td align="left">
				<xsl:value-of select="next_run"/>
			</td>
			<!--<td align="center">
<xsl:value-of select="times"/>
</td>
-->
			<td align="left">
				<xsl:value-of select="data"/>
			</td>
			<td align="center">
				<xsl:value-of select="enabled"/>
			</td>
			<td align="left">
				<xsl:value-of select="user"/>
			</td>
			<td align="center">
				<xsl:variable name="link_edit">
					<xsl:value-of select="link_edit"/>
				</xsl:variable>
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_edit"/>
				</a>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="method_list">
		<xsl:variable name="id">
			<xsl:value-of select="id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
