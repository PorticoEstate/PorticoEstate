<func:function name="phpgw:label">
	<func:result>
		<xsl:if test="title">
			<label for="{phpgw:or(id,generate-id())}">
				<xsl:if test="tooltip">
					<xsl:attribute name="title">
						<xsl:value-of select="tooltip"/>
					</xsl:attribute>
					
					<xsl:attribute name="style">
						<xsl:text>cursor:help</xsl:text>						
					</xsl:attribute>
				</xsl:if>			
				
				<xsl:attribute name="class">
					<xsl:if test="required">				
							<xsl:text>required </xsl:text>											
					</xsl:if>
					<xsl:if test="error">				
							<xsl:text>error </xsl:text>											
					</xsl:if>					
				</xsl:attribute>

				
				<xsl:choose>
					<xsl:when test="accesskey and contains(title, accesskey)">
						<xsl:value-of select="substring-before (title, accesskey)"/>
						<span class="accesskey">
							<xsl:value-of select="accesskey"/>
						</span>
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
	<form id="test-form" action="{action}">
		<xsl:attribute name="class">
			<xsl:if test="tabbed">
				<xsl:text>tabbed </xsl:text>
			</xsl:if>	
			<xsl:text>yui-skin-sam</xsl:text>
		</xsl:attribute>		
		
		<h2><xsl:value-of select="title"/></h2>	
		
		<p>
			* Detonates required field
		</p>	
		
		<div id="form-content">
			<xsl:apply-templates select="fieldset" />
			<xsl:apply-templates select="field | textarea" />
			<xsl:apply-templates select="bottom_toolbar" />
		</div>
		
		<p>
			<input type="submit" value="Save" />
			<input type="submit" value="Apply" />
			<input type="submit" value="Cancel" />
		</p>
		
		<div id="calendar"></div>
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
		<xsl:if test="error">error </xsl:if>		
		<xsl:if test="readonly">readonly </xsl:if>		
		<xsl:if test="disabled">disabled </xsl:if>		
		<xsl:if test="type='date'">date </xsl:if>
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
</xsl:attribute-set>

<xsl:template name="field" match="field">   	
	<xsl:copy-of select="phpgw:label()"/>
	
	<xsl:choose>	
		<xsl:when test="type='textarea'">
			<xsl:call-template name="textarea"/>
		</xsl:when>
		<xsl:when test="type='date'">
			<xsl:call-template name="date"/>
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
	<input xsl:use-attribute-sets="input-attributes core-attributes" value="{value}" type="{phpgw:or(type,'text')}">		
		<xsl:if test="accesskey">
			<xsl:attribute name="accesskey">
				<xsl:value-of select="accesskey"/>
			</xsl:attribute>
		</xsl:if>				
		
		<xsl:if test="maxlength">
			<xsl:attribute name="maxlength">
				<xsl:value-of select="maxlength"/>
			</xsl:attribute>
		</xsl:if>	
		
		<xsl:if test="disabled">
			<xsl:attribute name="disabled">disabled</xsl:attribute>
		</xsl:if>
		
		<xsl:if test="readonly">
			<xsl:attribute name="readonly">readonly</xsl:attribute>
		</xsl:if>		
	</input>
</xsl:template>

<xsl:template name="textarea">			
	<textarea xsl:use-attribute-sets="input-attributes core-attributes" cols="{phpgw:or(cols,20)}" rows="{phpgw:or(rows,3)}">
		<xsl:if test="accesskey">
			<xsl:attribute name="accesskey">
				<xsl:value-of select="accesskey"/>
			</xsl:attribute>
		</xsl:if>	
		<xsl:value-of select="value"/>
	</textarea>
</xsl:template>  

<xsl:template name="date">
	<input xsl:use-attribute-sets="input-attributes core-attributes" value="{value}" type="text">
		<xsl:if test="accesskey">
			<xsl:attribute name="accesskey">
				<xsl:value-of select="accesskey"/>
			</xsl:attribute>
		</xsl:if>	
	</input>
</xsl:template>
