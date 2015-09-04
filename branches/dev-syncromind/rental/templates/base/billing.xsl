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
					<fieldset>
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
						<div>
							<div id="user_messages">
								<h3>Meldinger</h3>
								<xsl:choose>
									<xsl:when test="//warningMsgs!=''">
										<xsl:for-each select="warningMsgs">
											<dl>

											</dl>
										</xsl:for-each>			
									</xsl:when>
								</xsl:choose>								
							</div>
						</div>
					</fieldset>
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