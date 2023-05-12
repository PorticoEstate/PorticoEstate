<!-- $Id$ -->

<xsl:template name="dummy">
	<xsl:apply-templates select="global_message"/>
</xsl:template>

<xsl:template match="global_message" xmlns:php="http://php.net/xsl">
	<div class = "container">
		<form method="post" action="{form_action}" class="pure-form pure-form-stacked">
			<fieldset>
				<legend>
					<xsl:value-of select="php:function('lang', 'global message')" />
				</legend>
				<label>
					<xsl:value-of select="php:function('lang', 'message')" />
				</label>
				<textarea rows="6" id='message' name="message" wrap="virtual" class="pure-input-1">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'global message')" />
					</xsl:attribute>
					<xsl:value-of select="value_message"/>
				</textarea>
				<label for="delete_message" class="pure-checkbox">
					<input type="checkbox" id="delete_message" name="delete_message" value="1" >
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'delete message')" />
						</xsl:attribute>
					</input>
					<xsl:value-of select="php:function('lang', 'delete message')" />
				</label>
				<input type="submit" name="confirm" value="{lang_submit}" class="pure-button pure-button-primary"/>
				<input type="submit" name="cancel" value="{lang_cancel}" class="pure-button pure-button-primary"/>
			</fieldset>
		</form>
	</div>
</xsl:template>
