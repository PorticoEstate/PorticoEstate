<!-- $Id: control_item.xsl 8913 2012-02-17 10:14:42Z erikhl $ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_phpgw_i18n"/>
<div class="identifier-header">
<h1><img src="{img_go_home}" /> 
		<xsl:value-of select="php:function('lang', 'edit user')" />
</h1>
</div>
	<div class="yui-content">
		<div id="details">
			<form action="#" method="post" name="form">
				<input type="hidden" name="id" value = "{value_id}">
				</input>
				<table>
					<xsl:for-each select="user_data">
						<tr>
							<td>
								<xsl:value-of select="text"/>
							</td>
							<td>
								<xsl:value-of select="value"/>
							</td>
						</tr>
					</xsl:for-each>
					<xsl:call-template name="location_form"/>			
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'approve')" />
						</td>
						<td>
							<input type="checkbox" name="values[approve]" value="1">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'approve')"/>
								</xsl:attribute>
								<xsl:if test="value_approved = '1'">
									<xsl:attribute name="checked">
										<xsl:text>checked</xsl:text>
									</xsl:attribute>
								</xsl:if>
							</input>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'process')" />
						</td>
						<td>
							<input type="checkbox" name="values[process]" value="1">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'process approved')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</table>
				<div class="form-buttons">
					<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
					<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
					<input type="submit" name="save" value="{$lang_save}" title = "{$lang_save}" />
					<input type="submit" name="cancel" value="{$lang_cancel}" title = "{$lang_cancel}" />
				</div>
			</form>			
		</div>
	</div>
</xsl:template>
	
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

