<xsl:template match="contract_data" xmlns:php="http://php.net/xsl">
	<!-- <xsl:copy-of select="."/> -->
    <div class="yui-navset" id="documents_tabview">
    <xsl:value-of disable-output-escaping="yes" select="tabs" />
        <div class="yui-content">
        	<div class="toolbar-container">
	        	<div class="toolbar" style="display: block; padding-bottom: 1em;">
	            	<div id="contract_selector">
			           <img src="frontend/templates/base/images/16x16/page_white_stack.png" class="list_image"/>
			           <form action="{form_url}" method="post" style="float:left;">
		           			<select name="contract_filter" onchange="this.form.submit()">
		           				<xsl:choose>
		           					<xsl:when test="//contract_filter = 'active'">
		           						<option value="active" selected="selected"><xsl:value-of select="php:function('lang', 'active')"/></option>
		           					</xsl:when>
		           					<xsl:otherwise>
		           						<option value="active"><xsl:value-of select="php:function('lang', 'active')"/></option>
		           					</xsl:otherwise>
		           				</xsl:choose>
		           				<xsl:choose>
		           					<xsl:when test="//contract_filter = 'not_active'">
		           						<option value="not_active" selected="selected"><xsl:value-of select="php:function('lang', 'not_active')"/></option>
		           					</xsl:when>
		           					<xsl:otherwise>
		           						<option value="not_active"><xsl:value-of select="php:function('lang', 'not_active')"/></option>
		           					</xsl:otherwise>
		           				</xsl:choose>
		           				<xsl:choose>
		           					<xsl:when test="//contract_filter = 'all'">
		           						<option value="all" selected="selected"><xsl:value-of select="php:function('lang', 'all')"/></option>
		           					</xsl:when>
		           					<xsl:otherwise>
		           						<option value="all"><xsl:value-of select="php:function('lang', 'all')"/></option>
		           					</xsl:otherwise>
		           				</xsl:choose>
		           			</select>
		           		</form>
				        <xsl:choose>
			           		<xsl:when test="not(normalize-space(select)) and (count(select) &lt;= 1)">
			           			 <em style="margin-left: 1em; float: left;"><xsl:value-of select="php:function('lang', 'no_contracts')"/></em>
			           		</xsl:when>
			           		<xsl:otherwise>
					             <form action="{form_url}" method="post" style="float: left;">
						           	<xsl:for-each select="select">
						           		<xsl:choose>
							           		<xsl:when test="id = //selected_contract">
						           				<input name="contract_id" type="radio" value="{id}" checked="" onclick="this.form.submit();" style="margin-left: 1em;"></input> 
						           			</xsl:when>
						           			<xsl:otherwise>	
						           				<input name="contract_id" type="radio" value="{id}" onclick	="this.form.submit();" style="margin-left: 1em;"></input>
						           			</xsl:otherwise>
						           		</xsl:choose>
						           		<label style="margin-right: 1em; padding-left: 5px;"> <xsl:value-of select="old_contract_id"/> (<xsl:value-of select="contract_status"/>)</label>
						           	</xsl:for-each>
					           	  </form>
					         </xsl:otherwise>
						</xsl:choose>
		 			</div>
		 		</div>
	 		</div>
	 		<div style="clear: both;"></div>
 			<div class="tickets">
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
			    <div id="paging_0"> </div>
				<div id="datatable-container_0"></div>
   	 			<xsl:apply-templates select="datatable" />
			</div>
        </div>
    </div>
</xsl:template>

<xsl:template name="datatable" match="datatable">
	<!--  DATATABLE DEFINITIONS-->
	<script type="text/javascript">
		var property_js = <xsl:value-of select="property_js" />;
		var datatable = new Array();
		var myColumnDefs = new Array();

		<xsl:for-each select="datatable">
			datatable[<xsl:value-of select="name"/>] = [
			{
				values			:	<xsl:value-of select="values"/>,
				total_records	: 	<xsl:value-of select="total_records"/>,
				edit_action		:  	<xsl:value-of select="edit_action"/>,
				is_paginator	:  	<xsl:value-of select="is_paginator"/>,
				footer			:	<xsl:value-of select="footer"/>
			}
			]
		</xsl:for-each>

		<xsl:for-each select="myColumnDefs">
			myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
		</xsl:for-each>
	</script>
</xsl:template>