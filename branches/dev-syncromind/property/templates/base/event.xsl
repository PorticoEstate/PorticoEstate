  <!-- $Id$ -->
	<xsl:template match="data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="schedule">
				<xsl:apply-templates select="schedule"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<!-- add / edit  -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<xsl:call-template name="msgbox"/>
			</xsl:when>
		</xsl:choose>
		<div id="event_edit_tabview">
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>
			<form method="post" action="{$form_action}" class= "pure-form pure-form-aligned">
				<input type="hidden" name="active_tab" value="{active_tab}"/>
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>
					<div id="general">
						<fieldset>
							<xsl:choose>
								<xsl:when test="value_id != ''">
									<div class="pure-control-group">
										<label><xsl:value-of select="lang_id"/></label>
										<div class="pure-custom"><xsl:value-of select="value_id"/></div>
									</div>
								</xsl:when>
							</xsl:choose>
							<xsl:call-template name="contact_form"/>
							<div class="pure-control-group">
								<label><xsl:value-of select="lang_descr"/></label>
								<textarea cols="{textareacols}" rows="{textarearows}" name="values[descr]">
									<xsl:value-of select="value_descr"/>
								</textarea>
							</div>
							<div class="pure-control-group">
								<label><xsl:value-of select="lang_action"/></label>
								<xsl:value-of disable-output-escaping="yes" select="action"/>
							</div>	
							<div class="pure-control-group">
								<label><xsl:value-of select="lang_enabled"/></label>
								<xsl:choose>
									<xsl:when test="value_enabled = '1'">
										<input type="checkbox" name="values[enabled]" value="1" checked="checked" onMouseout="window.status='';return true;">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_enabled_on_statustext"/>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[enabled]" value="1" onMouseout="window.status='';return true;">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_enabled_off_statustext"/>
											</xsl:attribute>
										</input>
									</xsl:otherwise>
								</xsl:choose>
							</div>
						</fieldset>
					</div>
					<div id="repeat">
						<fieldset>
							<xsl:choose>
								<xsl:when test="value_id != ''">
									<div class="pure-control-group">
										<label><xsl:value-of select="lang_next_run"/></label>
										<div class="pure-custom"><xsl:value-of select="value_next_run"/></div>
									</div>
								</xsl:when>
							</xsl:choose>
							<div class="pure-control-group">
								<label><xsl:value-of select="lang_start_date"/></label>
								<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_start_date_statustext"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label><xsl:value-of select="lang_end_date"/></label>
								<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_end_date_statustext"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label><xsl:value-of select="lang_repeat_type"/></label>
								<xsl:value-of disable-output-escaping="yes" select="repeat_type"/>
							</div>
							<div class="pure-control-group">
								<label><xsl:value-of select="lang_repeat_day"/></label>
								<div class="pure-custom"><xsl:value-of disable-output-escaping="yes" select="repeat_day"/></div>
							</div>
							<div class="pure-control-group">
								<label><xsl:value-of select="lang_repeat_interval"/></label>
								<input type="text" id="values_repeat_interval" name="values[repeat_interval]" size="4" value="{value_repeat_interval}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_repeat_interval_statustext"/>
									</xsl:attribute>
								</input>
							</div>
						</fieldset>
					</div>
					<xsl:variable name="edit_url">
						<xsl:value-of select="edit_url"/>
					</xsl:variable>
					<xsl:if test="datatable_def">
						<div id="plan">
							<fieldset>
								<input type="hidden" name="values[location_id]" value="{value_location_id}"/>
								<input type="hidden" name="values[location_item_id]" value="{value_location_item_id}"/>
								<div class="pure-control-group">		
									<label for="name">
										<xsl:value-of select="php:function('lang', 'alarm')"/>
									</label>
									<div class="pure-custom">
										<xsl:for-each select="datatable_def">
												<xsl:if test="container = 'datatable-container_0'">
													<xsl:call-template name="table_setup">
														<xsl:with-param name="container" select ='container'/>
														<xsl:with-param name="requestUrl" select ='requestUrl' />
														<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
														<xsl:with-param name="tabletools" select ='tabletools' />
														<xsl:with-param name="data" select ='data' />
														<xsl:with-param name="config" select ='config' />
													</xsl:call-template>
												</xsl:if>
										</xsl:for-each>
									</div>
								</div>
							</fieldset>
						</div>
					</xsl:if>
				</div>
				<div class="proplist-col">
					<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_statustext"/>
						</xsl:attribute>
					</input>
					<input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_apply_statustext"/>
						</xsl:attribute>
					</input>
					<input type="button" class="pure-button pure-button-primary" name="values[cancel]" value="{lang_cancel}" onClick="parent.TINY.box.hide();">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_cancel_statustext"/>
						</xsl:attribute>
					</input> 
					<xsl:choose>
						<xsl:when test="value_id != ''">
							<input type="submit" class="pure-button pure-button-primary" name="values[delete]" value="{lang_delete}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_delete_statustext"/>
								</xsl:attribute>
							</input>
						</xsl:when>
					</xsl:choose>
				</div>
			</form>
		</div>
	</xsl:template>

	<!-- New template-->
	<xsl:template xmlns:php="http://php.net/xsl" match="schedule">
		<div class="yui-navset" id="edit_tabview">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div class="yui-content">
				<xsl:variable name="edit_url">
					<xsl:value-of select="edit_url"/>
				</xsl:variable>
				<div id="general">
					<form method="post" name="alarm" action="{$edit_url}">
						<input type="hidden" name="values[location_id]" value="{value_location_id}"/>
						<input type="hidden" name="values[location_item_id]" value="{value_location_item_id}"/>
						<table cellpadding="2" cellspacing="2" width="79%" align="center" border="0">
							<tr>
								<td width="79%" class="center" align="left">
									<xsl:value-of select="php:function('lang', 'alarm')"/>
								</td>
							</tr>
							<!-- DataTable 0 EDIT -->
							<tr>
								<td class="center" align="left" colspan="10">
									<div id="datatable-container_0"/>
								</td>
							</tr>
							<tr>
								<td class="center" align="center" colspan="10">
									<div id="datatable-buttons_0"/>
								</td>
							</tr>
							<!-- <xsl:call-template name="alarm_form"/>  -->
						</table>
					</form>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"/>;
			var base_java_url = <xsl:value-of select="base_java_url"/>;
			var datatable = new Array();
			var myColumnDefs = new Array();
			var myButtons = new Array();
			var td_count = <xsl:value-of select="td_count"/>;

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"/>] = [
					{
						values:<xsl:value-of select="values"/>,
						total_records: <xsl:value-of select="total_records"/>,
						is_paginator:  <xsl:value-of select="is_paginator"/>,
						permission:<xsl:value-of select="permission"/>,
						footer:<xsl:value-of select="footer"/>
					}
				]
			</xsl:for-each>
			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
			<xsl:for-each select="myButtons">
				myButtons[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
		</script>
	</xsl:template>
