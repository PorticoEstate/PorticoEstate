
<xsl:template name="contract_info" xmlns:php="http://php.net/xsl">
	<xsl:param name="contract" />
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'executive_officer')"/>
		</label>
		<div id="executive_officer" class="pure-custom">
			<xsl:value-of select="contract/executive_officer"/>
		</div>
	</div>

	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'composite')"/>
		</label>
		<div id="composite" class="pure-custom">
			<xsl:value-of select="contract/composite"/>
		</div>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'rented_area')"/>
		</label>
		<div id="rented_area" class="pure-custom">
			<xsl:value-of select="contract/rented_area"/>
		</div>
	</div>

	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'security_amount')"/>
		</label>
		<div id="security_amount" class="pure-custom">
			<xsl:value-of select="contract/security_amount"/>
		</div>
	</div>

	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'date_start')"/>
		</label>
		<div id="date_start" class="pure-custom">
			<xsl:value-of select="contract/date_start"/>
		</div>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'date_end')"/>
		</label>
		<div id="date_end" class="pure-custom">
			<xsl:value-of select="contract/date_end"/>
		</div>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'type')"/>
		</label>
		<div id="type" class="pure-custom">
			<xsl:value-of select="contract/type"/>
		</div>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'party')"/>
		</label>
		<div id="party" class="pure-custom">
			<xsl:value-of select="contract/party"/>
		</div>
	</div>

	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'identifier')"/>
		</label>
		<div id="identifier" class="pure-custom">
			<xsl:value-of select="contract/identifier"/>
		</div>
	</div>

	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'mobile_phone')"/>
		</label>
		<div id="mobile_phone" class="pure-custom">
			<xsl:value-of select="contract/mobile_phone"/>
		</div>
	</div>

	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'department')"/>
		</label>
		<div id="department" class="pure-custom">
			<xsl:value-of select="contract/department"/>
		</div>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'contract_status')"/>
		</label>
		<div id="contract_status" class="pure-custom">
			<xsl:value-of select="contract/contract_status"/>
		</div>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'billing_terms')"/>
		</label>
		<div id="term_label" class="pure-custom">
			<xsl:value-of select="contract/term_label"/>
		</div>
	</div>

</xsl:template>
