
<!-- $Id: billing.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="step1">
			<xsl:apply-templates select="step1"/>
		</xsl:when>
		<xsl:when test="step2">
			<xsl:apply-templates select="step2"/>
		</xsl:when>
		<xsl:when test="simulation">
			<xsl:apply-templates select="simulation"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="step1">
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details">
					<fieldset>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
							</label>
							<xsl:text> </xsl:text>
							<xsl:value-of select="fields_of_responsibility_label"/>
							<input type="hidden" name="contract_type" id="contract_type" value="{contract_type}"/>
							<input type="hidden" name="step" value="1"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'title')"/>
							</label>
							<input type="text" name="title" id="title" value="{title}"/>
							<select id="existing_billing" name="existing_billing">
								<xsl:apply-templates select="list_existing_billing/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Year')"/>
							</label>
							<select id="year" name="year">
								<xsl:apply-templates select="list_year/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'billing_term')"/>
							</label>
							<select id="billing_term" name="billing_term">
								<xsl:apply-templates select="list_billing_term_group/option_group"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Export format')"/>
							</label>
							<input type="hidden" name="export_format" id="export_format" value="{export_format}"/>
							<xsl:value-of select="export_format"/>
						</div>						
					</fieldset>
				</div>
			</div>
			<div class="proplist-col">
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>				
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
				<input type="submit" class="pure-button pure-button-primary" name="next" value="{lang_next}" onMouseout="window.status='';return true;"/>
			</div>
		</form>
	</div>
</xsl:template>


<xsl:template xmlns:php="http://php.net/xsl" match="step2">
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<h3>
			<xsl:value-of select="php:function('lang', 'invoice_run')"/>:<xsl:text> </xsl:text>
			<xsl:value-of select="title"/>
		</h3>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details">
					<input type="hidden" name="step" value="2"/>
					<input type="hidden" name="contract_type" value="{contract_type}"/>
					<input type="hidden" name="year" value="{year}"/>
					<input type="hidden" name="month" value="{month}"/>
					<input type="hidden" name="title" value="{title}"/>
					<input type="hidden" name="use_existing" value="{use_existing}"/>
					<input type="hidden" name="existing_billing" value="{existing_billing}"/>
					<input type="hidden" name="billing_term" value="{billing_term}"/>
					<input type="hidden" name="billing_term_selection" value="{billing_term_selection}"/>
					<input type="hidden" name="export_format" value="{export_format}"/>

					<div class="pure-g">
						<div class="pure-u-1-3">
							<h3>Fakturakjøringsdetaljer</h3>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'contract_type')"/>
								</label>
								<xsl:value-of select="fields_of_responsibility_label"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'billing_start')"/>
								</label>
								<xsl:value-of select="billing_start"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'year')"/>
								</label>
								<xsl:value-of select="year"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Export format')"/>
								</label>
								<xsl:value-of select="export_format"/>
							</div>
							<xsl:if test="billing_term = 1">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'month')"/>
									</label>
									<xsl:value-of select="month_label"/>
								</div>
							</xsl:if>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'billing_term')"/>
								</label>
								<xsl:value-of select="billing_term_label"/>
							</div>
							<h3>Fakturakjøringsvalg</h3>
							<div class="pure-control-group">
								<xsl:variable name="previous">
									<xsl:value-of select="php:function('lang', 'previous')"/>
								</xsl:variable>
								<xsl:variable name="next">
									<xsl:value-of select="php:function('lang', 'simulation')"/>
								</xsl:variable>
								<div class="proplist-col">
									<input type="submit" class="pure-button pure-button-primary" name="previous" value="{$previous}"/>
									<input type="submit" class="pure-button pure-button-primary" name="next" value="{$next}"/>
								</div>
							</div>
						</div>
						<div class="pure-u-1-3">
							<h3>Kontrakter i kjøring</h3>
							<ul>
								<li>
									<a href="#non_cycle">
										<xsl:value-of select="php:function('lang', 'contracts_out_of_cycle')"/> (<xsl:value-of select="count(irregular_contracts)"/>)</a>
								</li>
								<li>
									<a href="#one_time">
										<xsl:value-of select="php:function('lang', 'contracts_with_one_time')"/> (<xsl:value-of select="count(contracts_with_one_time)"/>)</a>
								</li>
								<li>
									<a href="#cycle">
										<xsl:value-of select="php:function('lang', 'contracts_in_cycle')"/> (<xsl:value-of select="count(contracts)"/>)</a>
								</li>
							</ul>
							<h3>Kontraktsinformasjon</h3>
							<ul>
								<li>
									<a href="#new">
										<xsl:value-of select="php:function('lang', 'contracts_not_billed_before')"/> (<xsl:value-of select="count(not_billed_contracts)"/>)</a>
								</li>
								<li>
									<a href="#removed">
										<xsl:value-of select="php:function('lang', 'contracts_removed')"/> (<xsl:value-of select="count(removed_contracts)"/>)</a>
								</li>
							</ul>
						</div>
						<div class="pure-u-1-3">
							<h3>Meldinger</h3>
							<xsl:if test="//errorMsgs!=''">
								<div class="error">
									<xsl:for-each select="errorMsgs">
										<p class="message">
											<xsl:value-of select="current()"/>
										</p>
									</xsl:for-each>
								</div>
							</xsl:if>
							<xsl:if test="//warningMsgs!=''">
								<div class="warning">
									<xsl:for-each select="warningMsgs">
										<p class="message">
											<xsl:value-of select="current()"/>
										</p>
									</xsl:for-each>
								</div>
							</xsl:if>
							<xsl:if test="//infoMsgs!=''">
								<div class="info">
									<xsl:for-each select="infoMsgs">
										<p class="message">
											<xsl:value-of select="current()"/>
										</p>
									</xsl:for-each>
								</div>
							</xsl:if>
						</div>
					</div>
					<div>
						<h2>
							<xsl:value-of select="php:function('lang', 'contracts_out_of_cycle')"/> (<xsl:value-of select="count(irregular_contracts)"/>)</h2>
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
					<div>
						<h2>
							<xsl:value-of select="php:function('lang', 'contracts_with_one_time')"/> (<xsl:value-of select="count(contracts_with_one_time)"/>)</h2>
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
						<h2>
							<xsl:value-of select="php:function('lang', 'contracts_in_cycle')"/> (<xsl:value-of select="count(contracts)"/>)</h2>
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
					<div>
						<h2>
							<xsl:value-of select="php:function('lang', 'contracts_not_billed_before')"/> (<xsl:value-of select="count(not_billed_contracts)"/>)</h2>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_3'">
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
						<h2>
							<xsl:value-of select="php:function('lang', 'contracts_removed')"/> (<xsl:value-of select="count(removed_contracts)"/>)</h2>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_4'">
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
		</form>
	</div>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="simulation">
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<h3>
			<xsl:value-of select="php:function('lang', 'invoice_run')"/>:<xsl:text> </xsl:text>
			<xsl:value-of select="title"/>
		</h3>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details">
					<input type="hidden" name="step" value="simulation"/>
					<input type="hidden" name="contract_type" value="{contract_type}"/>
					<input type="hidden" name="year" value="{year}"/>
					<input type="hidden" name="month" value="{month}"/>
					<input type="hidden" name="title" value="{title}"/>
					<input type="hidden" name="use_existing" value="{use_existing}"/>
					<input type="hidden" name="existing_billing" value="{existing_billing}"/>
					<input type="hidden" name="billing_term" value="{billing_term}"/>
					<input type="hidden" name="billing_term_selection" value="{billing_term_selection}"/>
					<input type="hidden" name="export_format" value="{export_format}"/>
					<xsl:for-each select="contract_ids">
						<input type="hidden" name="contract[]">
							<xsl:attribute name="value">
								<xsl:value-of select="current()"/>
							</xsl:attribute>
						</input>
					</xsl:for-each>
					<xsl:for-each select="contract_ids_override">
						<input type="hidden" name="override_start_date[]">
							<xsl:attribute name="value">
								<xsl:value-of select="current()"/>
							</xsl:attribute>
						</input>
					</xsl:for-each>
					<xsl:for-each select="contract_bill_only_one_time">
						<input type="hidden" name="bill_only_one_time[]">
							<xsl:attribute name="value">
								<xsl:value-of select="current()"/>
							</xsl:attribute>
						</input>
					</xsl:for-each>

					<div class="pure-g">
						<div class="pure-u-1-3">
							<h3>Fakturakjøringsdetaljer</h3>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'contract_type')"/>
								</label>
								<xsl:value-of select="fields_of_responsibility_label"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'billing_start')"/>
								</label>
								<xsl:value-of select="billing_start"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'year')"/>
								</label>
								<xsl:value-of select="year"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Export format')"/>
								</label>
								<xsl:value-of select="export_format"/>
							</div>
							<xsl:if test="billing_term = 1">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'month')"/>
									</label>
									<xsl:value-of select="month_label"/>
								</div>
							</xsl:if>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'billing_term')"/>
								</label>
								<xsl:value-of select="billing_term_label"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'sum')"/>
								</label>
								<xsl:value-of select="sum"/>
							</div>
							<h3>Fakturakjøringsvalg</h3>
							<div class="pure-control-group">
								<xsl:variable name="previous">
									<xsl:value-of select="php:function('lang', 'previous')"/>
								</xsl:variable>
								<xsl:variable name="next">
									<xsl:value-of select="php:function('lang', 'bill2')"/>
								</xsl:variable>
								<div class="proplist-col">
									<input type="submit" class="pure-button pure-button-primary" name="previous" value="{$previous}"/>
									<input type="submit" class="pure-button pure-button-primary" name="next" value="{$next}"/>
								</div>
							</div>
						</div>
					</div>
					<div>
						<h2>
							<xsl:value-of select="php:function('lang', 'simulation')"/>
						</h2>
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
		</form>
	</div>
</xsl:template>

<!-- view  -->
<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'contract_type')"/>
						</label>
						<xsl:value-of select="contract_type"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'billing_terms')"/>
						</label>
						<xsl:if test="//billing_terms != ''">
							<div class="pure-custom">
								<xsl:for-each select="billing_terms">
									<div>
										<xsl:value-of select="current()"/>
									</div>
								</xsl:for-each>
							</div>
						</xsl:if>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'sum')"/>
						</label>
						<xsl:value-of select="sum"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'last_updated')"/>
						</label>
						<xsl:value-of select="last_updated"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Commited')"/>
						</label>
						<xsl:value-of select="commited"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'success')"/>
						</label>
						<xsl:value-of select="success"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Export format')"/>
						</label>
						<xsl:value-of select="export_format"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'export')"/>
						</label>
						<xsl:if test="has_generated_export = 1">
							<xsl:variable name="download_link">
								<xsl:value-of select="download_link"/>
							</xsl:variable>
							<xsl:variable name="download_link_bk">
								<xsl:value-of select="download_link_bk"/>
							</xsl:variable>
							<xsl:variable name="download_link_nlsh">
								<xsl:value-of select="download_link_nlsh"/>
							</xsl:variable>
							<div class="pure-custom">
								<div>
									<a href="{$download_link}">
										<xsl:value-of select="php:function('lang', 'Download export')"/>
									</a>
								</div>
								<div>
									<a href="{$download_link_bk}">
										<xsl:value-of select="php:function('lang', 'Download Excel export BK')"/>
									</a>
								</div>
								<div>
									<a href="{$download_link_nlsh}">
										<xsl:value-of select="php:function('lang', 'Last ned eksportfil i Excel-format NLSH')"/>
									</a>
								</div>
								<xsl:if test="is_commited = 0">
									<xsl:variable name="commit">
										<xsl:value-of select="php:function('lang', 'Commit')"/>
									</xsl:variable>
									<div>
										<input type="submit" class="pure-button pure-button-primary" name="commit" value="{$commit}"/>
									</div>
								</xsl:if>
							</div>
						</xsl:if>
						<xsl:if test="has_generated_export = 0">
							<xsl:variable name="generate_export">
								<xsl:value-of select="php:function('lang', 'Generate export')"/>
							</xsl:variable>
							<input type="submit" class="pure-button pure-button-primary" name="generate_export" value="{$generate_export}"/>
						</xsl:if>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'cs15_export')"/>
						</label>
						<xsl:variable name="download_link_cs15">
							<xsl:value-of select="download_link_cs15"/>
						</xsl:variable>
						<a href="{$download_link_cs15}">
							<xsl:value-of select="php:function('lang', 'Generate cs15')"/>
						</a>
					</div>
					<div>
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
			<div class="proplist-col">
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>				
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
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

<xsl:template match="option_group">
	<optgroup label="{label}">
		<xsl:apply-templates select="options"/>
	</optgroup>
</xsl:template>