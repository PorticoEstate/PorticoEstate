<!-- $Id$ -->

	<xsl:template match="helpdesk" xmlns:php="http://php.net/xsl">
		<table cellpadding="2" cellspacing="2" width="95%" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</table>
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
				<div class="yui-navset" id="ticket_tabview">
					<xsl:value-of disable-output-escaping="yes" select="tabs" />
					<div class="yui-content">
						<div class="toolbar-container">
							<div class="toolbar">
								<xsl:apply-templates select="datatable/actions" />
							</div>
						</div>
					</div>
				</div>

			<xsl:apply-templates select="datatable" />
	</xsl:template>

	<xsl:template match="add_ticket" xmlns:php="http://php.net/xsl">
        <h2>Ny skademelding</h2>
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

			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'subject')" />
				</td>
				<td class="th_text" valign="top">
					<input type="text" name="values[title]" value="{title}"/>
				</td>
			</tr>
            
            <tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'locationdesc')" />
				</td>
				<td class="th_text" valign="top">
					<input type="text" name="values[locationdesc]" value="locationdesc"/>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'description')" />
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[details]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:value-of select="description"/>
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
					<input type="submit" name="values[save]" value="{$lang_send}" title='{$lang_send}'/>
				</td>
			</tr>

 		</table>
 		</form>
	</xsl:template>


