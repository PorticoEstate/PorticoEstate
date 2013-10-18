<xsl:template match="header" xmlns:php="http://php.net/xsl">
	<xsl:variable name="messages_url"><xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:frontend.uimessages.index')" /></xsl:variable>
	<div id="wrapper">
    	<div id="header">
    		<div id="login-bar">
    			<ul class="user_menu">
    				<li><em><img src="frontend/templates/base/images/16x16/user_red.png"  class="list_image" /></em><xsl:value-of select="name_of_user"/> | <a href="http://portico/pe/preferences/changepassword.php">Bytt passord</a></li>
	  				<li><a href="{$messages_url}" class="list_image"><em><img src="frontend/templates/base/images/16x16/email.png" class="list_image"/></em><xsl:value-of select="new_messages"/></a></li>
    				<li>
    					<a href="logout.php"  class="header_link"><em><img src="frontend/templates/base/images/16x16/door_out.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'logout')"/></a> 
    					|
    					<a href="{home_url}"  class="header_link"><em><img src="frontend/templates/base/images/16x16/door_open.png" /></em><xsl:value-of select="php:function('lang', 'home')"/></a>
    				</li>
    			</ul>
			</div>
			<div id="information">
				<ul>
					<li><em><img src="frontend/templates/base/images/16x16/help.png" class="list_image"/></em><a href="{help_url}" class="header_link"><xsl:value-of select="php:function('lang', 'help')"/></a></li>
    				<li><em><img src="frontend/templates/base/images/16x16/group.png"  class="list_image"/></em><a href="{contact_url}" class="header_link"><xsl:value-of select="php:function('lang', 'contact_BKB')"/></a></li>
    				<li><em><img src="frontend/templates/base/images/16x16/page.png" class="list_image"/></em><a href="{folder_url}" class="header_link"><xsl:value-of select="php:function('lang', 'folder')"/></a></li>
				</ul>
			</div>
			<div id="area_and_price">
				<ul>
					<li ><em><img src="frontend/templates/base/images/16x16/house.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'number_of_units')"/>: <xsl:value-of select="number_of_locations"/> </li>
    				<li><em><img src="frontend/templates/base/images/16x16/shading.png"  class="list_image"/></em><xsl:value-of select="php:function('lang', 'total_area_internal')"/>: <xsl:value-of select="total_area"/></li>
    				<li><em><img src="frontend/templates/base/images/16x16/coins.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'total_price_internal')"/>: <xsl:value-of select="total_price"/></li>
    			</ul>
			</div>
			<xsl:choose>
	    		<xsl:when test="use_fellesdata = 1">
					<div id="org_units">
		    			<ul>
		    				<li>
		    					<em>
		    						<img src="frontend/templates/base/images/16x16/chart_organisation.png"  class="list_image" />
		    					</em>
		    					<xsl:value-of select="php:function('lang', 'organisational_units')"/> 
		    					(<xsl:value-of select="number_of_org_units"/>)
		    					<a href="{form_action}&amp;refresh=true" class="list_image">
		    						<img src="frontend/templates/base/images/16x16/page_refresh.png" class="list_image"/>
		    					</a>
		    				</li>
		    				<li>
		    					<form action="{form_action}" method="post">
			    					<select size="3" onchange="this.form.submit()" name="org_unit_id">
			    						<option value="none">
											<xsl:if test="'none' = //header/selected_org_unit">
												<xsl:attribute name="selected" value="selected"/>
											</xsl:if>
				    						<xsl:value-of select="php:function('lang', 'none')"/>
			    						</option>
			    						<option value="all">
											<xsl:if test="'all' = //header/selected_org_unit">
												<xsl:attribute name="selected" value="selected"/>
											</xsl:if>
			    							<xsl:value-of select="php:function('lang', 'all_organisational_units')"/>
			    						</option>
			    						<xsl:for-each select="org_unit">
			    							<xsl:sort select="ORG_NAME"/>
											<option value="{ORG_UNIT_ID}" >
												<xsl:if test="ORG_UNIT_ID = //header/selected_org_unit">
													<xsl:attribute name="selected" value="selected"/>
												</xsl:if>
												<xsl:value-of disable-output-escaping="yes" select="ORG_NAME"/>
											</option>
					    				</xsl:for-each>
			    					</select>
		    					</form>
		    				</li>
		    			</ul>
					</div>
				</xsl:when>
			</xsl:choose>
			<div id="logo_holder">
				<img src="{logo_path}"/>
			</div>
		</div>
    </div>
	<table id="header">
		<xsl:choose>
			<xsl:when test="number_of_locations = 0">
				<tr valign="top">
					<td>
						<label>
							<xsl:choose>
								<xsl:when test="use_fellesdata != 1">
									<a href="{form_action}&amp;refresh=true" class="list_image">
								    						<img src="frontend/templates/base/images/16x16/page_refresh.png" class="list_image"/>
								    					</a>
								  </xsl:when>
							</xsl:choose>
							<img src="frontend/templates/base/images/32x32/house.png" class="list_image"/>
							<em class="select_header"><xsl:value-of select="php:function('lang', 'no_buildings')"/></em>
						</label>
					</td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
				<tr valign="top">
					<td>
						<div id="unit_selector">
							<form action="{form_action}" method="post">
								<label>
									<xsl:choose>
										<xsl:when test="use_fellesdata != 1">
											<a href="{form_action}&amp;refresh=true" class="list_image">
										    						<img src="frontend/templates/base/images/16x16/page_refresh.png" class="list_image"/>
										    					</a>
										  </xsl:when>
									</xsl:choose>
									<img src="frontend/templates/base/images/32x32/house.png" class="list_image"/>
									<em class="select_header"><xsl:value-of select="php:function('lang', 'select_unit')"/></em>
								</label>
								<br/>
								<xsl:variable name="lang_no_name_unit"><xsl:value-of select="php:function('lang', 'no_name_unit')"/></xsl:variable>								
								<select name="location" size="7" onchange="this.form.submit();" style="margin:5px;">
									<xsl:for-each select="locations">
										<xsl:sort select="loc1_name"/>
										<xsl:choose>
											<xsl:when test="location_code = //header/selected_location">
												<option value="{location_code}" selected="selected">
													<xsl:choose>
														<xsl:when test="name != ''">
															<xsl:value-of select="name"/>
														</xsl:when>
														<xsl:otherwise>
															<xsl:value-of select="$lang_no_name_unit"/> (<xsl:value-of select="location_code"/>)
														</xsl:otherwise>
													</xsl:choose>
												</option>
											</xsl:when>
											<xsl:otherwise>
												<option value="{location_code}">
												<xsl:choose>
													<xsl:when test="name != ''">
														<xsl:value-of select="name"/>
													</xsl:when>
													<xsl:otherwise>
															<xsl:value-of select="$lang_no_name_unit"/> (<xsl:value-of select="location_code"/>)
													</xsl:otherwise>
												</xsl:choose>
												</option>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:for-each>
								</select>
							</form>
						</div>
					</td>
					<td>
						<div id="area_and_price" style="margin-top: 2em;">
						<ul>
							<li style="border-style: none none solid none; border-width: 1px; border-color: grey; padding-bottom: 5px; "><em><img src="frontend/templates/base/images/16x16/house.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'chosen_unit')"/>:</li>
		    				<li><em><img src="frontend/templates/base/images/16x16/shading.png"  class="list_image"/></em><xsl:value-of select="php:function('lang', 'total_area_internal')"/>: <xsl:value-of select="selected_total_area"/></li>
		    				<li><em><img src="frontend/templates/base/images/16x16/coins.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'total_price_internal')"/>: <xsl:value-of select="selected_total_price"/></li>
		    			</ul>
						</div>
					</td>
					<td>
						<br/>
						<div id="unit_image">
							<img alt="">
								<xsl:attribute name="src">
									<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:frontend.uifrontend.objectimg')" />
									<xsl:text>&amp;loc_code=</xsl:text>
									<xsl:value-of select="//header/selected_location"/>
								</xsl:attribute>
							</img>
						</div>
					</td>
				</tr>
			</xsl:otherwise>
		</xsl:choose>
	</table>
</xsl:template>
	
<xsl:template match="tabs">
	<xsl:value-of disable-output-escaping="yes" select="." />
</xsl:template>

