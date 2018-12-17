
<!-- $Id$ -->

<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="login">
			<xsl:apply-templates select="login"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- add -->
<xsl:template xmlns:php="http://php.net/xsl" match="login">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>

			<div id="message" class='message'/>

			<div id="login">
				<fieldset>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'username')"/>
						</label>
						<input type="text" id="external_username" name="external_username" value="{value_external_username}" required = '1' class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'username')"/>
							</xsl:attribute>
						</input>
					</div>
	
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'password')"/>
						</label>

						<input type="password" id="external_password" name="external_password" value="" required = '1' class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'password')"/>
							</xsl:attribute>
						</input>
					</div>
					<xsl:if test="value_token != ''">
						<label>
							<xsl:value-of select="php:function('lang', 'token')"/>
						</label>
						<xsl:value-of disable-output-escaping="yes" select="value_token"/>
					</xsl:if>
				</fieldset>
			</div>
			<div id="general">
			</div>

		</div>
		<div class="proplist-col">
			<xsl:variable name="lang_send">
				<xsl:value-of select="php:function('lang', 'send')"/>
			</xsl:variable>
			<xsl:variable name="lang_cancel">
				<xsl:value-of select="php:function('lang', 'cancel')"/>
			</xsl:variable>
			<input class="pure-button pure-button-primary" type="submit" name="save" value="{$lang_send}">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'Save the entry and return to list')"/>
				</xsl:attribute>
			</input>
			<input class="pure-button pure-button-primary" type="button" name="cancel" value="{$lang_cancel}">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'Back to the ticket list')"/>
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>




<!-- New template-->
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

