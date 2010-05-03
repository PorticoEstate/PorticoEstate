<xsl:template match="delegate_data" xmlns:php="http://php.net/xsl">
	<xsl:copy-of select="." />
	<div class="yui-content">
		<div class="toolbar-container">
		    <div class="toolbar" style="display: block;">
		        <div class="field" style="float: right;">
		        	<span id="btn_new" class="yui-button yui-push-nutton">
		        		<span class="first-child">
		        			<button id="btn_new-button" type="button">Legg til delegat</button>
		        		</span>
		        	</span>
		        </div>
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


