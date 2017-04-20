
<!-- $Id: tts.xsl 16389 2017-02-28 17:35:22Z sigurdne $ -->

<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="view">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
	</xsl:choose>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>



<!-- edit -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="general">
				<fieldset>
					<div class="pure-control-group">
						<xsl:variable name="lang_substitute">
							<xsl:value-of select="php:function('lang', 'substitute')"/>
						</xsl:variable>
						<label>
							<xsl:value-of select="$lang_substitute"/>
						</label>
						<select name="substitute_user_id" id="substitute_user_id">
							<xsl:attribute name="title">
								<xsl:value-of select="$lang_substitute"/>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:apply-templates select="user_list/options"/>
						</select>
					</div>
				</fieldset>
			</div>
		</div>
		<xsl:variable name="lang_save">
			<xsl:value-of select="php:function('lang', 'save')"/>
		</xsl:variable>
		<input type="submit" class="pure-button pure-button-primary" name="save">
			<xsl:attribute name="value">
				<xsl:value-of select="$lang_save"/>
			</xsl:attribute>
			<xsl:attribute name="title">
				<xsl:value-of select="$lang_save"/>
			</xsl:attribute>
		</input>
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
