<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="add">
				<xsl:apply-templates select="add"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="add2">
				<xsl:apply-templates select="add2"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="view2">
				<xsl:apply-templates select="view2"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="list2">
				<xsl:apply-templates select="list2"></xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"></xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list">
		<xsl:variable name="autorefresh"><xsl:value-of select="autorefresh"></xsl:value-of></xsl:variable>
		<META HTTP-EQUIV="Refresh" CONTENT="{$autorefresh}"></META>
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
				<xsl:choose>
					<xsl:when test="group_filters != ''">
						<xsl:variable name="select_action"><xsl:value-of select="select_action"></xsl:value-of></xsl:variable>
						<form method="post" name="search" action="{$select_action}">
							<td>
								<xsl:call-template name="categories"></xsl:call-template>
							</td>
							<td>
								<xsl:call-template name="select_district"></xsl:call-template>
							</td>
							<td align="center">
								<xsl:call-template name="filter_select"></xsl:call-template>
							</td>
							<td align="center">
								<xsl:call-template name="user_id_select"></xsl:call-template>
							</td>
							<td align="right">
								<xsl:call-template name="search_field_grouped"></xsl:call-template>
							</td>
						</form>
					</xsl:when>
					<xsl:otherwise>
						<td>
							<xsl:call-template name="categories"></xsl:call-template>
						</td>
						<td>
							<xsl:call-template name="filter_district"></xsl:call-template>
						</td>
						<td align="center">
							<xsl:call-template name="filter_filter"></xsl:call-template>
						</td>
						<td align="center">
							<xsl:call-template name="user_id_filter"></xsl:call-template>
						</td>
						<td align="right">
							<xsl:call-template name="search_field"></xsl:call-template>
						</td>
					</xsl:otherwise>
				</xsl:choose>
				<td class="small_text" valign="top" align="left">
					<xsl:variable name="link_download"><xsl:value-of select="link_download"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_download_help"><xsl:value-of select="lang_download_help"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_download"><xsl:value-of select="lang_download"></xsl:value-of></xsl:variable>
					<a href="javascript:var w=window.open('{$link_download}','','left=50,top=100')" onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')" onMouseOut="nd()">
						<xsl:value-of select="lang_download"></xsl:value-of></a>
				</td>
			</tr>
			<tr>
				<td colspan="8" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header"></xsl:apply-templates>
			<xsl:apply-templates select="values"></xsl:apply-templates>
			<xsl:apply-templates select="table_add"></xsl:apply-templates>
		</table>
	</xsl:template>

	<xsl:template match="table_header">
		<xsl:variable name="lang_priority_statustext"><xsl:value-of select="lang_priority_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_id_statustext"><xsl:value-of select="lang_id_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_opened_by_statustext"><xsl:value-of select="lang_opened_by_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_assigned_to_statustext"><xsl:value-of select="lang_assigned_to_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_finnish_statustext"><xsl:value-of select="lang_finnish_statustext"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="1%" align="right">
				<xsl:variable name="sort_priority"><xsl:value-of select="sort_priority"></xsl:value-of></xsl:variable>
				<a href="{$sort_priority}" onMouseover="window.status='{$lang_priority_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_priority"></xsl:value-of></a>
			</td>
			<td class="th_text" width="6%" align="right">
				<xsl:variable name="sort_id"><xsl:value-of select="sort_id"></xsl:value-of></xsl:variable>
				<a href="{$sort_id}" onMouseover="window.status='{$lang_id_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_id"></xsl:value-of></a>
			</td>
			<td class="th_text" width="10%">
				<xsl:value-of select="lang_subject"></xsl:value-of>
			</td>
			<td class="th_text" width="15%" align="left">
				<xsl:value-of select="lang_location_code"></xsl:value-of>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_address"></xsl:value-of>
			</td>
			<td class="th_text" width="8%" align="center">
				<xsl:variable name="sort_opened_by"><xsl:value-of select="sort_opened_by"></xsl:value-of></xsl:variable>
				<a href="{$sort_opened_by}" onMouseover="window.status='{$lang_opened_by_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_opened_by"></xsl:value-of></a>
			</td>
			<td class="th_text" width="8%" align="center">
				<xsl:variable name="sort_assigned_to"><xsl:value-of select="sort_assigned_to"></xsl:value-of></xsl:variable>
				<a href="{$sort_assigned_to}" onMouseover="window.status='{$lang_assigned_to_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_assigned_to"></xsl:value-of></a>
			</td>
			<td class="th_text" width="8%" align="center">
				<xsl:variable name="sort_date"><xsl:value-of select="sort_date"></xsl:value-of></xsl:variable>
				<a href="{$sort_date}" onMouseover="window.status='{$lang_opened_by_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_time_created"></xsl:value-of></a>
			</td>
			<xsl:for-each select="extra">
				<td class="th_text" width="{with}" align="{align}">
					<xsl:value-of select="header"></xsl:value-of>					
				</td>
			</xsl:for-each>
			<td class="th_text" width="8%" align="center">
				<xsl:variable name="sort_finnish_date"><xsl:value-of select="sort_finnish_date"></xsl:value-of></xsl:variable>
				<a href="{$sort_finnish_date}" onMouseover="window.status='{$lang_finnish_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_finnish_date"></xsl:value-of></a>
			</td>
			<td class="th_text" width="15%" align="center">
				<xsl:value-of select="lang_delay"></xsl:value-of>
			</td>
			<td class="th_text" width="15%" align="center">
				<xsl:value-of select="lang_status"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"></xsl:value-of></xsl:variable>		
		<xsl:variable name="link_view"><xsl:value-of select="link_view"></xsl:value-of></xsl:variable>
		<tr bgcolor="{bgcolor}">
			<td class="small_text" align="right">
				<xsl:value-of select="priostr"></xsl:value-of>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="new_ticket"></xsl:value-of>
				<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"></xsl:value-of></a>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="subject"></xsl:value-of>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="location_code"></xsl:value-of>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="address"></xsl:value-of>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="user"></xsl:value-of>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="assignedto"></xsl:value-of>
			</td>
			<td class="small_text" align="center">
				<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="date"></xsl:value-of></a>
			</td>

			<xsl:for-each select="child_date">
				<td class="small_text">
					<xsl:for-each select="date_info">
						<a href="{link}"><xsl:value-of select="entry_date"></xsl:value-of></a>
						<br></br>
					</xsl:for-each>
				</td>
			</xsl:for-each>

			<td class="small_text" align="center">
				<xsl:value-of select="finnish_date"></xsl:value-of>
			</td>
			<td class="small_text" align="center">
				<xsl:value-of select="delay"></xsl:value-of>
			</td>
			<td class="small_text" valign="top" align="center">
				<xsl:choose>
					<xsl:when test="//allow_edit_status != ''">
						<xsl:variable name="link_edit_status"><xsl:value-of select="link_edit_status"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_edit_status"><xsl:value-of select="lang_edit_status"></xsl:value-of></xsl:variable>
						<xsl:variable name="text_edit_status"><xsl:value-of select="text_edit_status"></xsl:value-of></xsl:variable>
						<xsl:variable name="status"><xsl:value-of select="status"></xsl:value-of></xsl:variable>
						<a href="{$link_edit_status}" onMouseOver="overlib('{$text_edit_status}', CAPTION, '{$lang_edit_status}')" onMouseOut="nd()">
							<xsl:value-of select="status"></xsl:value-of></a>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="status"></xsl:value-of>
					</xsl:otherwise>
				</xsl:choose>
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

	<xsl:template match="list2">
		<xsl:variable name="autorefresh"><xsl:value-of select="autorefresh"></xsl:value-of></xsl:variable>
		<META HTTP-EQUIV="Refresh" CONTENT="{$autorefresh}"></META>		
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
					<xsl:call-template name="filter_filter"></xsl:call-template>
				</td>
				<td colspan="10" align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
				<td class="small_text" valign="top" align="left">
					<xsl:variable name="link_download"><xsl:value-of select="link_download"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_download_help"><xsl:value-of select="lang_download_help"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_download"><xsl:value-of select="lang_download"></xsl:value-of></xsl:variable>
					<a href="javascript:var w=window.open('{$link_download}','','left=50,top=100')" onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')" onMouseOut="nd()">
						<xsl:value-of select="lang_download"></xsl:value-of></a>
				</td>
			</tr>
			<tr>
				<td colspan="12" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header2"></xsl:apply-templates>
			<xsl:apply-templates select="values2"></xsl:apply-templates>
			<xsl:apply-templates select="table_add"></xsl:apply-templates>
		</table>
	</xsl:template>

	<xsl:template match="table_header2">
		<xsl:variable name="lang_priority_statustext"><xsl:value-of select="lang_priority_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_id_statustext"><xsl:value-of select="lang_id_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_opened_by_statustext"><xsl:value-of select="lang_opened_by_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_assigned_to_statustext"><xsl:value-of select="lang_assigned_to_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_finnish_statustext"><xsl:value-of select="lang_finnish_statustext"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="1%" align="right">
				<xsl:variable name="sort_priority"><xsl:value-of select="sort_priority"></xsl:value-of></xsl:variable>
				<a href="{$sort_priority}" onMouseover="window.status='{$lang_priority_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_priority"></xsl:value-of></a>
			</td>
			<td class="th_text" width="6%" align="right">
				<xsl:variable name="sort_id"><xsl:value-of select="sort_id"></xsl:value-of></xsl:variable>
				<a href="{$sort_id}" onMouseover="window.status='{$lang_id_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_id"></xsl:value-of></a>
			</td>
			<td class="th_text" width="10%">
				<xsl:value-of select="lang_subject"></xsl:value-of>
			</td>
			<td class="th_text" width="15%" align="left">
				<xsl:value-of select="lang_location_code"></xsl:value-of>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_address"></xsl:value-of>
			</td>
			<td class="th_text" width="8%" align="center">
				<xsl:variable name="sort_opened_by"><xsl:value-of select="sort_opened_by"></xsl:value-of></xsl:variable>
				<a href="{$sort_opened_by}" onMouseover="window.status='{$lang_opened_by_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_opened_by"></xsl:value-of></a>
			</td>
			<td class="th_text" width="8%" align="center">
				<xsl:variable name="sort_assigned_to"><xsl:value-of select="sort_assigned_to"></xsl:value-of></xsl:variable>
				<a href="{$sort_assigned_to}" onMouseover="window.status='{$lang_assigned_to_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_assigned_to"></xsl:value-of></a>
			</td>
			<td class="th_text" width="8%" align="center">
				<xsl:variable name="sort_date"><xsl:value-of select="sort_date"></xsl:value-of></xsl:variable>
				<a href="{$sort_date}" onMouseover="window.status='{$lang_opened_by_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_time_created"></xsl:value-of></a>
			</td>
			<xsl:for-each select="extra">
				<td class="th_text" width="{with}" align="{align}">
					<xsl:value-of select="header"></xsl:value-of>					
				</td>
			</xsl:for-each>
			<td class="th_text" width="8%" align="center">
				<xsl:variable name="sort_finnish_date"><xsl:value-of select="sort_finnish_date"></xsl:value-of></xsl:variable>
				<a href="{$sort_finnish_date}" onMouseover="window.status='{$lang_finnish_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_finnish_date"></xsl:value-of></a>
			</td>
			<td class="th_text" width="15%" align="center">
				<xsl:value-of select="lang_delay"></xsl:value-of>
			</td>
			<td class="th_text" width="15%" align="center">
				<xsl:value-of select="lang_status"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values2">
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"></xsl:value-of></xsl:variable>		
		<xsl:variable name="link_view"><xsl:value-of select="link_view"></xsl:value-of></xsl:variable>
		<tr bgcolor="{bgcolor}">
			<td class="small_text" align="right">
				<xsl:value-of select="priostr"></xsl:value-of>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="new_ticket"></xsl:value-of>
				<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"></xsl:value-of></a>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="subject"></xsl:value-of>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="location_code"></xsl:value-of>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="address"></xsl:value-of>
			</td>
			<td class="small_text" align="center">
				<xsl:value-of select="user"></xsl:value-of>
			</td>
			<td class="small_text" align="center">
				<xsl:value-of select="assignedto"></xsl:value-of>
			</td>
			<td class="small_text" align="center">
				<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="date"></xsl:value-of></a>
			</td>
			<xsl:for-each select="child_date">
				<td class="small_text">
					<xsl:for-each select="date_info">
						<xsl:variable name="link"><xsl:value-of select="link"></xsl:value-of></xsl:variable>
						<a href="{$link}" onMouseover="window.status='';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="entry_date"></xsl:value-of></a>
						<br></br>
					</xsl:for-each>
				</td>
			</xsl:for-each>
			<td class="small_text" align="center">
				<xsl:value-of select="finnish_date"></xsl:value-of>
			</td>
			<td class="small_text" align="center">
				<xsl:value-of select="delay"></xsl:value-of>
			</td>
			<td class="small_text" align="center">
				<xsl:value-of select="status"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>


<!-- add -->

	<xsl:template xmlns:php="http://php.net/xsl" match="add">
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
		<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$form_action}">
			<div class="yui-navset" id="ticket_tabview">
				<div class="yui-content">

					<table cellpadding="2" cellspacing="2" width="80%" align="center">

						<xsl:for-each select="value_origin">
							<tr>
								<td valign="top">
									<xsl:value-of select="descr"></xsl:value-of>
								</td>
								<td>
									<table>							
										<xsl:for-each select="data">
											<tr>
												<td align="left">
													<xsl:variable name="link_request"><xsl:value-of select="//link_request"></xsl:value-of>&amp;id=<xsl:value-of select="id"></xsl:value-of></xsl:variable>
													<a href="{link}" title="{//lang_origin_statustext}"><xsl:value-of select="id"></xsl:value-of></a>
													<xsl:text> </xsl:text>
												</td>
											</tr>
										</xsl:for-each>
									</table>
								</td>
							</tr>
						</xsl:for-each>
						<input type="hidden" name="values[origin]" value="{value_origin_type}"></input>
						<input type="hidden" name="values[origin_id]" value="{value_origin_id}"></input>

						<xsl:call-template name="location_form"></xsl:call-template>

						<tr>
							<td>
								<xsl:value-of select="lang_category"></xsl:value-of>
							</td>
							<td>
								<xsl:call-template name="categories"></xsl:call-template>
							</td>
						</tr>

						<xsl:choose>
							<xsl:when test="simple !='1'">
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'Group')"></xsl:value-of>							
									</td>
									<td>
										<xsl:call-template name="group_select"></xsl:call-template>
									</td>
								</tr>
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'Assign to')"></xsl:value-of>							
									</td>
									<td>
										<xsl:call-template name="user_id_select"></xsl:call-template>
									</td>
								</tr>
								<xsl:call-template name="contact_form"></xsl:call-template>	
								<tr>
									<td>
										<xsl:value-of select="php:function('lang', 'Send e-mail')"></xsl:value-of>							
									</td>
									<td>
										<input type="checkbox" name="values[send_mail]" value="1">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'Choose to send mailnotification')"></xsl:value-of>
											</xsl:attribute>
											<xsl:if test="pref_send_mail = '1'">
												<xsl:attribute name="checked">
													<xsl:text>checked</xsl:text>
												</xsl:attribute>
											</xsl:if>
										</input>
									</td>
								</tr>
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'Priority')"></xsl:value-of>							
									</td>
									<td>
										<xsl:variable name="lang_priority_statustext"><xsl:value-of select="lang_priority_statustext"></xsl:value-of></xsl:variable>
										<xsl:variable name="select_priority_name"><xsl:value-of select="select_priority_name"></xsl:value-of></xsl:variable>
										<select name="{$select_priority_name}" onMouseover="window.status='{$lang_priority_statustext}'; return true;" onMouseout="window.status='';return true;">
											<xsl:apply-templates select="priority_list/options"></xsl:apply-templates>
										</select>			
									</td>
								</tr>
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'status')"></xsl:value-of>
									</td>
									<td>
										<select name="values[status]">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'Set the status of the ticket')"></xsl:value-of>
											</xsl:attribute>
											<xsl:apply-templates select="status_list/options"></xsl:apply-templates>
										</select>			
									</td>
								</tr>
								<xsl:choose>
									<xsl:when test="show_finnish_date ='1'">
										<tr>
											<td>
												<xsl:value-of select="php:function('lang', 'finnish date')"></xsl:value-of>							
											</td>
											<td>
												<input type="text" id="values_finnish_date" name="values[finnish_date]" size="10" value="{value_finnish_date}" readonly="readonly" onMouseout="window.status='';return true;">
													<xsl:attribute name="title">
														<xsl:value-of select="lang_finnish_date_statustext"></xsl:value-of>
													</xsl:attribute>
												</input>
												<img id="values_finnish_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
											</td>
										</tr>
									</xsl:when>
								</xsl:choose>
							</xsl:when>
						</xsl:choose>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'subject')"></xsl:value-of>
							</td>
							<td>
								<input type="text" name="values[subject]" value="{value_subject}" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Enter the subject of this ticket')"></xsl:value-of>
									</xsl:attribute>
								</input>

							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'Details')"></xsl:value-of>
							</td>
							<td>
								<textarea cols="60" rows="10" name="values[details]" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Enter the details of this ticket')"></xsl:value-of>
									</xsl:attribute>
									<xsl:value-of select="value_details"></xsl:value-of>		
								</textarea>
							</td>
						</tr>

						<xsl:choose>
							<xsl:when test="fileupload = 1">
								<tr>
									<td valign="top">
										<xsl:value-of select="lang_upload_file"></xsl:value-of>
									</td>
									<td>
										<input type="file" name="file" size="40" onMouseout="window.status='';return true;">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_file_statustext"></xsl:value-of>
											</xsl:attribute>
										</input>
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>
					</table>
				</div>
				<table cellpadding="2" cellspacing="2" width="50%" align="center">
					<tr height="50">
						<td>
							<input type="hidden" id="save" name="values[save]" value=""></input>
							<input type="hidden" id="apply" name="values[apply]" value=""></input>
							<input type="hidden" id="cancel" name="values[cancel]" value=""></input>
							<input type="button" name="save" value="{lang_send}" onClick="confirm_session('save');">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_send_statustext"></xsl:value-of>
								</xsl:attribute>
							</input>
						</td>
						<td>
							<input type="button" name="apply" value="{lang_save}" onClick="confirm_session('apply');">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_send_statustext"></xsl:value-of>
								</xsl:attribute>
							</input>
						</td>
						<td>
							<input type="button" name="cancel" value="{lang_cancel}" onClick="confirm_session('cancel');">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_send_statustext"></xsl:value-of>
								</xsl:attribute>
							</input>
						</td>
<!--
					<input type="submit" name="values[save]" value="{lang_send}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_send_statustext"/>
						</xsl:attribute>
					</input>
				</td>
				<td>
					<input type="submit" name="values[apply]" value="{lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_statustext"/>
						</xsl:attribute>
					</input>
				</td>
				<td>
					<input type="submit" name="values[cancel]" value="{lang_cancel}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_cancel_statustext"/>
						</xsl:attribute>
					</input>
				</td>
-->
			</tr>
		</table>
	</div>
</form>
	</xsl:template>

<!-- add2 -->

	<xsl:template match="add2">
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
				<form name="form" method="post" action="{$form_action}">

					<xsl:call-template name="location_view"></xsl:call-template>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_contact_phone"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[extra][contact_phone]" value="{value_contact_phone}" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_contact_phone_statustext"></xsl:value-of>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_contact_email"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[extra][contact_email]" value="{value_contact_email}" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_contact_email_statustext"></xsl:value-of>
								</xsl:attribute>
							</input>
						</td>
					</tr>
<!--
			<tr>
				<td>
					<xsl:value-of select="lang_finnish_date"/>
				</td>
				<td>
					<input type="text" name="values[finnish_date]" value="{value_finnish_date}" onFocus="{dateformat_validate}" onKeyUp="{onKeyUp}" onBlur="{onBlur}" size="12" maxlength="10"  onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="statustext"/>
							<xsl:text>';return true;</xsl:text>
						</xsl:attribute>
					</input>
					[<xsl:value-of select="lang_dateformat"/>]
				</td>
			</tr>
-->

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_subject"></xsl:value-of>
				</td>
				<td>
					<input type="text" name="values[subject]" value="{value_subject}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_subject_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_details"></xsl:value-of>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[details]" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_details_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_details"></xsl:value-of>		
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

<!-- view -->
	<xsl:template xmlns:php="http://php.net/xsl" match="view">
		<script type="text/javascript">
			self.name="first_Window";
			function generate_order()
			{
				Window1=window.open('<xsl:value-of select="order_link"></xsl:value-of>','','left=50,top=100');
			}		

			function generate_request()
			{
				Window1=window.open('<xsl:value-of select="request_link"></xsl:value-of>','','left=50,top=100');
			}		

			function template_lookup()
			{
				var oArgs = {menuaction:'property.uilookup.order_template',type:'order_template'};
				var strURL = phpGWLink('index.php', oArgs);
				Window1=window.open(strURL,"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}

			function response_lookup()
			{
				var oArgs = {menuaction:'property.uilookup.response_template',type:'response_template'};
				var strURL = phpGWLink('index.php', oArgs);
				Window1=window.open(strURL,"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}
		</script>
		<table cellpadding="2" cellspacing="2" width="95%" align="center">
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
		<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
		<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$form_action}">
			<div class="yui-navset" id="ticket_tabview">
				<xsl:value-of disable-output-escaping="yes" select="tabs"></xsl:value-of>
				<div class="yui-content">
					<div id="general">
						<table cellpadding="2" cellspacing="2" width="95%" align="center">
							<tr class="th">
								<td class="th_text" valign="top">
									<xsl:value-of select="php:function('lang', 'Ticket')"></xsl:value-of>
								</td>
								<td class="th_text" valign="top">
									<xsl:value-of select="value_id"></xsl:value-of>
									<input type="text" name="values[subject]" value="{value_subject}">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'update subject')"></xsl:value-of>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<xsl:for-each select="value_origin">
								<tr>
									<td valign="top">
										<xsl:value-of select="descr"></xsl:value-of>
									</td>
									<td>
										<table>							
											<xsl:for-each select="data">
												<tr>
													<td class="th_text" align="left">
														<a href="{link}" title="{statustext}"><xsl:value-of select="id"></xsl:value-of></a>
														<xsl:text> </xsl:text>
													</td>
												</tr>
											</xsl:for-each>
										</table>
									</td>
								</tr>
							</xsl:for-each>
							<xsl:choose>
								<xsl:when test="lookup_type ='view'">
									<xsl:call-template name="location_view"></xsl:call-template>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="location_form"></xsl:call-template>
								</xsl:otherwise>
							</xsl:choose>

							<xsl:choose>
								<xsl:when test="contact_phone !=''">
									<tr>
										<td class="th_text" align="left">
											<xsl:value-of select="php:function('lang', 'Contact phone')"></xsl:value-of>
										</td>
										<td align="left">
											<xsl:value-of select="contact_phone"></xsl:value-of>					
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>

<!--
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'Open Date')" />
				</td>
				<td valign="top">
					<xsl:value-of select="value_opendate"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'Assigned from')" />
				</td>
				<td valign="top">
					<xsl:value-of select="value_assignedfrom"/>
				</td>
			</tr>

			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'Assigned to')" />
				</td>
				<td valign="top">
					<xsl:value-of select="value_assignedto_name"/>
				</td>
			</tr>
-->
			<xsl:call-template name="contact_form"></xsl:call-template>

			<xsl:for-each select="value_target">
				<tr>
					<td class="th_text" valign="top">
						<xsl:value-of select="descr"></xsl:value-of>
					</td>
					<td class="th_text" align="left">
						<xsl:for-each select="data">
							<a href="{link}" title="{statustext}"><xsl:value-of select="id"></xsl:value-of></a>
							<xsl:text> </xsl:text>
						</xsl:for-each>
					</td>
				</tr>
			</xsl:for-each>

<!--
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'details')" />
				</td>
				<td valign="top">
					<xsl:value-of select="value_details"/>
				</td>
			</tr>

-->
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'details')"></xsl:value-of>
				</td>
				<xsl:choose>
					<xsl:when test="additional_notes=''">
						<td class="th_text">
							<xsl:value-of select="php:function('lang', 'no additional notes')"></xsl:value-of>
						</td>
					</xsl:when>
					<xsl:otherwise>
						<td>
							<table width="100%" cellpadding="2" cellspacing="2" align="center">
								<!--  DATATABLE 0-->
								<!--  <xsl:apply-templates select="table_header_additional_notes"/><xsl:apply-templates select="additional_notes"/>-->
								<td><div id="paging_0"></div><div id="datatable-container_0"></div> </td>
							</table>
						</td>
					</xsl:otherwise>
				</xsl:choose>
			</tr>
			<xsl:choose>
				<xsl:when test="simple !='1'">
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'group')"></xsl:value-of>
						</td>
						<td>
							<xsl:call-template name="group_select"></xsl:call-template>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'assigned to')"></xsl:value-of>
						</td>
						<td>
							<xsl:call-template name="user_id_select"></xsl:call-template>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="lang_takeover != ''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_takeover"></xsl:value-of>
								</td>
								<td>
									<input type="checkbox" name="values[takeover]" value="1">
										<xsl:attribute name="title">				
											<xsl:value-of select="php:function('lang', 'Take over the assignment for this ticket')"></xsl:value-of>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'Send e-mail')"></xsl:value-of>
						</td>
						<td>
							<input type="checkbox" name="values[send_mail]" value="1">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Choose to send mailnotification')"></xsl:value-of>
								</xsl:attribute>
								<xsl:if test="pref_send_mail = '1'">
									<xsl:attribute name="checked">
										<xsl:text>checked</xsl:text>
									</xsl:attribute>
								</xsl:if>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'Priority')"></xsl:value-of>
						</td>
						<td>
							<xsl:variable name="lang_priority_statustext"><xsl:value-of select="php:function('lang', 'Select the priority the selection belongs to')"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_priority_name"><xsl:value-of select="select_priority_name"></xsl:value-of></xsl:variable>
							<select name="{$select_priority_name}" class="forms" title="{$lang_priority_statustext}" onMouseover="window.status='{$lang_priority_statustext}'; return true;" onMouseout="window.status='';return true;">
								<xsl:apply-templates select="priority_list/options"></xsl:apply-templates>
							</select>			
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_order_id=''">
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'status')"></xsl:value-of>
								</td>
								<td>
									<select name="values[status]" class="forms">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'Set the status of the ticket')"></xsl:value-of>
										</xsl:attribute>
										<xsl:apply-templates select="status_list/options"></xsl:apply-templates>
									</select>			
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'category')"></xsl:value-of>
						</td>
						<td>
							<xsl:call-template name="categories"></xsl:call-template>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="show_finnish_date ='1'">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'finnish date')"></xsl:value-of>
								</td>
								<td>
									<input type="text" id="values_finnish_date" name="values[finnish_date]" size="10" value="{value_finnish_date}" readonly="readonly" onMouseout="window.status='';return true;">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'select the estimated date for closing the task')"></xsl:value-of>
										</xsl:attribute>
									</input>
									<img id="values_finnish_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="show_billable_hours ='1'">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'billable hours')"></xsl:value-of>
								</td>
								<td>
									<input type="text" id="values_billable_hour" name="values[billable_hours]" size="10" value="{value_billable_hours}">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'enter the billable hour for the task')"></xsl:value-of>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

				</xsl:when>
				<xsl:otherwise>
					<input type="hidden" name="values[status]" value="{value_status}"></input>
					<input type="hidden" name="values[assignedto]" value="{value_assignedto_id}"></input>
					<input type="hidden" name="values[group_id]" value="{value_group_id}"></input>
					<input type="hidden" name="values[priority]" value="{value_priority}"></input>
					<input type="hidden" name="values[cat_id]" value="{value_cat_id}"></input>
					<input type="hidden" name="values[finnish_date]" value="{value_finnish_date}"></input>
					<input type="hidden" name="values[billable_hour]" value="{value_billable_hours}"></input>
				</xsl:otherwise>
			</xsl:choose>

			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'new note')"></xsl:value-of>
				</td>
				<td>
					<textarea cols="{textareacols}" rows="{textarearows}" name="values[note]">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'add new comments')"></xsl:value-of>
						</xsl:attribute>
					</textarea>

				</td>
			</tr>

			<xsl:choose>
				<xsl:when test="fileupload = 1">
					<tr>
						<td width="19%" align="left" valign="top">
							<xsl:value-of select="php:function('lang', 'files')"></xsl:value-of>
						</td>
						<td>
							<div id="datatable-container_2"></div>
						</td>
					</tr>	
				</xsl:when>
			</xsl:choose>

			<xsl:choose>
				<xsl:when test="fileupload = 1">
					<script type="text/javascript">
						var fileuploader_action = <xsl:value-of select="fileuploader_action"></xsl:value-of>;
					</script>
					<xsl:call-template name="file_upload"></xsl:call-template>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="send_response = 1">
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="php:function('lang', 'notify client by sms')"></xsl:value-of>
						</td>
						<td align="left">
							<table>
								<tr>
									<td>
										<input type="checkbox" name="notify_client_by_sms" value="true">
											<xsl:attribute name="title">
												<xsl:value-of select="value_sms_client_order_notice"></xsl:value-of>
											</xsl:attribute>
										</input>
									</td>
									<td>
										<input type="text" name="to_sms_phone" value="{value_sms_phone}">
											<xsl:attribute name="title">
												<xsl:value-of select="value_sms_client_order_notice"></xsl:value-of>
											</xsl:attribute>
										</input>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<a href="javascript:response_lookup()">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'response')"></xsl:value-of>
									</xsl:attribute>
								<xsl:value-of select="php:function('lang', 'response')"></xsl:value-of>
							</a>
						</td>
						<td>
							<textarea cols="{textareacols}" rows="{textarearows}" id="response_text" name="values[response_text]" onKeyUp="javascript: SmsCountKeyUp(160);" onKeyDown="javascript: SmsCountKeyDown(160);" wrap="virtual">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'response')"></xsl:value-of>
								</xsl:attribute>
								<xsl:value-of select="value_order_descr"></xsl:value-of>
							</textarea>
						</td>
					</tr>

					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'character left')"></xsl:value-of>
						</td>
						<td>
							<input type="text" readonly="readonly" size="3" maxlength="3" name="charNumberLeftOutput" id="charNumberLeftOutput" value="160">
							</input>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="access_order = 1">
					<xsl:choose>
						<xsl:when test="value_order_id=''">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'make order')"></xsl:value-of>
								</td>
								<td>
									<input type="checkbox" name="values[make_order]" value="True">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'make order')"></xsl:value-of>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="value_order_id!=''">
							<tr class="th">
								<td class="th_text">
									<xsl:value-of select="php:function('lang', 'order id')"></xsl:value-of>
								</td>
								<td>
									<xsl:value-of select="value_order_id"></xsl:value-of>
									<input type="hidden" name="values[order_id]" value="{value_order_id}"></input>
								</td>
							</tr>
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'on behalf of assigned')"></xsl:value-of>
								</td>
								<td>
									<input type="checkbox" name="values[on_behalf_of_assigned]" value="True">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'on behalf of assigned - vacation mode')"></xsl:value-of>
										</xsl:attribute>
									</input>
								</td>
							</tr>

							<xsl:call-template name="vendor_form"></xsl:call-template>
							<xsl:call-template name="ecodimb_form"></xsl:call-template>
							<xsl:call-template name="b_account_form"></xsl:call-template>

							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'building part')"></xsl:value-of>
								</td>
								<td>
									<select name="values[building_part]">
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
								<xsl:when test="branch_list!=''">
									<tr>
										<td>
											<xsl:value-of select="php:function('lang', 'branch')"></xsl:value-of>
										</td>
										<td>
											<select name="values[branch_id]">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'select branch')"></xsl:value-of>
												</xsl:attribute>
												<option value="0">
													<xsl:value-of select="php:function('lang', 'select branch')"></xsl:value-of>
												</option>
												<xsl:apply-templates select="branch_list/options"></xsl:apply-templates>
											</select>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'order_dim1')"></xsl:value-of>
								</td>
								<td>
									<select name="values[order_dim1]">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'order_dim1')"></xsl:value-of>
										</xsl:attribute>
										<option value="0">
											<xsl:value-of select="php:function('lang', 'order_dim1')"></xsl:value-of>
										</option>
										<xsl:apply-templates select="order_dim1_list/options"></xsl:apply-templates>
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'cost estimate')"></xsl:value-of>
								</td>
								<td>
									<input type="text" name="values[budget]" value="{value_budget}">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'Enter the budget')"></xsl:value-of>
										</xsl:attribute>
									</input>
									<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'actual cost')"></xsl:value-of>
								</td>
								<td>
									<input type="text" name="values[actual_cost]" value="{value_actual_cost}">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'Enter actual cost')"></xsl:value-of>
										</xsl:attribute>
									</input>
									<!--<xsl:value-of select="value_actual_cost"/> -->
									<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
								</td>
							</tr>
							<tr>
								<td valign="top">
									<a href="javascript:template_lookup()">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'lookup template')"></xsl:value-of>
										</xsl:attribute>
										<xsl:value-of select="php:function('lang', 'description')"></xsl:value-of>
									</a>

								</td>
								<td>
									<textarea cols="{textareacols}" rows="{textarearows}" id="order_descr" name="values[order_descr]" wrap="virtual">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'description order')"></xsl:value-of>
										</xsl:attribute>
										<xsl:value-of select="value_order_descr"></xsl:value-of>
									</textarea>
								</td>
							</tr>
							<xsl:choose>
								<xsl:when test="need_approval='1'">
									<tr>
										<td valign="top">
											<xsl:value-of select="php:function('lang', 'ask for approval')"></xsl:value-of>
										</td>
										<td>
											<table>
												<xsl:for-each select="value_approval_mail_address">
													<tr>
														<td>
															<input type="checkbox" name="values[approval][{id}]" value="{address}">
																<xsl:attribute name="title">
																	<xsl:value-of select="php:function('lang', 'ask for approval')"></xsl:value-of>
																</xsl:attribute>
															</input>
														</td>
														<td valign="top">
															<xsl:value-of select="address"></xsl:value-of>
														</td>
													</tr>
												</xsl:for-each>
											</table>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'send order')"></xsl:value-of>
								</td>
								<td><div id="paging_3"></div><div id="datatable-container_3"></div> </td>
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'extra mail address')"></xsl:value-of>
									</td>
									<td>
										<input type="text" name="values[vendor_email][]" value="">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'The order will also be sent to this one')"></xsl:value-of>
											</xsl:attribute>
										</input>
									</td>
								</tr>

								<!--	<td>
								<table>
									<xsl:apply-templates select="vendor_email"/>
								</table>
							</td>
						-->
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'status')"></xsl:value-of>
							</td>
							<td>
								<select name="values[status]" class="forms">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Set the status of the ticket')"></xsl:value-of>
									</xsl:attribute>
									<xsl:apply-templates select="status_list/options"></xsl:apply-templates>
								</select>			
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="value_order_id!=''">
						<tr class="th">
							<td class="th_text">
								<xsl:value-of select="php:function('lang', 'order id')"></xsl:value-of>
							</td>
							<td>
								<xsl:value-of select="value_order_id"></xsl:value-of>
							</td>
						</tr>
						<xsl:call-template name="vendor_view"></xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</table>
</div>
<div id="history">
	<div id="paging_1"></div>
	<div id="datatable-container_1"></div>	
</div>
</div>
	<table cellpadding="2" cellspacing="2" width="80%" align="center">
		<tr height="50">
			<td>
				<input type="hidden" id="save" name="values[save]" value=""></input>
				<input type="button" name="save" onClick="confirm_session('save');">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'save')"></xsl:value-of>
					</xsl:attribute>
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'save the ticket')"></xsl:value-of>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</table>
</div>
			</form>
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<tr>
					<td>
						<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_done"><xsl:value-of select="php:function('lang', 'done')"></xsl:value-of></xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Back to the ticket list')"></xsl:value-of>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
			<hr noshade="noshade" width="100%" align="center" size="1"></hr>
			<table width="80%" cellpadding="2" cellspacing="2" align="center">
				<tr>
					<xsl:choose>
						<xsl:when test="request_link != ''">
							<td valign="top">
								<xsl:variable name="request_link"><xsl:value-of select="request_link"></xsl:value-of></xsl:variable>
								<form method="post" action="{$request_link}">
									<xsl:variable name="lang_generate_request"><xsl:value-of select="php:function('lang', 'Generate Request')"></xsl:value-of></xsl:variable>
									<input type="submit" name="location" value="{$lang_generate_request}" onMouseout="window.status='';return true;">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'click this to generate a request with this information')"></xsl:value-of>
										</xsl:attribute>
									</input>
								</form>
							</td>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="order_link != ''">

							<td valign="top">
								<xsl:variable name="order_link"><xsl:value-of select="order_link"></xsl:value-of></xsl:variable>
								<form method="post" action="{$order_link}">
									<xsl:variable name="lang_generate_project"><xsl:value-of select="php:function('lang', 'generate new project')"></xsl:value-of></xsl:variable>
									<input type="submit" name="location" value="{$lang_generate_project}" onMouseout="window.status='';return true;">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'click this to generate a project with this information')"></xsl:value-of>
										</xsl:attribute>
									</input>
								</form>
							</td>
							<td valign="top">
								<xsl:variable name="add_to_project_link"><xsl:value-of select="add_to_project_link"></xsl:value-of></xsl:variable>
								<form method="post" action="{$add_to_project_link}">
									<xsl:variable name="lang_add_to_project"><xsl:value-of select="php:function('lang', 'add to project')"></xsl:value-of></xsl:variable>
									<input type="submit" name="location" value="{$lang_add_to_project}" onMouseout="window.status='';return true;">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'click this to add an order to an existing project')"></xsl:value-of>
										</xsl:attribute>
									</input>
								</form>
							</td>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="link_entity!=''">
							<xsl:for-each select="link_entity">
								<td valign="top">
									<xsl:variable name="link"><xsl:value-of select="link"></xsl:value-of></xsl:variable>
									<form method="post" action="{$link}">
										<xsl:variable name="name"><xsl:value-of select="name"></xsl:value-of></xsl:variable>
										<input type="submit" name="location" value="{$name}" onMouseout="window.status='';return true;">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_start_statustext"></xsl:value-of>
											</xsl:attribute>
										</input>
									</form>
								</td>
							</xsl:for-each>
						</xsl:when>
					</xsl:choose>	
				</tr>
			</table>

			<hr noshade="noshade" width="100%" align="center" size="1"></hr>

		<div id="lightbox-placeholder" style="background-color:#000000;color:#FFFFFF;display:none">
			<div class="hd" style="background-color:#000000;color:#000000; border:0; text-align:center"> <xsl:value-of select="php:function('lang', 'fileuploader')"></xsl:value-of> </div>
			<div class="bd" style="text-align:center;"> </div>

		</div>

			<!--  DATATABLE DEFINITIONS-->
			<script type="text/javascript">
				var property_js = <xsl:value-of select="property_js"></xsl:value-of>;
				var base_java_url = <xsl:value-of select="base_java_url"></xsl:value-of>;
				var datatable = new Array();
				var myColumnDefs = new Array();
				var myButtons = new Array();
				var td_count = <xsl:value-of select="td_count"></xsl:value-of>;

				<xsl:for-each select="datatable">
					datatable[<xsl:value-of select="name"></xsl:value-of>] = [
					{
					values			:	<xsl:value-of select="values"></xsl:value-of>,
					total_records	: 	<xsl:value-of select="total_records"></xsl:value-of>,
					is_paginator	:  	<xsl:value-of select="is_paginator"></xsl:value-of>,
					<!--		permission		:	<xsl:value-of select="permission"/>, -->
					footer			:	<xsl:value-of select="footer"></xsl:value-of>
					}
					]
				</xsl:for-each>
				<xsl:for-each select="myColumnDefs">
					myColumnDefs[<xsl:value-of select="name"></xsl:value-of>] = <xsl:value-of select="values"></xsl:value-of>
				</xsl:for-each>
				<xsl:for-each select="myButtons">
					myButtons[<xsl:value-of select="name"></xsl:value-of>] = <xsl:value-of select="values"></xsl:value-of>
				</xsl:for-each>
			</script>

		</xsl:template>

<!-- view2 -->
	<xsl:template match="view2">
		<script type="text/javascript">
			self.name="first_Window";
			function generate_order()
			{
			Window1=window.open('<xsl:value-of select="order_link"></xsl:value-of>');
			}		
			function generate_request()
			{
			Window1=window.open('<xsl:value-of select="request_link"></xsl:value-of>');
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
				<form name="form" method="post" action="{$form_action}">
					<tr class="th">
						<td class="th_text" valign="top">
							<xsl:value-of select="lang_ticket"></xsl:value-of>
						</td>
						<td class="th_text" valign="top">
							<xsl:value-of select="value_id"></xsl:value-of>
							<input type="text" name="values[subject]" value="{value_subject}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_subject_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<xsl:call-template name="location_view"></xsl:call-template>
					<xsl:choose>
						<xsl:when test="contact_phone !=''">
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_contact_phone"></xsl:value-of>
								</td>
								<td align="left">
									<xsl:value-of select="contact_phone"></xsl:value-of>					
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<tr>
						<td class="th_text" valign="top">
							<xsl:value-of select="lang_opendate"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:value-of select="value_opendate"></xsl:value-of>
						</td>
					</tr>
					<tr>
						<td class="th_text" valign="top">
							<xsl:value-of select="lang_assignedfrom"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:value-of select="value_assignedfrom"></xsl:value-of>
						</td>
					</tr>
					<tr>
						<td class="th_text" valign="top">
							<xsl:value-of select="lang_assignedto"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:value-of select="value_assignedto_name"></xsl:value-of>
						</td>
					</tr>
					<tr>
						<td class="th_text" valign="top">
							<xsl:value-of select="lang_priority"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:value-of select="value_priority"></xsl:value-of>
						</td>
					</tr>
					<tr>
						<td class="th_text" valign="top">
							<xsl:value-of select="lang_category"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:value-of select="value_category_name"></xsl:value-of>
						</td>
					</tr>

					<xsl:for-each select="value_origin">
						<tr>
							<td class="th_text" valign="top">
								<xsl:value-of select="descr"></xsl:value-of>
							</td>
							<td class="th_text" align="left">
								<xsl:for-each select="data">
									<a href="{link}" title="{statustext}"><xsl:value-of select="id"></xsl:value-of></a>
									<xsl:text> </xsl:text>
								</xsl:for-each>
							</td>
						</tr>
					</xsl:for-each>



					<xsl:for-each select="entity_origin_list">
						<tr>
							<td class="th_text">
								<xsl:value-of select="name"></xsl:value-of>
							</td>
							<td class="th_text">
								<xsl:for-each select="link_info">
									<xsl:variable name="link_entity_origin"><xsl:value-of select="link"></xsl:value-of>&amp;id=<xsl:value-of select="id"></xsl:value-of></xsl:variable>
									<xsl:variable name="lang_entity_statustext"><xsl:value-of select="entry_date"></xsl:value-of></xsl:variable>
									<a href="{$link_entity_origin}" title="{statustext}" onMouseout="window.status='';return true;"><xsl:value-of select="id"></xsl:value-of></a>
									<xsl:text> </xsl:text>
								</xsl:for-each>
							</td>
						</tr>				
					</xsl:for-each>

					<tr>
						<td class="th_text" valign="top">
							<xsl:value-of select="lang_details"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:value-of select="value_details"></xsl:value-of>
						</td>
					</tr>
					<tr>
						<td class="th_text" valign="top">
							<xsl:value-of select="lang_additional_notes"></xsl:value-of>
						</td>
						<xsl:choose>
							<xsl:when test="additional_notes=''">
								<td class="th_text">
									<xsl:value-of select="lang_no_additional_notes"></xsl:value-of>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td>
									<table width="100%" cellpadding="2" cellspacing="2" align="center">
										<xsl:apply-templates select="table_header_additional_notes"></xsl:apply-templates>
										<xsl:apply-templates select="additional_notes"></xsl:apply-templates>
									</table>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tr>
<!--
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_priority"/>
				</td>
				<td>
				<xsl:variable name="lang_priority_statustext"><xsl:value-of select="lang_priority_statustext"/></xsl:variable>
				<xsl:variable name="select_priority_name"><xsl:value-of select="select_priority_name"/></xsl:variable>
					<select name="{$select_priority_name}" class="forms" onMouseover="window.status='{$lang_priority_statustext}'; return true;" onMouseout="window.status='';return true;">
							<xsl:apply-templates select="priority_list"/>
					</select>			
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_status"/>
				</td>
				<td>
				<xsl:variable name="lang_status_statustext"><xsl:value-of select="lang_status_statustext"/></xsl:variable>
					<select name="values[status]" class="forms" onMouseover="window.status='{$lang_status_statustext}'; return true;" onMouseout="window.status='';return true;">
							<xsl:apply-templates select="status_list/options"/>
					</select>			
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:call-template name="categories"/>
				</td>
			</tr>

			<tr>
				<td>
					<xsl:value-of select="lang_finnish_date"/>
				</td>
				<td>
					<input type="text" name="values[finnish_date]" value="{value_finnish_date}" onFocus="{dateformat_validate}" onKeyUp="{onKeyUp}" onBlur="{onBlur}" size="12" maxlength="10"  onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="statustext"/>
							<xsl:text>';return true;</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="disabled">
							<xsl:text>="disabled"</xsl:text>
						</xsl:attribute>
					</input>
					[<xsl:value-of select="lang_dateformat"/>]
				</td>
			</tr>
-->
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_new_note"></xsl:value-of>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[note]" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_details_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
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
	<hr noshade="noshade" width="100%" align="center" size="1"></hr>
	<table width="80%" cellpadding="2" cellspacing="2" align="center">
		<xsl:choose>
			<xsl:when test="record_history=''">
				<tr>
					<td class="th_text" align="center">
						<xsl:value-of select="lang_no_history"></xsl:value-of>
					</td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="table_header_history"></xsl:apply-templates>
				<xsl:apply-templates select="record_history"></xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</table>
</div>
<hr noshade="noshade" width="100%" align="center" size="1"></hr>
	</xsl:template>

	<xsl:template match="table_header_additional_notes">
		<tr class="th">
			<td class="th_text" width="4%" align="right">
				<xsl:value-of select="lang_count"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_date"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_user"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_note"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="additional_notes">
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
				<xsl:value-of select="value_count"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_date"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_user"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_note"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="table_header_history">
		<tr class="th">
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_date"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_user"></xsl:value-of>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_action"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_new_value"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="record_history">
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
			<td align="left">
				<xsl:value-of select="value_date"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_user"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_action"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_new_value"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="options">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"></xsl:attribute>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of>
		</option>
	</xsl:template>

	<xsl:template xmlns:php="http://php.net/xsl" match="vendor_email">
		<tr>
			<td>
				<input type="checkbox" name="values[vendor_email][]" value="{email}">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'The address to which this order will be sendt')"></xsl:value-of>
					</xsl:attribute>
				</input>
			</td>
			<td>
				<xsl:value-of select="email"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>
