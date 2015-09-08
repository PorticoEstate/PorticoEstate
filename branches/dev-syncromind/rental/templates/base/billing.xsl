  <!-- $Id: billing.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="step1">
			<xsl:apply-templates select="step1"/>
		</xsl:when>
		<xsl:when test="step2">
			<xsl:apply-templates select="step2"/>
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
								<xsl:value-of select="php:function('lang', 'fields_of_responsibility')"/>
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
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="document.cancel_form.submit();"/>
				<input type="submit" class="pure-button pure-button-primary" name="next" value="{lang_next}" onMouseout="window.status='';return true;"/>
			</div>
		</form>
		<xsl:variable name="cancel_url">
			<xsl:value-of select="cancel_url"/>
		</xsl:variable>
		<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post"></form>
	</div>
</xsl:template>


<xsl:template xmlns:php="http://php.net/xsl" match="step2">
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

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

					<div>
						<dl>
							<dt><xsl:value-of select="php:function('lang', 'contract_type')"/></dt>
							<dd><xsl:value-of select="fields_of_responsibility_label"/></dd>
							<dt><xsl:value-of select="php:function('lang', 'billing_start')"/></dt> 
							<dd><xsl:value-of select="billing_start"/></dd>
							<dt><xsl:value-of select="php:function('lang', 'year')"/></dt>
							<dd><xsl:value-of select="year"/></dd>
							<dt><xsl:value-of select="php:function('lang', 'Export format')"/></dt>
							<dd><xsl:value-of select="export_format"/></dd>
							<xsl:if test="billing_term = 1">
								<dt><xsl:value-of select="php:function('lang', 'month')"/></dt>
								<dd><xsl:value-of select="month"/></dd>
							</xsl:if>
							<dt><xsl:value-of select="php:function('lang', 'billing_term')"/></dt>
							<dd><xsl:value-of select="billing_term_label"/></dd>																								
						</dl>							
					</div>
					<div>
						<div class="proplist-col">
							<input type="submit" class="pure-button pure-button-primary" name="previous" value="previous"/>
							<input type="submit" class="pure-button pure-button-primary" name="next" value="next"/>
						</div>							
					</div>
					<div id="user_messages">
						<h3>Meldinger</h3>
						<xsl:choose>
							<xsl:when test="//errorMsgs!=''">
								<div class="error">
									<xsl:for-each select="errorMsgs">
										<p class="message"><xsl:value-of select="current()"/></p>
									</xsl:for-each>
								</div>
							</xsl:when>
							<xsl:when test="//warningMsgs!=''">
								<div class="warning">
									<xsl:for-each select="warningMsgs">
										<p class="message"><xsl:value-of select="current()"/></p>
									</xsl:for-each>
								</div>
							</xsl:when>
							<xsl:when test="//infoMsgs!=''">
								<div class="info">
									<xsl:for-each select="infoMsgs">
										<p class="message"><xsl:value-of select="current()"/></p>
									</xsl:for-each>
								</div>
							</xsl:when>
						</xsl:choose>
					</div>
					<div id="list_navigation">
						<h3>Kontrakter i kjøring</h3>
						<ul>
							<li><a href="#non_cycle"><xsl:value-of select="php:function('lang', 'contracts_out_of_cycle')"/> (<xsl:value-of select="count(irregular_contracts)"/>)</a></li>
							<li><a href="#one_time"><xsl:value-of select="php:function('lang', 'contracts_with_one_time')"/> (<xsl:value-of select="count(contracts_with_one_time)"/>)</a></li>
							<li><a href="#cycle"><xsl:value-of select="php:function('lang', 'contracts_in_cycle')"/> (<xsl:value-of select="count(contracts)"/>)</a></li>
						</ul>
						<h3>Kontraktsinformasjon</h3>
						<ul>
							<li><a href="#new"><xsl:value-of select="php:function('lang', 'contracts_not_billed_before')"/> (<xsl:value-of select="count(not_billed_contracts)"/>)</a></li>
							<li><a href="#removed"><xsl:value-of select="php:function('lang', 'contracts_removed')"/> (<xsl:value-of select="count(removed_contracts)"/>)</a></li>
						</ul>
					</div>
					<div>
						<h2><xsl:value-of select="php:function('lang', 'contracts_out_of_cycle')"/> (<xsl:value-of select="count(irregular_contracts)"/>)</h2>
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
						<h2><xsl:value-of select="php:function('lang', 'contracts_with_one_time')"/> (<xsl:value-of select="count(contracts_with_one_time)"/>)</h2>
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
						<h2><xsl:value-of select="php:function('lang', 'contracts_in_cycle')"/> (<xsl:value-of select="count(contracts)"/>)</h2>
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
						<h2><xsl:value-of select="php:function('lang', 'contracts_not_billed_before')"/> (<xsl:value-of select="count(not_billed_contracts)"/>)</h2>
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
						<h2><xsl:value-of select="php:function('lang', 'contracts_removed')"/> (<xsl:value-of select="count(removed_contracts)"/>)</h2>
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
					<fieldset>
					</fieldset>
				</div>
			</div>
			<div class="proplist-col">
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

<xsl:template match="option_group">
	<optgroup label="{label}">
		<xsl:apply-templates select="options"/>
	</optgroup>
</xsl:template>