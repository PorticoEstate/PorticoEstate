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
			<div class="toolbar-container">
			    <div class="toolbar">
			        <div class="field" style="float: right;">
			        	<span id="btn_new" class="yui-button yui-push-nutton">
			        		<span class="first-child">
			        			<button id="btn_new-button" type="button">Legg til delegat</button>
			        		</span>
			        	</span>
			        </div>
			    </div>
			</div>
			<div class="delegates">
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

<xsl:template match="lightbox_name" xmlns:php="http://php.net/xsl">
</xsl:template>

<xsl:template match="add_ticket" xmlns:php="http://php.net/xsl">
	<xsl:choose>
        <xsl:when test="normalize-space(redirect) != ''">
            <script>
            	window.parent.location = '<xsl:value-of select="redirect"/>';
            	window.close();
            </script>
        </xsl:when>
    </xsl:choose>
    <h2>Ny delegat</h2>
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
			<form>
		<div>
			<label for="username">Brukernavn
				<input type="text" name="username"/>
				<input type="submit" name="Search" value="Søk"/>
			</label>
		</div>
		<div>
			<ul>
				<li>
					<label for="firstname"> Fornavn
						<input type="text" name="firstname"/>
					</label>
				</li>
				<li>
					<label for="lastname"> Etternavn
						<input type="text" name="lastname"/>
					</label>
				</li>
				<li>
					<label for="email"> E-post
						<input type="text" name="email"/>
					</label>
				</li>
				
				<li>
					<label for="password1"> Passord
						<input type="password" name="password1"/>
					</label>
				</li>
				<li>
					<label for="password2"> Gjenta passord
						<input type="password" name="password2"/>
					</label>
				</li>
			</ul>
		</div>
	</form>
        </table>
    </form>
</xsl:template>


