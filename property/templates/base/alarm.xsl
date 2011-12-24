<!-- $Id$ -->

<xsl:template name="app_data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"></xsl:apply-templates>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"></xsl:apply-templates>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="list_alarm"></xsl:apply-templates>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="list"></xsl:apply-templates>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="list">
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
		<tr>
			<td align="left">
				<!--		<xsl:call-template name="cat_filter"/> -->
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
	<xsl:variable name="link_url"><xsl:value-of select="link_url"></xsl:value-of></xsl:variable>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<form name="form" method="post" action="{$link_url}">
			<xsl:apply-templates select="table_header"></xsl:apply-templates>
			<xsl:apply-templates select="values"></xsl:apply-templates>
			<xsl:apply-templates select="alter_alarm"></xsl:apply-templates>
		</form>
		<xsl:apply-templates select="table_add"></xsl:apply-templates>
	</table>
</xsl:template>

<xsl:template match="table_header">
	<xsl:variable name="sort_alarm_id" select="sort_alarm_id"></xsl:variable>
	<xsl:variable name="sort_next_run" select="sort_next_run"></xsl:variable>		
	<xsl:variable name="sort_method" select="sort_method"></xsl:variable>		
	<xsl:variable name="sort_user" select="sort_user"></xsl:variable>		
	<tr class="th">
		<td width="10%" align="right">
			<a href="{$sort_alarm_id}" class="th_text"><xsl:value-of select="lang_alarm_id"></xsl:value-of></a>
		</td>
		<td width="10%">
			<a href="{$sort_next_run}" class="th_text"><xsl:value-of select="lang_next_run"></xsl:value-of></a>
		</td>
		<td width="10%" align="center">
			<xsl:value-of select="lang_times"></xsl:value-of>
		</td>
		<td width="10%">
			<a href="{$sort_method}" class="th_text"><xsl:value-of select="lang_method"></xsl:value-of></a>
		</td>
		<td width="40%" align="center">
			<xsl:value-of select="lang_data"></xsl:value-of>
		</td>
		<td width="5%" align="center">
			<xsl:value-of select="lang_enabled"></xsl:value-of>
		</td>
		<td width="10%">
			<a href="{$sort_user}" class="th_text"><xsl:value-of select="lang_user"></xsl:value-of></a>
		</td>
		<td width="5%" align="center">
			<xsl:value-of select="lang_select"></xsl:value-of>
		</td>
		<td width="5%" align="center">
			<xsl:value-of select="lang_edit"></xsl:value-of>
		</td>
	</tr>
</xsl:template>

<xsl:template match="values">
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
			<xsl:value-of select="id"></xsl:value-of>
		</td>
		<td align="left">
			<xsl:value-of select="next_run"></xsl:value-of>
		</td>
		<td align="center">
			<xsl:value-of select="times"></xsl:value-of>
		</td>
		<td align="center">
			<xsl:value-of select="method"></xsl:value-of>
		</td>
		<td align="left">
			<xsl:value-of select="data"></xsl:value-of>
		</td>
		<td align="center">
			<xsl:value-of select="enabled"></xsl:value-of>
		</td>
		<td align="left">
			<xsl:value-of select="user"></xsl:value-of>
		</td>
		<td align="center">
			<input type="checkbox" name="values[alarm][{id}]" value="{id}" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_select_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
		</td>

		<td align="center">
			<xsl:variable name="link_edit"><xsl:value-of select="link_edit"></xsl:value-of></xsl:variable>
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"></xsl:value-of></a>
		</td>
	</tr>
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


<xsl:template match="alter_alarm">
	<tr height="20">
		<td align="right" valign="bottom" colspan="9">
			<xsl:variable name="lang_test_cron"><xsl:value-of select="lang_test_cron"></xsl:value-of></xsl:variable>
			<input type="submit" name="values[test_cron]" value="{$lang_test_cron}" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_test_cron_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
			<xsl:variable name="lang_enable"><xsl:value-of select="lang_enable"></xsl:value-of></xsl:variable>
			<input type="submit" name="values[enable_alarm]" value="{$lang_enable}" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_enable_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
			<xsl:variable name="lang_disable"><xsl:value-of select="lang_disable"></xsl:value-of></xsl:variable>
			<input type="submit" name="values[disable_alarm]" value="{$lang_disable}" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_disable_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
			<xsl:variable name="lang_delete"><xsl:value-of select="lang_delete"></xsl:value-of></xsl:variable>
			<input type="submit" name="values[delete_alarm]" value="{$lang_delete}" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_delete_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
		</td>
	</tr>
</xsl:template>



<!-- add / edit -->

	<xsl:template match="edit">
		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"></xsl:value-of></xsl:variable>
		<div align="left">
			<form name="form" method="post" action="{$edit_url}">
				<table cellpadding="2" cellspacing="2" width="79%" align="center">
					<xsl:choose>
						<xsl:when test="msgbox_data != ''">
							<tr>
								<td align="left" colspan="3">
									<xsl:call-template name="msgbox"></xsl:call-template>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_async_id!=''">
							<tr>
								<td width="25%" align="left">
									<xsl:value-of select="lang_async_id"></xsl:value-of>
								</td>
								<td width="75%" align="left">
									<xsl:value-of select="value_async_id"></xsl:value-of>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<tr>
						<td align="left">
							<xsl:value-of select="lang_method"></xsl:value-of>
						</td>

						<td align="left">
							<xsl:variable name="lang_method_statustext"><xsl:value-of select="lang_method_statustext"></xsl:value-of></xsl:variable>
							<select name="values[method_id]" class="forms" onMouseover="window.status='{$lang_method_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_method"></xsl:value-of></option>
								<xsl:apply-templates select="method_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top">
							<xsl:value-of select="lang_timing"></xsl:value-of>
						</td>

						<td align="left">
							<table width="80%" cellpadding="2" cellspacing="2" align="left">
								<tr class="th">
									<td width="20%" align="center">
										<xsl:value-of select="lang_year"></xsl:value-of>
									</td>
									<td width="20%">
										<xsl:value-of select="lang_month"></xsl:value-of>
									</td>
									<td width="20%" align="center">
										<xsl:value-of select="lang_day"></xsl:value-of>
									</td>
									<td width="20%" align="center">
										<xsl:value-of select="lang_dow"></xsl:value-of>
									</td>
									<td width="20%" align="center">
										<xsl:value-of select="lang_hour"></xsl:value-of>
									</td>
									<td width="20%" align="center">
										<xsl:value-of select="lang_minute"></xsl:value-of>
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
												<xsl:value-of select="lang_id_b_accounttext"></xsl:value-of>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td>
										<input type="text" size="4" name="values[month]" value="{value_month}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_id_b_accounttext"></xsl:value-of>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td>
										<input type="text" size="4" name="values[day]" value="{value_day}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_id_b_accounttext"></xsl:value-of>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td>
										<input type="text" size="4" name="values[dow]" value="{value_dow}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_id_b_accounttext"></xsl:value-of>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td>
										<input type="text" size="4" name="values[hour]" value="{value_hour}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_id_b_accounttext"></xsl:value-of>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td>
										<input type="text" size="4" name="values[min]" value="{value_minute}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_id_b_accounttext"></xsl:value-of>
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
										<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
										<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_save_statustext"></xsl:value-of>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td valign="bottom">
										<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"></xsl:value-of></xsl:variable>
										<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_apply_statustext"></xsl:value-of>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</td>
									<td align="right" valign="bottom">
										<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
										<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
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
					<xsl:value-of select="lang_time_created"></xsl:value-of>
				</td>
				<td width="81%">
					<xsl:value-of select="value_date"></xsl:value-of>
				</td>
			</tr>
			<tr class="row_on">
				<td>
					<xsl:value-of select="lang_category"></xsl:value-of>
				</td>
				<td>
					<xsl:value-of select="value_cat"></xsl:value-of>
				</td>
			</tr>
			<tr class="row_off">
				<td valign="top">
					<xsl:value-of select="lang_name"></xsl:value-of>
				</td>
				<td>
					<xsl:value-of select="value_name"></xsl:value-of>
				</td>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseover="window.status='Back to the list.';return true;" onMouseout="window.status='';return true;"></input>
					</form>
				</td>
			</tr>
		</table>
	</xsl:template>


	<xsl:template match="list_alarm">
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
			<tr>
				<td align="left">
					<!--		<xsl:call-template name="cat_filter"/> -->
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
			<xsl:apply-templates select="table_header_alarm"></xsl:apply-templates>
			<xsl:apply-templates select="values_alarm"></xsl:apply-templates>
		</table>
	</xsl:template>

	<xsl:template match="table_header_alarm">
		<xsl:variable name="sort_alarm_id" select="sort_alarm_id"></xsl:variable>
		<xsl:variable name="sort_next_run" select="sort_next_run"></xsl:variable>		
		<xsl:variable name="sort_method" select="sort_method"></xsl:variable>		
		<xsl:variable name="sort_user" select="sort_user"></xsl:variable>		
		<tr class="th">
			<td width="10%" align="right">
				<a href="{$sort_alarm_id}" class="th_text"><xsl:value-of select="lang_alarm_id"></xsl:value-of></a>
			</td>
			<td width="10%">
				<a href="{$sort_next_run}" class="th_text"><xsl:value-of select="lang_next_run"></xsl:value-of></a>
			</td>
			<!--		<td width="10%" align="center">
					<xsl:value-of select="lang_times"/>
				</td>
		-->
				<td width="40%" align="center">
					<xsl:value-of select="lang_data"></xsl:value-of>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_enabled"></xsl:value-of>
				</td>
				<td width="10%">
					<a href="{$sort_user}" class="th_text"><xsl:value-of select="lang_user"></xsl:value-of></a>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_edit"></xsl:value-of>
				</td>
			</tr>
		</xsl:template>

		<xsl:template match="values_alarm">
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
					<xsl:value-of select="id"></xsl:value-of>
				</td>
				<td align="left">
					<xsl:value-of select="next_run"></xsl:value-of>
				</td>
				<!--			<td align="center">
					<xsl:value-of select="times"/>
				</td>
	-->
				<td align="left">
					<xsl:value-of select="data"></xsl:value-of>
				</td>
				<td align="center">
					<xsl:value-of select="enabled"></xsl:value-of>
				</td>
				<td align="left">
					<xsl:value-of select="user"></xsl:value-of>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"></xsl:value-of></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"></xsl:value-of></a>
				</td>
			</tr>
		</xsl:template>


		<xsl:template match="method_list">
			<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
			<xsl:choose>
				<xsl:when test="selected">
					<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
				</xsl:when>
				<xsl:otherwise>
					<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:template>
