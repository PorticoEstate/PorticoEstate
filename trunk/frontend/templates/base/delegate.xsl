<xsl:template match="delegate_data" xmlns:php="http://php.net/xsl">
   	<div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">
		<xsl:variable name="unit_leader" select="//header/org_unit[ORG_UNIT_ID = //selected_org_unit]/LEADER"></xsl:variable>
		<xsl:choose>
			<xsl:when test="//selected_org_unit = 'all' or $unit_leader = '1'">
				<div class="add_delegate" style="width=30%; height=100%; float: left; padding-left: 2em; padding-top: 2em; padding-bottom: 2em; margin-right: 2em;">
					<xsl:choose>
						<xsl:when test="number_of_delegates &lt; delegate_limit">
							<img src="frontend/templates/base/images/16x16/group_add.png" class="list_image"/><xsl:value-of select="php:function('lang', 'find_user')"/>
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
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="error_message"/>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</xsl:when>
		</xsl:choose>
			
			
		<xsl:choose>
           <xsl:when test="msgbox_data != ''">
           	<xsl:call-template name="msgbox"/>
           </xsl:when>
       </xsl:choose>
		
			<xsl:choose>
			   		<xsl:when test="//selected_org_unit != 'all'">
			   			<div class="delegates" style=" float: left; padding-left: 2em; padding-top: 2em; width=70%; text-align: left;">
							<h3><xsl:value-of select="php:function('lang', 'delegates_for_res_unit')"/> (<xsl:value-of select="number_of_delegates"/>)</h3>
							<xsl:choose>
						   		<xsl:when test="not(normalize-space(delegate)) and (count(delegate) &lt;= 1)">
						   			 <em style="margin-left: 1em; "><xsl:value-of select="php:function('lang', 'no_delegates')"/></em>
						   		</xsl:when>
								<xsl:otherwise>
								 <xsl:variable name="btn_remove"><xsl:value-of select="php:function('lang', 'btn_remove')"/></xsl:variable>
								 	
									<ul>
										<xsl:for-each select="delegate">
											<li>
												 <xsl:choose>
												 	<xsl:when test="$unit_leader = '1'">
													  <form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
													  		<input type="hidden" name="account_id" value="{account_id}"/>
															 <img src="frontend/templates/base/images/16x16/user_gray.png" class="list_image"/><xsl:value-of select="account_lastname"/>, <xsl:value-of select="account_firstname"/> 
															(<xsl:value-of select="account_lid"/>)
															<input type="submit" name="remove_specific" value="{$btn_remove}"/>
														</form>
													</xsl:when>
													<xsl:otherwise>
														<img src="frontend/templates/base/images/16x16/user_gray.png" class="list_image"/><xsl:value-of select="account_lastname"/>, <xsl:value-of select="account_firstname"/> 
															(<xsl:value-of select="account_lid"/>)
													</xsl:otherwise>
												</xsl:choose>
												
											</li>
										</xsl:for-each>
									</ul>
								</xsl:otherwise>
							</xsl:choose>
						</div>
			   		</xsl:when>
			   		<xsl:otherwise>
			   			<div class="delegates" style="float: left; padding-left: 2em; padding-top: 2em; width=70%;">
			   				<h3 style="color: red;"><xsl:value-of select="php:function('lang', 'deletage_to_all_res_units')"/></h3>
			   			</div>
			   		</xsl:otherwise>
			</xsl:choose>
								
			<xsl:choose>
				<xsl:when test="normalize-space(//user_delegate) != ''">	       
					<div class="delegates" style="padding-left: 2em; padding-top: 2em; width=70%; float: left;">
						<h3><xsl:value-of select="php:function('lang', 'delegates_for_user')"/> (<xsl:value-of select="number_of_user_delegates"/>)</h3>
						<xsl:choose>
					   		<xsl:when test="not(normalize-space(user_delegate)) and (count(user_delegate) &lt;= 1)">
					   			 <em style="margin-left: 1em;"><xsl:value-of select="php:function('lang', 'no_delegates')"/></em>
					   		</xsl:when>
							<xsl:otherwise>
							 <xsl:variable name="btn_remove"><xsl:value-of select="php:function('lang', 'btn_remove')"/></xsl:variable>
								<ul>
									<xsl:for-each select="user_delegate">
										<li>
										  <form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
										  		<input type="hidden" name="account_id" value="{account_id}"/>
												 <img src="frontend/templates/base/images/16x16/user_gray.png" class="list_image"/><xsl:value-of select="account_lastname"/>, <xsl:value-of select="account_firstname"/> 
												(<xsl:value-of select="account_lid"/>)
												<input type="submit" name="remove" value="{$btn_remove}"/>
											</form>
										</li>
									</xsl:for-each>
								</ul>
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</xsl:when>
			</xsl:choose>
		</div>	
	</div>
</xsl:template>


