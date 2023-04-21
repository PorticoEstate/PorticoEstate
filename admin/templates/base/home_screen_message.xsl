<!-- $Id: global_message.xsl 9282 2012-05-04 13:11:20Z sigurdne $ -->

<xsl:template name="dummy">
	<xsl:apply-templates select="home_screen_message"/>
</xsl:template>

<xsl:template match="home_screen_message" xmlns:php="http://php.net/xsl">
	<div class = "container">
		<form method="post" action="{form_action}" class="pure-form pure-form-stacked">
			<fieldset>
				<legend>
					<xsl:value-of select="php:function('lang', 'home screen message')" />
				</legend>
				<label>
					<xsl:value-of select="php:function('lang', 'title')" />
				</label>
				<xsl:variable name="value_title">
					<xsl:value-of select="value_title" />
				</xsl:variable>
				<input type="text" name="msg_title" id="msg_title" value="{value_title}" class="pure-input-1"/>
				<label>
					<xsl:value-of select="php:function('lang', 'message')" />
				</label>
				<textarea rows="6" id='message' name="message" wrap="virtual" class="pure-input-1">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'home screen message')" />
					</xsl:attribute>
					<xsl:value-of select="value_message" disable-output-escaping="yes" />
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
