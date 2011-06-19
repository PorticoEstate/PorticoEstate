<xsl:template match="entityinfo" xmlns:php="http://php.net/xsl">
	
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
        			<li style="margin-bottom: 1em;">
        				<a href="{entitylist}"> &lt;&lt; <xsl:value-of select="php:function('lang', 'show_all_entities')"/></a>
        			</li>
        			<li>
        				<ul>
        					<li style="margin-bottom: 5px;">
        						<img src="frontend/templates/base/images/16x16/comment.png" class="list_image"/> <strong><xsl:value-of select="entity/subject"/></strong>
        					</li>
        					<li class="entity_detail">
        						<img src="frontend/templates/base/images/16x16/clock_edit.png" class="list_image"/> <xsl:value-of select="php:function('lang', 'entry_date')"/> <xsl:value-of select="entity/entry_date"/><xsl:value-of select="php:function('lang', 'of')"/><xsl:value-of select="entity/user_name"/>
        					</li>
     						<li class="entity_detail">
     							<img src="frontend/templates/base/images/16x16/timeline_marker.png" class="list_image"/> <xsl:value-of select="php:function('lang', 'status')"/>: <xsl:value-of select="entity/status_name"/>
     						</li>
     						<xsl:choose>
     							<xsl:when test="entity/value_vendor_name">
		     						<li class="entity_detail">
		     							<img src="frontend/templates/base/images/16x16/user_suit.png" class="list_image"/> <xsl:value-of select="php:function('lang', 'vendor')"/>: <xsl:value-of select="entity/value_vendor_name"/>
		     						</li>
		     					</xsl:when>
     						</xsl:choose>
     						<xsl:choose>
     							<xsl:when test="entity/assigned_to_name">
		     						<li class="entity_detail">
		     							<img src="frontend/templates/base/images/16x16/user_red.png" class="list_image"/> <xsl:value-of select="php:function('lang', 'assigned_to')"/>: <xsl:value-of select="entity/assigned_to_name"/>
		     						</li>
		     					</xsl:when>
     						</xsl:choose>
     						<xsl:choose>
     							<xsl:when test="entity/value_contact_name">
		     						<li class="entity_detail">
		     							<img src="frontend/templates/base/images/16x16/user_green.png" class="list_image"/> <xsl:value-of select="php:function('lang', 'contact')"/>: <xsl:value-of select="entity/value_contact_name"/>
		     							Telefon: <xsl:value-of select="entity/value_contact_tel"/> 
		     							E-post: <xsl:value-of select="entity/value_contact_email"/>
		     						</li>
		     					</xsl:when>
     						</xsl:choose>
     						<xsl:choose>
     							<xsl:when test="entity/publish_note = 1">
		     						<li class="entity_detail">
		     							<img src="frontend/templates/base/images/16x16/page_white_edit.png" class="list_image"/><xsl:value-of select="php:function('lang', 'message')"/>: <xsl:value-of select="entity/details"/>
		     						</li>
		     					</xsl:when>
     						</xsl:choose>
     						<li class="entity_detail">
     							<img src="frontend/templates/base/images/16x16/comments.png" class="list_image"/> <xsl:value-of select="php:function('lang', 'comments')"/>:<br/>
     							<hr/>
     							<ul>
		        					<xsl:for-each select="entityhistory/*[starts-with(name(), 'record')]">
						                <li  class="entity_detail">
						                	<img src="frontend/templates/base/images/16x16/page_white_edit.png" class="list_image"/> <xsl:value-of select="date"/> - 
						                	<img src="frontend/templates/base/images/16x16/user_gray.png" class="list_image"/> <xsl:value-of select="user"/><br/>
						                	<p style="padding: 10px;"><xsl:value-of select="note"/></p>
						                </li>
						            </xsl:for-each>
				            	</ul>
     						</li>
     					</ul>
        			</li>
        		</ul>
        	</div>
        </div>
    </div>
</xsl:template>


