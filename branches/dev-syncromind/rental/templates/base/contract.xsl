  <!-- $Id: contract.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:apply-templates select="edit" />
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<xsl:template name="top-toolbar">
	<div class="toolbar-container">
		<div class="toolbar">
			<div class="pure-g">
				<div class="pure-u-1-3">
					<div><xsl:value-of select="lang_contract_number"/>:<xsl:value-of select="value_contract_number"/></div>
					<div><xsl:value-of select="lang_parties"/>:<xsl:value-of select="value_parties"/></div>
					<div><xsl:value-of select="lang_last_updated"/>:<xsl:value-of select="value_last_updated"/></div>
					<div><xsl:value-of select="lang_name"/>:<xsl:value-of select="value_name"/></div>
					<div><xsl:value-of select="lang_composite"/>:<xsl:value-of select="value_composite"/></div>
				</div>
				<div class="pure-u-2-3">
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
		</div>
	</div>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">

	<xsl:call-template name="top-toolbar" />
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<dl>
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<dt>
							<xsl:call-template name="msgbox"/>
						</dt>
					</xsl:when>
				</xsl:choose>
			</dl>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details">
					<fieldset>
						<input type="hidden" name="id" value="{contract_id}"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_field_of_responsibility"/>
							</label>
							<xsl:choose>
								<xsl:when test="contract_id = 0 or contract_id = ''">
									<input type="hidden" name="location_id" id="location_id" value="{location_id}"/>
								</xsl:when>
							</xsl:choose>
							<xsl:value-of select="value_field_of_responsibility"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_contract_type"/>
							</label>
							<select id="contract_type" name="contract_type">
								<xsl:apply-templates select="list_contract_type/options"/>
							</select>
						</div>	
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_executive_officer"/>
							</label>
							<select id="executive_officer" name="executive_officer">
								<xsl:apply-templates select="list_executive_officer/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_date_start"/>
							</label>
							<input type="text" id="date_start" name="date_start" size="10" value="{value_date_start}" readonly="readonly"/>
						</div>	
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_date_end"/>
							</label>
							<input type="text" id="date_end" name="date_end" size="10" value="{value_date_end}" readonly="readonly"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_due_date"/>
							</label>
							<input type="text" id="due_date" name="due_date" size="10" value="{value_due_date}" readonly="readonly"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_invoice_header"/>
							</label>
							<input type="text" name="invoice_header" value="{value_invoice_header}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_billing_term"/>
							</label>
							<select id="billing_term" name="billing_term">
								<xsl:apply-templates select="list_billing_term/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_billing_start"/>
							</label>
							<input type="text" id="billing_start_date" name="billing_start_date" size="10" value="{value_billing_start}" readonly="readonly"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_billing_end"/>
							</label>
							<input type="text" id="billing_end_date" name="billing_end_date" size="10" value="{value_billing_end}" readonly="readonly"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_reference"/>
							</label>
							<input type="text" name="reference" value="{value_reference}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_responsibility"/>
							</label>
							<xsl:choose>
								<xsl:when test="list_responsibility">
									<xsl:if test="list_responsibility != ''">
										<select id="responsibility_id" name="responsibility_id">
											<xsl:apply-templates select="list_responsibility/options"/>
										</select>
									</xsl:if>
									<xsl:if test="list_responsibility = ''">
										<input type="text" name="responsibility_id" id="responsibility_id" value="{value_responsibility_id}"/>
									</xsl:if>
								</xsl:when>
							</xsl:choose>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_service"/>
							</label>
							<input type="text" name="service_id" value="{value_service}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_account_in"/>
							</label>
							<input type="text" name="account_in" value="{value_account_in}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_account_out"/>
							</label>
							<input type="text" name="account_out" value="{value_account_out}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_project_id"/>
							</label>
							<input type="text" name="project_id" value="{value_project_id}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_security"/>
							</label>
							<select id="security_type" name="security_type">
								<xsl:apply-templates select="list_security/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_security_amount"/>
							</label>
							<xsl:value-of select="security_amount_simbol"/> <input type="text" name="security_amount" value="{value_security_amount}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_rented_area"/>
							</label>
							<input type="text" name="rented_area" value="{value_rented_area}"></input> <xsl:value-of select="rented_area_simbol"/>
						</div>
						<xsl:choose>
							<xsl:when test="is_adjustable">				
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="lang_adjustable"/>
									</label>
									<input type="checkbox" name="adjustable" id="adjustable">
										<xsl:if test="is_adjustable = 1">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input>
								</div>
							</xsl:when>
						</xsl:choose>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_adjustment_interval"/>
							</label>
							<select id="adjustment_interval" name="adjustment_interval">
								<xsl:apply-templates select="list_adjustment_interval/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_adjustment_share"/>
							</label>
							<select id="adjustment_share" name="adjustment_share">
								<xsl:apply-templates select="list_adjustment_share/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_adjustment_year"/>
							</label>
							<xsl:value-of select="value_adjustment_year"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_comment"/>
							</label>
							<textarea cols="40" rows="10" name="comment" id="comment"><xsl:value-of select="value_comment"/></textarea>
						</div>
						<xsl:choose>
							<xsl:when test="value_publish_comment">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="lang_publish_comment"/>
									</label>
									<input type="checkbox" name="publish_comment" id="publish_comment">
										<xsl:if test="value_publish_comment = 1">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input>
								</div>
							</xsl:when>
						</xsl:choose>
					</fieldset>
				</div>
				<xsl:choose>
					<xsl:when test="mode = 'edit'">
						<div id="composite">
							<fieldset>
								<script type="text/javascript">
									link_included_composites = <xsl:value-of select="link_included_composites"/>;
									link_not_included_composites = <xsl:value-of select="link_not_included_composites"/>;
								</script>							
								<div>
									<xsl:for-each select="datatable_def">
										<xsl:if test="container = 'datatable-container_1'">
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
							
								<div>
									<xsl:for-each select="datatable_def">
										<xsl:if test="container = 'datatable-container_2'">
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
							</fieldset>
						</div>
						<div id="parties">
							<fieldset>
								
							</fieldset>
						</div>
						<div id="price">
							<fieldset>
								
							</fieldset>
						</div>
						<div id="invoice">
							<fieldset>
								
							</fieldset>
						</div>
						<div id="documents">
							<fieldset>
								
							</fieldset>
						</div>
						<div id="notifications">
							<fieldset>
								
							</fieldset>
						</div>
					</xsl:when>
				</xsl:choose>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save_contract" value="{lang_save}" onMouseout="window.status='';return true;"/>
				<input type="button" class="pure-button pure-button-primary" name="contract_back" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="document.cancel_form.submit();"/>
			</div>
		</form>
		<xsl:variable name="cancel_url">
			<xsl:value-of select="cancel_url"/>
		</xsl:variable>
		<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post"></form>
	</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>