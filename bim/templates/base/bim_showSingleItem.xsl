
    <xsl:template match="/">
    	<div class="bimObject">
	        <xsl:apply-templates select="*">
	                
	        </xsl:apply-templates>
        </div>
        
    </xsl:template>
    <xsl:template match="*">
    <div>
    	
    	<!-- tests if children have no children  -->
    	<!-- 
    	<xsl:choose>
    		
    		<xsl:when test="count(*[not(*)]) = 0 and count(*)=0">
    			<dl>
   					<xsl:call-template name="childElements"/>
   				</dl>
    		</xsl:when> 
    		<xsl:otherwise>
    			<h1><xsl:value-of select="name()"/></h1>
    		</xsl:otherwise>
    	</xsl:choose>
    	 -->
    	 <xsl:choose>
		    <xsl:when test="not(*)">
		    	
			    	<dl>
			    		
					<dt>
						<xsl:value-of select="name()"/>
					</dt>
					<dd>
						<xsl:value-of select="."/>
					</dd>
			    		
			    	</dl>
		    	
		    </xsl:when>
		    <xsl:otherwise>
				<h1>
					<xsl:value-of select="name()"/>
				</h1>
		    </xsl:otherwise>
		</xsl:choose>
    	<xsl:if test="@*">
    		<h2>Xml attributes</h2>
    		<dl>
    		<xsl:for-each select="@*">
					<dt>
						<xsl:value-of select="name()"/>
					</dt>
					<dd>
						<xsl:value-of select="."/>
					</dd>
            </xsl:for-each>
            </dl>
    	</xsl:if>
		
    	<!-- 
    	<xsl:choose>
		    <xsl:when test="not(*)">
		    	<dl>
		    		<dt><xsl:value-of select="name()"/></dt>
		    		<dd><xsl:value-of select="."/></dd>
		    	</dl> 
		    </xsl:when>
		    <xsl:otherwise>
		        <h1><xsl:value-of select="name()"/></h1>
		    </xsl:otherwise>
		</xsl:choose>
		 -->
    	
		
		<xsl:apply-templates select="*">
			
		</xsl:apply-templates>
	</div>
</xsl:template>
<xsl:template name="childElements" >
	<dt>
		<xsl:value-of select="name()"/>
	</dt>
	<dd>
		<xsl:value-of select="."/>
	</dd>
		
</xsl:template>
