<xsl:template match="view_ticket">
	<div class="tabsholder">
		<ul class="tabs">
			<li id="tab1"><a href="javascript:oTabs.display(1);"><span><xsl:value-of select="lang/summary"/></span></a></li>
			<li id="tab2"><a href="javascript:oTabs.display(2);"><span><xsl:value-of select="lang/notes" /></span></a></li>
			<li id="tab3"><a href="javascript:oTabs.display(3);"><span><xsl:value-of select="lang/history" /></span></a></li>
			<li id="tab4"><a href="javascript:oTabs.display(4);"><span><xsl:value-of select="lang/update" /></span></a></li>
		</ul><br />
	</div>
	
	<div class="msg">{messages}</div>

	<form method="post" action="{form_action}" enctype="multipart/form-data">

		<div id="tabcontent1">
			<xsl:for-each select="view">
				<div>
					<xsl:attribute name="class">
						<xsl:choose>
							<xsl:when test="position() mod 2 = 0">
								<xsl:text>row_off</xsl:text>
							</xsl:when>
							<xsl:otherwise>
								<xsl:text>row_on</xsl:text>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
					<span class="mock_label"><xsl:value-of select="label" /></span>
					<span class="mock_field"><xsl:value-of select="value" disable-output-escaping="yes" /></span><br />
				</div>
			</xsl:for-each>
		</div>

		<div id="tabcontent2">
			<xsl:choose>
				<xsl:when test="count(notes/*)">
					<xsl:for-each select="notes">
						<div class="tts_note">
								<xsl:attribute name="class">
									<xsl:choose>
										<xsl:when test="position() mod 2 = 0">
											<xsl:text>row_off</xsl:text>
										</xsl:when>
										<xsl:otherwise>
											<xsl:text>row_on</xsl:text>
										</xsl:otherwise>
									</xsl:choose>
								</xsl:attribute>
							<div>
								<xsl:value-of select="note_contents" disable-output-escaping="yes" />
							</div>
							<p class="tts_note_info"><xsl:value-of select="note_user" /> @ <xsl:value-of select="note_date" /></p>
						</div>
					</xsl:for-each>
				</xsl:when>
			</xsl:choose>
		</div>

		<div id="tabcontent3">
			<xsl:choose>
				<xsl:when test="count(history/*)">
					<table>
						<thead>
							<tr>
								<td><xsl:value-of select="lang/date" /></td>
								<td><xsl:value-of select="lang/user" /></td>
								<td><xsl:value-of select="lang/action" /></td>
								<td><xsl:value-of select="lang/old_value" /></td>
								<td><xsl:value-of select="lang/new_value" /></td>
							</tr>
						</thead>
						<tbody>
							<xsl:for-each select="history">
							<tr>
								<xsl:attribute name="class">
									<xsl:choose>
										<xsl:when test="position() mod 2 = 0">
											<xsl:text>row_off</xsl:text>
										</xsl:when>
										<xsl:otherwise>
											<xsl:text>row_on</xsl:text>
										</xsl:otherwise>
									</xsl:choose>
								</xsl:attribute>
								<td><xsl:value-of select="datetime" /></td>
								<td><xsl:value-of select="owner" /></td>
								<td><xsl:value-of select="action" /></td>
								<td><xsl:value-of select="old_value" /></td>
								<td><xsl:value-of select="new_value" /></td>
							</tr>
							</xsl:for-each>
						</tbody>
					</table>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="lang/no_history" />
				</xsl:otherwise>
			</xsl:choose>
		</div>

		<div id="tabcontent4">
			<xsl:choose>
				<xsl:when test="edit">
					<xsl:for-each select="form_elements//form_elm">
						<xsl:call-template name="form_elm" />
					</xsl:for-each>
					<!-- <xsl:apply-templates select="edit" /> -->
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="lang/no_rights" />
				</xsl:otherwise>
			</xsl:choose>
		</div>

		<div class="button_group">
			<input type="submit" id="cancel" name="cancel" onclick="self.location.href='{done_url}';" value="{lang/done}" class="button" />
			<xsl:choose>
				<xsl:when test="edit">
					<input type="submit" id="submit" name="submit" value="{lang/save}" class="button" />
				</xsl:when>
			</xsl:choose>
		</div>

	</form>

</xsl:template>

