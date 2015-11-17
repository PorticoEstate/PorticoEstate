<xsl:template match="entityinfo" xmlns:php="http://php.net/xsl">
	
	<script type="text/javascript">

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

    <xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
	<xsl:variable name="tab_selected"><xsl:value-of select="tab_selected"/></xsl:variable>
	
	<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-stacked">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div id="{$tab_selected}">
				<fieldset>
						<div class="pure-control-group">
							<label>
								<a href="{entitylist}"> &lt;&lt; <xsl:value-of select="php:function('lang', 'show all entities')"/></a>
							</label>
						</div>						
						<div class="pure-control-group">
							<label>
								<a href="#" onclick="showlightbox_edit_entity({location_id},{id});"><xsl:value-of select="php:function('lang', 'edit')"/></a>
							</label>
						</div>						
						<div class="pure-control-group">
							<label>
								<a href="#" onclick="showlightbox_start_ticket('{start_ticket}');"><xsl:value-of select="php:function('lang', 'add ticket')"/></a>
							</label>
						</div>
    
        	
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
				

						<xsl:apply-templates select="custom_attributes/attributes"/>
        			
<!--
					<xsl:choose>
						<xsl:when test="files!=''">
							<li>
								<div id="datatable-container_0"></div>
							</li>
						</xsl:when>
					</xsl:choose>
-->


					<xsl:for-each select="integration">
						<div id="{section}">
							<iframe id="{section}_content" width="100%" height="{height}" src="{src}">
								<p>Your browser does not support iframes.</p>
							</iframe>
						</div>
					</xsl:for-each>
				</fieldset>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs_content" />
		</div>
	</form>
</xsl:template>


