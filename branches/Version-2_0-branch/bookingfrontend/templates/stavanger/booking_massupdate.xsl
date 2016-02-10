<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

		<dl class="form">
			<dt class="heading"><xsl:value-of select="php:function('lang', 'Mass update')"/></dt>
		</dl>
		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>
		<xsl:choose>
			<xsl:when test="step = 2">
				<p>
					<xsl:value-of select="php:function('lang', '%1 bookings will be updated.', count(booking/results))" />
				</p>
				<form action="" method="POST">
					<input type="hidden" name="season_id" value="{booking/season_id}"/>
					<input type="hidden" name="step" value="{step}"/>
					<input type="hidden" name="group_id" value="{group_id}"/>
					<input type="hidden" name="activity_id" value="{activity_id}"/>
					<select size="10">
						<xsl:for-each select="booking/results">
							<option>
								<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
								<xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>
							</option>
						</xsl:for-each>
					</select>
					<xsl:if test="count(booking/results) &gt; 0">
						<div class="form-buttons">
							<input type="submit" style="float: right;">
							<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Update')"/></xsl:attribute>
							</input>
						</div>
					</xsl:if>
				</form>
			</xsl:when>
			<xsl:when test="step = 3">
				<p>
					<xsl:value-of select="php:function('lang', '%1 bookings were updated.', update_count)" />
				</p>
			</xsl:when>
			<xsl:otherwise>
				<p>
					<xsl:value-of select="php:function('lang', 'You are now about to update all bookings from this date and to the end of the season.')" />&nbsp;
					<xsl:value-of select="php:function('lang', 'Please update the data and click the Next-button.')" />&nbsp;
					<xsl:value-of select="php:function('lang', 'When clicking the Next-button you will be presented to a list of bookings that will be updated.')" />
				</p>
				<form action="" method="POST">
					<input type="hidden" name="season_id" value="{booking/season_id}"/>
					<input type="hidden" name="allocation_id" value="{booking/allocation_id}"/>
					<input type="hidden" name="step" value="1"/>
					<dl class="form-col">
						<dt><label for="field_activity"><xsl:value-of select="php:function('lang', 'Activity')" /></label></dt>
						<dd>
							<select name="activity_id" id="field_activity">
								<option value=""><xsl:value-of select="php:function('lang', '-- select an activity --')" /></option>
								<xsl:for-each select="activities">
									<option>
										<xsl:if test="../booking/activity_id = id">
											<xsl:attribute name="selected">selected</xsl:attribute>
										</xsl:if>
										<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
										<xsl:value-of select="name"/>
									</option>
								</xsl:for-each>
							</select>
						</dd>
						<dt><label for="field_group"><xsl:value-of select="php:function('lang', 'Group')"/></label></dt>
						<dd>
							<select name="group_id">
									<option value=""><xsl:value-of select="php:function('lang', 'Select a group')"/></option>
								<xsl:for-each select="groups">
									<option value="{id}">
										<xsl:if test="../booking/group_id = id">
											<xsl:attribute name="selected">selected</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="name"/>
									</option>
								</xsl:for-each>
							</select>
						</dd>
					</dl>
					<dl class="form-col">
						<dt><label for="field_from"><xsl:value-of select="php:function('lang', 'Target audience')" /></label></dt>
						<dd>
							<ul>
								<xsl:for-each select="audience">
									<li>
										<input type="checkbox" name="audience[]">
											<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
											<xsl:if test="../booking/audience=id">
												<xsl:attribute name="checked">checked</xsl:attribute>
											</xsl:if>
										</input>
										<label><xsl:value-of select="name"/></label>
									</li>
								</xsl:for-each>
							</ul>
						</dd>
					</dl>
					<dl class="form-col">
						<dt><label for="field_from"><xsl:value-of select="php:function('lang', 'Number of participants')" /></label></dt>
						<dd>
							<table id="agegroup">
								<tr><th/><th><xsl:value-of select="php:function('lang', 'Male')" /></th>
									<th><xsl:value-of select="php:function('lang', 'Female')" /></th></tr>
								<xsl:for-each select="agegroups">
									<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
									<tr>
										<th><xsl:value-of select="name"/></th>
										<td>
											<input type="text">
												<xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
												<xsl:attribute name="value"><xsl:value-of select="../booking/agegroups/male[../agegroup_id = $id]"/></xsl:attribute>
											</input>
										</td>
										<td>
											<input type="text">
												<xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
												<xsl:attribute name="value"><xsl:value-of select="../booking/agegroups/female[../agegroup_id = $id]"/></xsl:attribute>
											</input>
										</td>
									</tr>
								</xsl:for-each>
							</table>
						</dd>

					</dl>
					<div class="form-buttons">
						<input type="submit" style="float: right;">
						<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Next')"/></xsl:attribute>
						</input>
					</div>
				</form>
			</xsl:otherwise>
		</xsl:choose>
    </div>
    <script type="text/javascript">
		YAHOO.util.Dom.setStyle(('header'), 'display', 'none');
    </script>
</xsl:template>
