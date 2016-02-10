<!-- $Id$ -->

<xsl:template name="form_elements">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template match="edit">
	<form method="post" action="{form_action}">
		<xsl:for-each select="form_elements//form_elm">
			<xsl:call-template name="form_elm" />
		</xsl:for-each>
	</form>
</xsl:template>

<xsl:template name="form_elm">
	<xsl:choose>
		<xsl:when test="type = 'button'">
			<xsl:call-template name="button" />
		</xsl:when>
		<xsl:when test="type = 'date'">
			<div><xsl:call-template name="date" /></div>
		</xsl:when>
		<xsl:when test="type = 'hidden'">
			<div><xsl:call-template name="hidden" /></div>
		</xsl:when>
		<xsl:when test="type = 'memo'">
			<div><xsl:call-template name="memo" /></div>
		</xsl:when>
		<xsl:when test="type = 'password'">
			<div><xsl:call-template name="password" /></div>
		</xsl:when>
		<xsl:when test="type = 'select'">
			<div><xsl:call-template name="select" /></div>
		</xsl:when>
		<xsl:when test="type = 'textbox'">
			<div><xsl:call-template name="textbox" /></div>
		</xsl:when>
		<xsl:when test="type = 'break'">
			<div><xsl:call-template name="break" /></div>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template name="button">
	<input type="submit" name="{id}" id="{id}" value="{value}" disable-output-escaping="yes" class="button" />
</xsl:template>

<xsl:template name="break">
	<br />
</xsl:template>

<xsl:template name="date">
	<div class="date_select">
		<label><xsl:value-of select="label" /></label>
		<input type="text" id="{id}">
			<xsl:if test="value">
				<xsl:attribute name="value"><xsl:value-of select="value" disable-output-escaping="yes" /></xsl:attribute>
			</xsl:if>
			<xsl:attribute name="name">
				<xsl:choose>
					<xsl:when test="name">
						<xsl:value-of select="name"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="id"/>
					</xsl:otherwise>
				</xsl:choose>	
			</xsl:attribute>
			<xsl:attribute name="class">
				<xsl:if test="class"><xsl:value-of select="class" /></xsl:if>
			</xsl:attribute>
			<xsl:if test="disabled">
				<xsl:attribute name="disabled"><xsl:value-of select="disabled" /></xsl:attribute>
			</xsl:if>
		</input>
		<img src="{img_trigger}" id="{id}-trigger" alt="{lang_trigger}" />
		<xsl:if test="help">
			<a href="#" title="{help}" class="help">?</a>
		</xsl:if>
	</div>
</xsl:template>

<xsl:template name="hidden">
	<input type="hidden" id="{id}" class="hidden">
		<xsl:if test="value">
			<xsl:attribute name="value"><xsl:value-of select="value" disable-output-escaping="yes" /></xsl:attribute>
		</xsl:if>
		<xsl:attribute name="name">
			<xsl:choose>
				<xsl:when test="name">
					<xsl:value-of select="name"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="id"/>
				</xsl:otherwise>
			</xsl:choose>	
		</xsl:attribute>
	</input>
</xsl:template>

<xsl:template name="memo">
	<label><xsl:value-of select="label" /></label>
	<textarea id="{id}">
		<xsl:if test="value">
			<xsl:attribute name="value"><xsl:value-of select="value" disable-output-escaping="yes" /></xsl:attribute>
		</xsl:if>
		<xsl:attribute name="name">
			<xsl:choose>
				<xsl:when test="name">
					<xsl:value-of select="name"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="id"/>
				</xsl:otherwise>
			</xsl:choose>	
		</xsl:attribute>
		<xsl:attribute name="cols">
			<xsl:choose>
				<xsl:when test="cols">
					<xsl:value-of select="cols"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>80</xsl:text>
				</xsl:otherwise>
			</xsl:choose>	
		</xsl:attribute>
		<xsl:attribute name="rows">
			<xsl:choose>
				<xsl:when test="rows">
					<xsl:value-of select="rows"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>10</xsl:text>
				</xsl:otherwise>
			</xsl:choose>	
		</xsl:attribute>
		<xsl:if test="class">
			<xsl:attribute name="class"><xsl:value-of select="class" /></xsl:attribute>
		</xsl:if>		
		<xsl:if test="disabled">
			<xsl:attribute name="disabled"><xsl:value-of select="disabled" /></xsl:attribute>
		</xsl:if>
	</textarea>
	<xsl:if test="help">
		<a href="#" title="{help}" class="help">?</a>
	</xsl:if>
</xsl:template>

<xsl:template name="password">
	<label><xsl:value-of select="label" /></label>
	<input type="password" id="{id}">
		<xsl:attribute name="name">
			<xsl:choose>
				<xsl:when test="name">
					<xsl:value-of select="name"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="id"/>
				</xsl:otherwise>
			</xsl:choose>	
		</xsl:attribute>
		<xsl:if test="class">
			<xsl:attribute name="class"><xsl:value-of select="class" /></xsl:attribute>
		</xsl:if>		
		<xsl:if test="disabled">
			<xsl:attribute name="disabled"><xsl:value-of select="disabled" /></xsl:attribute>
		</xsl:if>
	</input>
	<xsl:if test="help">
		<a href="#" title="{help}" class="help">?</a>
	</xsl:if>
</xsl:template>

<xsl:template name="select">
	<label><xsl:value-of select="label" /></label>
	<select id="{id}">
		<xsl:if test="multiple">
			<xsl:attribute name="multiple">multiple</xsl:attribute>
		</xsl:if>
		<xsl:attribute name="name">
			<xsl:choose>
				<xsl:when test="name">
					<xsl:value-of select="name"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="id"/>
				</xsl:otherwise>
			</xsl:choose>	
		</xsl:attribute>
		<xsl:if test="class">
			<xsl:attribute name="class"><xsl:value-of select="class" /></xsl:attribute>
		</xsl:if>
		<xsl:if test="options">
			<xsl:for-each select="options">
				<xsl:call-template name="select_options_list" />
			</xsl:for-each>
		</xsl:if>
	</select>
	<xsl:if test="help">
		<a href="#" title="{help}">?</a>
	</xsl:if>
</xsl:template>

<xsl:template name="select_options_list">
	<xsl:choose>
		<xsl:when test="selected">
			<option value="{id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="value" /></option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{id}"><xsl:value-of disable-output-escaping="yes" select="value" /></option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="textbox">
	<label><xsl:value-of select="label" /></label>
	<input type="text" id="{id}">
		<xsl:if test="value">
			<xsl:attribute name="value"><xsl:value-of select="value" disable-output-escaping="yes" /></xsl:attribute>
		</xsl:if>
		<xsl:attribute name="name">
			<xsl:choose>
				<xsl:when test="name">
					<xsl:value-of select="name"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="id"/>
				</xsl:otherwise>
			</xsl:choose>	
		</xsl:attribute>
		<xsl:if test="class">
			<xsl:attribute name="class"><xsl:value-of select="class" /></xsl:attribute>
		</xsl:if>		
		<xsl:if test="disabled">
			<xsl:attribute name="disabled"><xsl:value-of select="disabled" /></xsl:attribute>
		</xsl:if>
	</input>
	<xsl:if test="help">
		<a href="#" title="{help}" class="help">?</a>
	</xsl:if>
</xsl:template>
