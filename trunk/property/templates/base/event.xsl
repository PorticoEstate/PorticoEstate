<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="schedule">
				<xsl:apply-templates select="schedule"></xsl:apply-templates>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

<!-- add / edit  -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<xsl:call-template name="msgbox"></xsl:call-template>
			</xsl:when>
		</xsl:choose>

		<div class="yui-navset" id="general_edit_tabview" align="left">
			<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
			<form method="post" action="{$form_action}">
				<xsl:value-of disable-output-escaping="yes" select="tabs"></xsl:value-of>
				<div class="yui-content">		
					<div id="general">
						<table cellpadding="2" cellspacing="2" width="79%" align="center">
							<xsl:choose>
								<xsl:when test="value_id != ''">
									<tr>
										<td valign="top">
											<xsl:value-of select="lang_id"></xsl:value-of>
										</td>
										<td>
											<xsl:value-of select="value_id"></xsl:value-of>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>	
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_descr"></xsl:value-of>
								</td>
								<td>
									<textarea cols="{textareacols}" rows="{textarearows}" name="values[descr]">
										<xsl:value-of select="value_descr"></xsl:value-of>		
									</textarea>
								</td>
							</tr>
							<xsl:call-template name="contact_form"></xsl:call-template>
<!--
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_responsible"/>
								</td>
								<td>
									<xsl:value-of disable-output-escaping="yes" select="responsible"/>
								</td>
							</tr>
-->
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_action"></xsl:value-of>
								</td>
								<td>
									<xsl:value-of disable-output-escaping="yes" select="action"></xsl:value-of>
								</td>
							</tr>

							<tr>
								<td valign="top">
									<xsl:value-of select="lang_enabled"></xsl:value-of>
								</td>
								<td>
									<xsl:choose>
										<xsl:when test="value_enabled = '1'">
											<input type="checkbox" name="values[enabled]" value="1" checked="checked" onMouseout="window.status='';return true;">
												<xsl:attribute name="title">
													<xsl:value-of select="lang_enabled_on_statustext"></xsl:value-of>
												</xsl:attribute>
											</input>
										</xsl:when>
										<xsl:otherwise>
											<input type="checkbox" name="values[enabled]" value="1" onMouseout="window.status='';return true;">
												<xsl:attribute name="title">
													<xsl:value-of select="lang_enabled_off_statustext"></xsl:value-of>
												</xsl:attribute>
											</input>
										</xsl:otherwise>
									</xsl:choose>
								</td>
							</tr>


						</table>
					</div>
					<div id="repeat">
						<table cellpadding="2" cellspacing="2" width="79%" align="center">
							<xsl:choose>
								<xsl:when test="value_id != ''">

									<tr>
										<td valign="top">
											<xsl:value-of select="lang_next_run"></xsl:value-of>
										</td>
										<td>
											<xsl:value-of select="value_next_run"></xsl:value-of>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_start_date"></xsl:value-of>
								</td>
								<td>
									<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_start_date_statustext"></xsl:value-of>
										</xsl:attribute>
									</input>
									<img id="values_start_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_end_date"></xsl:value-of>
								</td>
								<td>
									<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_end_date_statustext"></xsl:value-of>
										</xsl:attribute>
									</input>
									<img id="values_end_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_repeat_type"></xsl:value-of>
								</td>
								<td>
									<xsl:value-of disable-output-escaping="yes" select="repeat_type"></xsl:value-of>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_repeat_day"></xsl:value-of>
								</td>
								<td>
									<xsl:value-of disable-output-escaping="yes" select="repeat_day"></xsl:value-of>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_repeat_interval"></xsl:value-of>
								</td>
								<td>
									<input type="text" id="values_repeat_interval" name="values[repeat_interval]" size="4" value="{value_repeat_interval}">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_repeat_interval_statustext"></xsl:value-of>
										</xsl:attribute>
									</input>
								</td>
							</tr>			
<!--
						<tr>
							<td valign="top">
								<a>
									<xsl:attribute name="href"><xsl:value-of select="link_schedule"/></xsl:attribute>
									<xsl:value-of select="php:function('lang', 'plan')" />
								</a>
							</td>
						</tr>
-->

					</table>
				</div>
				<xsl:variable name="edit_url"><xsl:value-of select="edit_url"></xsl:value-of></xsl:variable>
				<div id="plan">

					<input type="hidden" name="values[location_id]" value="{value_location_id}"></input>
					<input type="hidden" name="values[location_item_id]" value="{value_location_item_id}"></input>
					<table cellpadding="2" cellspacing="2" width="79%" align="center" border="0">
						<tr>
							<td width="79%" class="center" align="left">
								<xsl:value-of select="php:function('lang', 'alarm')"></xsl:value-of>
							</td>
						</tr>

						<!-- DataTable 0 EDIT -->
						<tr>
							<td class="center" align="left" colspan="10">
								<div id="datatable-container_0"></div>
							</td>
						</tr>
						<tr>
							<td class="center" align="center" colspan="10">
								<div id="datatable-buttons_0"></div>
							</td>
						</tr>
						<!-- <xsl:call-template name="alarm_form"/>  -->

					</table>

				</div>

				<table cellpadding="2" cellspacing="2" width="80%" align="center">
					<tr height="50">
						<td valign="bottom">
							<input type="submit" name="values[save]" value="{lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_save_statustext"></xsl:value-of>
								</xsl:attribute>
							</input>
						</td>
						<td valign="bottom">
							<input type="submit" name="values[apply]" value="{lang_apply}" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_apply_statustext"></xsl:value-of>
								</xsl:attribute>
							</input>
						</td>
						<td align="right" valign="bottom">
							<input type="submit" name="values[cancel]" value="{lang_cancel}" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
								</xsl:attribute>
							</input>
						</td>
						<xsl:choose>
							<xsl:when test="value_id != ''">
								<td align="right" valign="bottom">
									<input type="submit" name="values[delete]" value="{lang_delete}">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_delete_statustext"></xsl:value-of>
										</xsl:attribute>
									</input>
								</td>
							</xsl:when>
						</xsl:choose>	
					</tr>
				</table>
			</div>
		</form>
	</div>
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
			permission		:	<xsl:value-of select="permission"></xsl:value-of>,
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

<xsl:template xmlns:php="http://php.net/xsl" match="schedule">
	<div class="yui-navset" id="edit_tabview">
		<xsl:value-of disable-output-escaping="yes" select="tabs"></xsl:value-of>
		<div class="yui-content">
			<xsl:variable name="edit_url"><xsl:value-of select="edit_url"></xsl:value-of></xsl:variable>
			<div id="general">
				<form method="post" name="alarm" action="{$edit_url}">
					<input type="hidden" name="values[location_id]" value="{value_location_id}"></input>
					<input type="hidden" name="values[location_item_id]" value="{value_location_item_id}"></input>
					<table cellpadding="2" cellspacing="2" width="79%" align="center" border="0">
						<tr>
							<td width="79%" class="center" align="left">
								<xsl:value-of select="php:function('lang', 'alarm')"></xsl:value-of>
							</td>
						</tr>

						<!-- DataTable 0 EDIT -->
						<tr>
							<td class="center" align="left" colspan="10">
								<div id="datatable-container_0"></div>
							</td>
						</tr>
						<tr>
							<td class="center" align="center" colspan="10">
								<div id="datatable-buttons_0"></div>
							</td>
						</tr>
						<!-- <xsl:call-template name="alarm_form"/>  -->

					</table>
				</form>
			</div>
		</div>
	</div>
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
			permission		:	<xsl:value-of select="permission"></xsl:value-of>,
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
