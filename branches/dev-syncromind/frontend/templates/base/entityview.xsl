<xsl:template match="entityinfo" xmlns:php="http://php.net/xsl">
	
	<script type="text/javascript">
		var property_js = <xsl:value-of select="property_js" />;
		var base_java_url = <xsl:value-of select="base_java_url" />;
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

	showlightbox_edit_entity = function(location_id, id)
	{
		var oArgs = {menuaction:'property.uientity.edit', location_id:location_id, id: id, noframework:1, lean: 1};
		var sUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:sUrl, boxid:'frameless',width:750,height:550,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true,
		closejs:function(){refresh_entity(location_id, id)}
		});
	}

	showlightbox_start_ticket = function(sUrl)
	{
		TINY.box.show({iframe:sUrl, boxid:'frameless',width:750,height:550,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true,
		close: true,
		closejs:function(){refresh_entity(false, false)}
		});
	}


	refresh_entity = function(location_id, id)
	{
		parent.location.reload();
	}


	</script>

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

    <div class="yui-navset" id="entity_tabview">
        <div class="yui-content">
        	<div id="entityinfo">
        		<ul style="margin: 2em;">
	<!--				<xsl:value-of disable-output-escaping="yes" select="tabs"/>-->
					<div id="info">

        			<li style="margin-bottom: 1em;">
        				<a href="{entitylist}"> &lt;&lt; <xsl:value-of select="php:function('lang', 'show all entities')"/></a>
        			</li>
        			<li style="margin-bottom: 1em;">
						<a href="#" onclick="showlightbox_edit_entity({location_id},{id});">
							<xsl:value-of select="php:function('lang', 'edit')"/>
						</a>
        			</li>
        			<li style="margin-bottom: 1em;">
						<a href="#" onclick="showlightbox_start_ticket('{start_ticket}');">
							<xsl:value-of select="php:function('lang', 'add ticket')"/>
						</a>
        			</li>
        			<li>
						<xsl:choose>
							<xsl:when test="location_data!=''">
								<li>
									<b><xsl:value-of select="php:function('lang', 'location')"/></b>
								</li>
								<div id="location">
									<table>
										<xsl:call-template name="location_view"/>
									</table>
								</div>
							</xsl:when>
						</xsl:choose>
					</li>
					<li>
						<xsl:apply-templates select="custom_attributes/attributes"/>
						<hr/>
        			</li>
<!--
					<xsl:choose>
						<xsl:when test="files!=''">
							<li>
								<div id="datatable-container_0"></div>
							</li>
						</xsl:when>
					</xsl:choose>
-->
					</div>

					<xsl:for-each select="integration">
						<div id="{section}">
							<iframe id="{section}_content" width="100%" height="{height}" src="{src}">
								<p>Your browser does not support iframes.</p>
							</iframe>
						</div>
					</xsl:for-each>


        		</ul>


        	</div>
        </div>
    </div>
</xsl:template>


