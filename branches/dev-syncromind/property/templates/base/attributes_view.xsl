  <!-- $Id$ -->
	<xsl:template match="attributes_view">
		<xsl:variable name="lang_attribute_statustext">
			<xsl:value-of select="lang_attribute_statustext"/>
		</xsl:variable>
		<tr>
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
			<td align="left" valign="top">
				<label>
				<xsl:value-of select="input_text"/>
				<xsl:choose>
					<xsl:when test="datatype='D'">
						<xsl:text>[</xsl:text>
						<xsl:value-of select="//lang_dateformat"/>
						<xsl:text>]</xsl:text>
					</xsl:when>
				</xsl:choose>
				</label>
			</td>
			<td align="left">
				<xsl:choose>
					<xsl:when test="name!=''">
						<xsl:choose>
							<xsl:when test="datatype='R'">
								<xsl:call-template name="choice_view"/>
							</xsl:when>
							<xsl:when test="datatype='CH'">
								<xsl:call-template name="choice_view"/>
							</xsl:when>
							<xsl:when test="datatype='LB'">
								<xsl:for-each select="choice[checked='checked']">
									<xsl:value-of select="value"/>
									<xsl:if test="position() != last()">, </xsl:if>
								</xsl:for-each>
							</xsl:when>
							<xsl:when test="datatype='AB'">
								<input type="text" value="{value}" readonly="readonly" size="5" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="statustext"/>
									</xsl:attribute>
								</input>
								<input size="30" type="text" value="{contact_name}" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="statustext"/>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:when test="datatype='VENDOR'">
								<input type="text" value="{value}" readonly="readonly" size="6" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="statustext"/>
									</xsl:attribute>
								</input>
								<input size="30" type="text" value="{vendor_name}" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="statustext"/>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:when test="datatype='D'">
								<xsl:value-of select="value"/>
								<img id="values_attribute_{counter}-trigger"/>
							</xsl:when>
							<xsl:when test="datatype='link'">
								<xsl:choose>
									<xsl:when test="value!=''">
										<br/>
										<a href="{value}" target="_blank">
											<xsl:value-of select="value"/>
										</a>
									</xsl:when>
								</xsl:choose>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="value"/>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="history=1">
								<xsl:variable name="link_history">
									<xsl:value-of select="link_history"/>
								</xsl:variable>
								<xsl:variable name="lang_history_help">
									<xsl:value-of select="//lang_history_help"/>
								</xsl:variable>
								<xsl:variable name="lang_history">
									<xsl:value-of select="//lang_history"/>
								</xsl:variable>
								<a href="javascript:var w=window.open('{$link_history}','','left=50,top=100,width=550,height=400,scrollbars')" onMouseOver="overlib('{$lang_history_help}', CAPTION, '{$lang_history}')" onMouseOut="nd()">
									<xsl:value-of select="//lang_history"/>
								</a>
							</xsl:when>
						</xsl:choose>
					</xsl:when>
				</xsl:choose>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template name="choice_view">
		<xsl:variable name="counter">
			<xsl:value-of select="counter"/>
		</xsl:variable>
		<table cellpadding="2" cellspacing="2" width="50%" align="left">
			<xsl:for-each select="choice">
				<tr>
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
					<td align="left">
						<xsl:value-of select="value"/>
						<xsl:text> </xsl:text>
					</td>
					<xsl:variable name="checked">
						<xsl:value-of select="checked"/>
					</xsl:variable>
					<td align="left">
						<xsl:choose>
							<xsl:when test="checked='checked'">
								<input type="{input_type}" name="values_attribute[{$counter}][value][]" value="{id}" checked="$checked" disabled="disabled"/>
							</xsl:when>
							<xsl:otherwise>
								<input type="{input_type}" name="values_attribute[{$counter}][value][]" value="{id}" disabled="disabled"/>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>
