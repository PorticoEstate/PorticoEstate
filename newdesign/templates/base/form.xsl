<func:function name="phpgw:label">
	<func:result>
		<xsl:if test="title">
			<label>
				<xsl:attribute name="for">
					 <xsl:value-of select="phpgw:or(id,generate-id())"/>
				</xsl:attribute>		
			
				<xsl:if test="tooltip">
					<xsl:attribute name="title">
						<xsl:value-of select="tooltip"/>
					</xsl:attribute>
					<xsl:attribute name="style">
						cursor:help
					</xsl:attribute>
				</xsl:if>			
	
				<xsl:choose>
					<xsl:when test="accesskey and contains(title, accesskey)">
						<xsl:value-of select="substring-before (title, accesskey)"/>
						<u>
							<xsl:value-of select="accesskey"/>
						</u>
						<xsl:value-of select="substring-after (title, accesskey)"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="title"/>
					</xsl:otherwise>		
				</xsl:choose>		
			</label>
		</xsl:if>		
	</func:result>
</func:function>

<xsl:template match="phpgw">
	<xsl:apply-templates /> 
</xsl:template>


<xsl:template name="form" match="form">
	<form id="test-form">
		<h2><xsl:value-of select="title"/></h2>		
		<xsl:apply-templates select="fieldset" />
		<xsl:apply-templates select="field | textarea" />
		<xsl:apply-templates select="bottom_toolbar" />
		
		<input type="submit" value="Save" />
		<input type="submit" value="Apply" />
		<input type="submit" value="Cancel" />
	</form>  
</xsl:template>

<xsl:template match="fieldset">
	<fieldset>
		<legend>
			<xsl:value-of select="title"/>
		</legend>	
		<xsl:apply-templates select="field | textarea" />
	</fieldset> 
</xsl:template>

<xsl:attribute-set name="core-attributes">
	<!--  class, id, style, title -->
	<xsl:attribute name="id">
		<xsl:value-of select="phpgw:or(id,generate-id())"/>  
	</xsl:attribute>
	
	<xsl:attribute name="class">
		<xsl:if test="error">
			error
		</xsl:if>
		
		<xsl:if test="readonly">
			readonly
		</xsl:if>
		
		<xsl:if test="disabled">
			disabled
		</xsl:if>
		
		<xsl:value-of select="class"/>
	</xsl:attribute>
	
	<xsl:attribute name="style">
		<xsl:value-of select="style"/>
	</xsl:attribute>
</xsl:attribute-set>

<xsl:attribute-set name="input-attributes">
	<xsl:attribute name="name">
		<xsl:value-of select="name"/>
	</xsl:attribute>
	
	<xsl:attribute name="value">
		<xsl:value-of select="value"/>
	</xsl:attribute>	
</xsl:attribute-set>

<xsl:template name="field" match="field">   	
	<xsl:copy-of select="phpgw:label()"/>
	
	<xsl:choose>	
		<xsl:when test="type='textarea'">
			<xsl:call-template name="textarea"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="textfield"/>
		</xsl:otherwise>
	</xsl:choose>
	
	<xsl:if test="error">
		<div class="error">
			<xsl:value-of select="error"/>
		</div>
	</xsl:if>
	<br />	
</xsl:template>

<xsl:template name="textfield">
	<input xsl:use-attribute-sets="input-attributes core-attributes">		
		<xsl:if test="accesskey">
			<xsl:attribute name="accesskey">
				<xsl:value-of select="accesskey"/>
			</xsl:attribute>
		</xsl:if>				

		<xsl:if test="disabled">
			<xsl:attribute name="disabled">
				disabled
			</xsl:attribute>
		</xsl:if>
		
		<xsl:if test="readonly">
			<xsl:attribute name="readonly">
				readonly
			</xsl:attribute>
		</xsl:if>	
			
		<xsl:attribute name="type">
			<xsl:value-of select="phpgw:or(type,'text')"/>			
		</xsl:attribute>					
	</input>
</xsl:template>

<xsl:template name="textarea">
	<xsl:if test="accesskey">
		<xsl:attribute name="accesskey">
			<xsl:value-of select="accesskey"/>
		</xsl:attribute>
	</xsl:if>				
	
	<textarea xsl:use-attribute-sets="input-attributes core-attributes">
		<xsl:value-of select="value"/>
	</textarea>
</xsl:template>  
