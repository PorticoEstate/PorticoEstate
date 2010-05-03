<xsl:template match="delegate_data" xmlns:php="http://php.net/xsl">
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
   	<div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">
			<div class="add_delegate" style="float: left;">
				<h2>Ny delegat</h2>
			    <form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
			    	<dl>
			    		<dt>Brukernavn</dt>
			    		<dd><input type="text" name="username"/></dd>
			    		<dt>Fornavn</dt>
			    		<dd><input type="text" name="firstname"/></dd>
			    		<dt>Etternavn</dt>
			    		<dd><input type="text" name="lastname"/></dd>
			    		<dt>E-post</dt>
			    		<dd><input type="text" name="email"/></dd>
			    		<dt>Passord</dt>
			    		<dd><input type="password" name="password1"/></dd>
			    		<dt>Gjenta passord</dt>
			    		<dd><input type="password" name="password2"/></dd>
			    		<dt><input type="submit" name="search" value="Søk"/></dt>
			    		<dd><input type="submit" name="add" value="Søk"/></dd>
			    	</dl>
				</form>
			</div>
			<div class="delegates" style="float: right;">
				<xsl:choose>
			   		<xsl:when test="not(normalize-space(delegate)) and (count(delegate) &lt;= 1)">
			   			 <em style="margin-left: 1em; float: left;"><xsl:value-of select="php:function('lang', 'no_delegates')"/></em>
			   		</xsl:when>
					<xsl:otherwise>
						<ul>
							<xsl:foreach select="delegate">
								<li>
										<xsl:value-of select="account_firstname"/>&amp;nbsp;<xsl:value-of select="account_lastname"/>
										(<xsl:value-of select="account_lid"/>)
											<a href="index.php?menuaction=frontend.uidelegate.remove_deletage&amp;account_id={account_id}">Fjern</a>
								</li>
							</xsl:foreach>
						</ul>
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</div>	
	</div>
</xsl:template>


