<!-- $Id$ -->

<!--A widget is something like an input box, image etc, or a composite/virtual
widget like a "seperator" or a "label".  These are used throughout the filemanager
as a cunning way of avoiding putting any HTML in the app
	 
NB:This means that someone clever could write an XSLT that converted to, say,
Mozilla XUL, or QT's XML-UI, or a GTK glade interface etc (I dare you!) -->

	<xsl:template match="widget">
		<xsl:variable name="type"><xsl:value-of select="type"/></xsl:variable>
		<xsl:choose>
			<xsl:when test='$type="select"'>
				<select>
					<xsl:attribute name="name"><xsl:value-of select="name"/></xsl:attribute>
					<xsl:if test="id">
						<xsl:attribute name="id"><xsl:value-of select="id"/></xsl:attribute>
					</xsl:if>
					<xsl:if test="onChange">
						<xsl:attribute name="onChange"><xsl:value-of disable-output-escaping="yes" select="onChange"/></xsl:attribute>
					</xsl:if>
					<xsl:if test="disabled">
						<xsl:attribute name="disabled"><xsl:text>disabled</xsl:text></xsl:attribute>
					</xsl:if>
					<xsl:value-of select="caption"/>
					<xsl:for-each select="options/option">
						<option>
							<xsl:attribute name="value"><xsl:value-of select="value"/></xsl:attribute>
							<xsl:choose>
								<xsl:when test="selected">
									<xsl:variable name="selected" select="selected"/>
									<xsl:if test="$selected=1">
										<xsl:attribute name="selected"><xsl:text>selected</xsl:text></xsl:attribute>	
									</xsl:if>
								</xsl:when>
								<xsl:when test="disabled">
									<xsl:attribute name="disabled"><xsl:text>disabled</xsl:text></xsl:attribute>
								</xsl:when>
							</xsl:choose>
							<xsl:value-of select="caption"/>
						</option>
					</xsl:for-each>
				</select>
			</xsl:when>
			<xsl:when test='$type="seperator"'>
				<br />
			</xsl:when>
			<xsl:when test='$type="empty"'>
				&nbsp;
			</xsl:when>
			<xsl:when test='$type="image"'>
				<xsl:choose> 
					<xsl:when test="name">
						<xsl:variable name="name" select="name"/>
						<xsl:variable name="src" select="src"/>
						<xsl:variable name="title" select="title"/>
						<xsl:variable name="value" select="value"/>
						<input type="{$type}" name="{$name}" value="{$value}" src="{$src}" title="{$title}" alt="{$title}" border="0" />
					</xsl:when>
					<xsl:when test="link"> 
						<xsl:variable name="link"><xsl:value-of select="link"/></xsl:variable>
						<a class="none">
							<xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute>
							<xsl:call-template name="img" />
						</a>
					</xsl:when>	
					<xsl:otherwise>
						<xsl:call-template name="img" />
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:when test='$type="label"'>
				<xsl:value-of select="caption" />
			</xsl:when>
			<xsl:when test='$type="link"'>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="href"/></xsl:attribute>
					<xsl:if test="onClick != ''">
						<xsl:attribute name="onClick"><xsl:value-of disable-output-escaping="yes" select="onClick"/></xsl:attribute>
					</xsl:if>
					<xsl:value-of select="caption"/>
				</a>
			</xsl:when>
			<xsl:when test='$type="plain"'>
				<xsl:value-of select="caption"/>
			</xsl:when>
			<xsl:when test='$type="help"'>
				<a href="#">
					<xsl:attribute name="onClick"><xsl:value-of disable-output-escaping="yes" select="onClick"/></xsl:attribute>
					<font size="-2" color="maroon"><xsl:text>[?]</xsl:text></font>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test="caption_start">
					<xsl:value-of select="caption_start"/>&nbsp;
				</xsl:if>
				<input>
					<xsl:attribute name="type"><xsl:value-of select="type"/></xsl:attribute>
					<xsl:attribute name="name"><xsl:value-of select="name"/></xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="value"/></xsl:attribute>
					<xsl:if test="size">
						<xsl:attribute name="size"><xsl:value-of select="size"/></xsl:attribute>
					</xsl:if>
					<xsl:if test='$type="checkbox"'>
						<xsl:variable name="checked" select="checked"/>
						<xsl:if test="$checked=1">
							<xsl:attribute name="checked"><xsl:text>checked</xsl:text></xsl:attribute>
						</xsl:if>
						<xsl:if test="onClick != ''">
						<xsl:attribute name="onClick"><xsl:value-of disable-output-escaping="yes" select="onClick"/></xsl:attribute>
					</xsl:if>
					</xsl:if>
					&nbsp;<xsl:value-of select="caption"/>
				</input>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="img">
		<xsl:element name="img">
			<xsl:attribute name="src"><xsl:value-of select="src"/></xsl:attribute>
			<xsl:attribute name="alt"><xsl:value-of select="title"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:value-of select="title"/></xsl:attribute>
			<xsl:attribute name="border">0</xsl:attribute>
			<!--<xsl:attribute name="valign">center</xsl:attribute>-->
		</xsl:element>
	</xsl:template>

	<xsl:template match="form">
		<form>
			<xsl:attribute name="action"> <xsl:value-of select="action"/> </xsl:attribute>
			<xsl:attribute name="method"> <xsl:value-of select="method"/></xsl:attribute>
			<xsl:attribute name="enctype"> <xsl:value-of select="enctype"/></xsl:attribute>
			<table>
				<tr>
			<xsl:for-each select="members">
				<xsl:apply-templates />	
			</xsl:for-each>
				</tr>
			</table>
		</form>
	</xsl:template>

	<xsl:template match="table">
		<table border="0" cellpadding="2" cellspacing="2">
			<xsl:attribute name="class"><xsl:value-of select="class"/></xsl:attribute>
			<xsl:attribute name="width"><xsl:value-of select="width"/></xsl:attribute>
			<xsl:apply-templates select="table_head"/>
			<xsl:for-each select="table_row">
					<xsl:choose>
						<xsl:when test="position() mod 2 = 0">
							<xsl:call-template name="table_row">
								<xsl:with-param name="class"><xsl:text>row_on</xsl:text></xsl:with-param>
							</xsl:call-template>
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="table_row">
								<xsl:with-param name="class"><xsl:text>row_off</xsl:text></xsl:with-param>
							</xsl:call-template>
						</xsl:otherwise>
					</xsl:choose>
			</xsl:for-each>
			<xsl:apply-templates select="table_footer"/>
		</table>
	</xsl:template>

	<xsl:template match="tablediff">
		<table border="0" cellspacing="0">
			<xsl:attribute name="class"><xsl:value-of select="class"/></xsl:attribute>
			<xsl:attribute name="width"><xsl:value-of select="width"/></xsl:attribute>
			<xsl:apply-templates select="table_head"/>
			<xsl:for-each select="table_row">
				<xsl:call-template name="table_row">
				</xsl:call-template>
			</xsl:for-each>
			<xsl:apply-templates select="table_footer"/>
		</table>
	</xsl:template>

	<xsl:template match="table_head">
		<tr class="th">
			<xsl:apply-templates select="table_col"/>
		</tr>
	</xsl:template>

	<xsl:template name="table_row">
	<xsl:param name="class" select="text" />
		<tr class="{$class}">
			<!--<xsl:choose>
				<xsl:when test="alternate_row_color">
				<xsl:attribute name="class">
					<xsl:choose>
						<xsl:when test="@class">
							<xsl:value-of select="@class"/>
						</xsl:when>
						<xsl:when test="position() mod 2 = 0">
							<xsl:text>row_off</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>row_on</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				</xsl:when>
			</xsl:choose>-->
			<xsl:apply-templates select="table_col" />
		</tr>
	</xsl:template>

	<xsl:template match="table_col">
		<td>
			<xsl:if test="class">
				<xsl:attribute name="class"><xsl:value-of select="class"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="width">
				<xsl:attribute name="width"><xsl:value-of select="width"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="height">
				<xsl:attribute name="height"><xsl:value-of select="height"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="colspan">
				<xsl:attribute name="colspan"><xsl:value-of select="colspan"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="align">
				<xsl:attribute name="align"><xsl:value-of select="align"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="valign">
				<xsl:attribute name="valign"><xsl:value-of select="valign"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="style">
				<xsl:attribute name="style"><xsl:value-of select="style"/></xsl:attribute>
			</xsl:if>
			<xsl:apply-templates select="widget"/>
		</td>
	</xsl:template>

	<xsl:template match="table_footer">
		<tr>
			<xsl:apply-templates select="table_col"/>
		</tr>
	</xsl:template>
