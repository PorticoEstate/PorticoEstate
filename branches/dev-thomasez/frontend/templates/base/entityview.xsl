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
        				<a href="{entitylist}"> &lt;&lt; <xsl:value-of select="php:function('lang', 'show all entities')"/></a>
        			</li>
        			<li>
						<xsl:choose>
							<xsl:when test="location_data!=''">
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
					<xsl:choose>
						<xsl:when test="files!=''">
							<li>
								<table cellpadding="2" cellspacing="2" width="80%" align="center">
									<!-- <xsl:call-template name="file_list"/> -->
									<tr>
										<td align="left" valign="top">
											<xsl:value-of select="php:function('lang', 'files')"/>
										</td>
										<td>
											<div id="datatable-container_0"></div>
										</td>
									</tr>
								</table>
							</li>
						</xsl:when>
					</xsl:choose>
        		</ul>
        	</div>
        </div>
    </div>
</xsl:template>


