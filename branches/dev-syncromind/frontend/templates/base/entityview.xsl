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

	<xsl:choose>
	    <xsl:when test="msgbox_data != ''">
			<xsl:call-template name="msgbox"/>
	    </xsl:when>
   </xsl:choose>
   
	<xsl:variable name="tab_selected"><xsl:value-of select="tab_selected"/></xsl:variable>
	
	<div class="frontend_body">
		<div>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs" />
				<div id="{$tab_selected}">
					<div class="ticket_content">
						<div class="pure-control-group">
							
								<a class="pure-button" href="{entitylist}"> &lt;&lt; <xsl:value-of select="php:function('lang', 'show all entities')"/></a>
								<a class="pure-button" href="#" onclick="showlightbox_edit_entity({location_id},{id});"><xsl:value-of select="php:function('lang', 'edit')"/></a>
								<a class="pure-button" href="#" onclick="showlightbox_start_ticket('{start_ticket}');"><xsl:value-of select="php:function('lang', 'add ticket')"/></a>
							
						</div>						

						<xsl:choose>
							<xsl:when test="location_data!=''">
								<xsl:call-template name="location_view"/>
							</xsl:when>
						</xsl:choose>

						<xsl:apply-templates select="custom_attributes/attributes"/>

						<xsl:for-each select="integration">
							<div id="{section}">
								<iframe id="{section}_content" width="100%" height="{height}" src="{src}">
									<p>Your browser does not support iframes.</p>
								</iframe>
							</div>
						</xsl:for-each>
					</div>
				</div>
				<xsl:value-of disable-output-escaping="yes" select="tabs_content" />
			</div>
		</div>
	</div>
</xsl:template>


