<!-- $Id$ -->

	<xsl:template name="dummy">
		<xsl:apply-templates select="global_message"/>
	</xsl:template>

	<xsl:template match="global_message" xmlns:php="http://php.net/xsl">
		<h1><xsl:value-of select="php:function('lang', 'global message')" /></h1>
		<form method="post" action="{form_action}">
			<table>
				<tr>
					<td valign="top">
								<xsl:value-of select="php:function('lang', 'message')" />
					</td>
					<td>
						<textarea cols="40" rows="6" id='message' name="message" wrap="virtual">
							<xsl:attribute name="title">
	    		        		<xsl:value-of select="php:function('lang', 'global message')" />
							</xsl:attribute>
							<xsl:value-of select="value_message"/>
						</textarea>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'delete message')" />
					</td>
					<td>
						<input type="checkbox" name="delete_message" value="1" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'delete message')" />
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="confirm" value="{lang_submit}"/>
						<input type="submit" name="cancel" value="{lang_cancel}" />
					</td>
				</tr>
			</table>
		</form>
	</xsl:template>
