<xsl:template match="delegate_data" xmlns:php="http://php.net/xsl">
	<div class="yui-content">
		<div class="toolbar-container">
		    <div class="toolbar">
		        <xsl:apply-templates select="datatable/actions" />  
		    </div>
		</div>
		<div class="delegates">
			<xsl:choose>
		   		<xsl:when test="not(normalize-space(delegate)) and (count(delegate) &lt;= 1)">
		   			 <em style="margin-left: 1em; float: left;"><xsl:value-of select="php:function('lang', 'no_delegates')"/></em>
		   		</xsl:when>
				<xsl:otherwise>
					<ul>
						<xsl:foreach select="delegate">
							<li>
									<xsl:value-of select="account_firstname"/>&amp;nbsp;<xsl:value-of select="account_lastname"/>
									(<xsl:value-of select="account_lid"/>)
										<a href="index.php?menuaction=frontend.uidelegate.remove_deletage&amp;account_id={account_id}">Fjern</a>
								
							</li>
						</xsl:foreach>
					</ul>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</div>	
</xsl:template>


