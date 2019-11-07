<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--div id="content"-->

	<!--dl class="form">
			<dt class="heading">
				<xsl:value-of select="php:function('lang', 'Asynchronous Tasks')" />
			</dt>
	</dl-->

	<xsl:call-template name="msgbox"/>

	<form action="{form_action}" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="settings/tabs"/>
			<div id="async_settings">
				<table>
					<tr>
						<td>
							<input type='checkbox' value='1' name="helpdesk_async_task_anonyminizer_enabled" id="field_helpdesk_async_task_anonyminizer_enabled">
								<xsl:if test="settings/helpdesk_async_task_anonyminizer_enabled and settings/helpdesk_async_task_anonyminizer_enabled='1'">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input>
						</td>
						<td>
							<label for="helpdesk_async_task_anonyminizer_enabled">
								<xsl:value-of select="php:function('lang', 'helpdesk_async_task_anonyminizer_enabled')" />
							</label>
						</td>
					</tr>
				</table>
				<div class="clr"/>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" id="button" class="button pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:choose>
						<xsl:when test="new_form">
							<xsl:value-of select="php:function('lang', 'Create')"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="php:function('lang', 'Update')"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
			</input>
		</div>
	</form>
	<!--/div-->
</xsl:template>
