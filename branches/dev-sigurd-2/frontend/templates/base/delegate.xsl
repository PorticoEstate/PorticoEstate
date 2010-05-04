<xsl:template match="delegate_data" xmlns:php="http://php.net/xsl">
   	<div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">
			<div class="add_delegate" style="float: left; padding-left: 2em; padding-top: 2em;">
				<xsl:value-of select="php:function('lang', 'find_user')"/>
				<table cellpadding="2" cellspacing="2" align="center">
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
			    <xsl:variable name="btn_add"><xsl:value-of select="php:function('lang', 'btn_add')"/></xsl:variable>
			    <xsl:variable name="btn_search"><xsl:value-of select="php:function('lang', 'btn_search')"/></xsl:variable>
			
			   
			    
			    <form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
			    	<input type="hidden" name="account_id" value="{search/account_id}"/>
			    	<dl>
			    		<dt><xsl:value-of select="php:function('lang', 'username')"/></dt>
			    		<dd><input type="text" name="username" value="{search/username}"/><input type="submit" name="search" value="{$btn_search}"/></dd>
			    		<dt><xsl:value-of select="php:function('lang', 'firstname')"/></dt>
			    		<dd><input type="text" name="firstname" readonly="" value="{search/firstname}" style="background-color: #CCCCCC;"/></dd>
			    		<dt><xsl:value-of select="php:function('lang', 'lastname')"/></dt>
			    		<dd><input type="text" name="lastname" readonly="" value="{search/lastname}" style="background-color: #CCCCCC;"/></dd>
			    		<dt><xsl:value-of select="php:function('lang', 'email')"/></dt>
			    		<dd><input type="text" name="email" readonly="" value="{search/email}" style="background-color: #CCCCCC;"/></dd>
			    		<!-- <dt><xsl:value-of select="php:function('lang', 'password')"/></dt>
			    		<dd><input type="password" name="password1"/></dd>
			    		<dt><xsl:value-of select="php:function('lang', 'repeat_password')"/></dt>
			    		<dd><input type="password" name="password2"/></dd> -->
			    		<dt></dt>
			    		<dd><input type="submit" name="add" value="{$btn_add}"/></dd>
			    	</dl>
				</form>
			</div>
			<div class="delegates" style="float: left; padding-left: 2em; padding-top: 2em;">
				Delegater
				<xsl:choose>
			   		<xsl:when test="not(normalize-space(delegate)) and (count(delegate) &lt;= 1)">
			   			 <em style="margin-left: 1em; float: left;"><xsl:value-of select="php:function('lang', 'no_delegates')"/></em>
			   		</xsl:when>
					<xsl:otherwise>
					 <xsl:variable name="btn_remove"><xsl:value-of select="php:function('lang', 'btn_remove')"/></xsl:variable>
						<ul>
							<xsl:for-each select="delegate">
								<li>
									  <form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
									  		<input type="hidden" name="account_id" value="{account_id}"/>
											<xsl:value-of select="account_lastname"/>, <xsl:value-of select="account_firstname"/> 
										
											(<xsl:value-of select="account_lid"/>)
											<input type="submit" name="remove" value="{$btn_remove}"/>
										</form>
								</li>
							</xsl:for-each>
						</ul>
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</div>	
	</div>
</xsl:template>


