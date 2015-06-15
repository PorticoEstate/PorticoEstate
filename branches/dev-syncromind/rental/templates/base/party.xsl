  <!-- $Id: party.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
	        	<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
  	</func:result>
</func:function>

<xsl:template match="data">
	<xsl:apply-templates select="edit" />
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">

	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<xsl:value-of select="validator"/>

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
					</fieldset>
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{lang_save}" onMouseout="window.status='';return true;">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'Save the record and return to the list')"/>
					</xsl:attribute>
				</input>
				<input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{lang_sync_data}" onMouseout="window.status='';return true;">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'Apply the values')"/>
					</xsl:attribute> 
				</input>
				<input type="button" class="pure-button pure-button-primary" name="values[cancel]" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="document.cancel_form.submit();">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'Leave the record untouched and return to the list')"/>
					</xsl:attribute>
				</input>
			</div>
		</form>
		<xsl:variable name="cancel_url">
			<xsl:value-of select="cancel_url"/>
		</xsl:variable>
		<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post"></form>
	</div>
</xsl:template>
