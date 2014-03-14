<!-- $Id: drawings.xsl 11378 2013-10-18 08:26:49Z sigurdne $ -->
<xsl:template match="drawings" xmlns:php="http://php.net/xsl">
    <xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
    <div class="yui-navset" id="drawing_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
        <div class="yui-content">
        	<xsl:choose>
				<xsl:when test="normalize-space(//header/selected_location) != ''">
					<div class="toolbar-container">
		                <div class="toolbar">
		                    <xsl:apply-templates select="datatable/actions" />  
		                </div>
		            </div>
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
		            	<xsl:apply-templates select="datatable" />
		            </div>
				</xsl:when>
				<xsl:otherwise>
					<div class="tickets">
		            	<xsl:value-of select="php:function('lang', 'no_buildings')"/>
		            </div>
				</xsl:otherwise>
			</xsl:choose>  
        </div>
    </div>
</xsl:template>

<xsl:template name="datatable" match="datatable">
	<div id="paging_0"> </div>
	<div id="datatable-container_0"></div>
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
