<!-- $Id$ -->

	<xsl:template match="send" xmlns:php="http://php.net/xsl">
		<form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
		<table cellpadding="0" cellspacing="0" width="100%">
 			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="2">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

 			<tr class="th">
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'address')" />
				</td>
				<td class="th_text" valign="top">
					<input type="text" name="values[address]" value="{support_address}">
						<xsl:attribute name="size">
							<xsl:text>60</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'address')" />
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'from')" />
				</td>
				<td class="th_text" valign="top">
					<xsl:value-of select="from_name"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'from adress')" />
				</td>
				<td class="th_text" valign="top">
					<input type="text" name="values[from_address]" value="{from_address}">
						<xsl:attribute name="size">
							<xsl:text>60</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'address')" />
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'description')" />
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[details]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'details')" />
						</xsl:attribute>
						<xsl:value-of select="value_details"/>		
					</textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'file')" />
				</td>
				<td>
					<input type="file" name="file" size="50">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'file')" />
						</xsl:attribute>
					</input>
				</td>
			</tr>

			<tr height="50">
				<td>
					<xsl:variable name="lang_send"><xsl:value-of select="php:function('lang', 'send')" /></xsl:variable>					
					<input type="submit" name="values[save]" value="{$lang_send}" title='{$lang_send}'>
					</input>
				</td>
			</tr>

 		</table>
 		</form>
	</xsl:template>

