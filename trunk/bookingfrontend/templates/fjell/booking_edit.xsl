<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

	<dl class="form">
    	<dt class="heading"><xsl:value-of select="php:function('lang', 'Booking')"/> #<xsl:value-of select="booking/id"/></dt>
	</dl>
    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

    <form action="" method="POST">
		<input type="hidden" name="season_id" value="{booking/season_id}"/>
		<input type="hidden" name="allocation_id" value="{booking/allocation_id}"/>
		<input type="hidden" name="step" value="1"/>
        <dl class="form-col">
            <dt><label for="field_active"><xsl:value-of select="php:function('lang', 'Active')"/></label></dt>
            <dd>
                <select id="field_active" name="active">
                    <option value="1">
                    	<xsl:if test="booking/active=1">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="php:function('lang', 'Active')"/>
                    </option>
                    <option value="0">
                    	<xsl:if test="booking/active=0">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="php:function('lang', 'Inactive')"/>
                    </option>
                </select>
            </dd>
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
            <dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')"/></label></dt>
            <dd>
                <input id="field_building_id" name="building_id" type="hidden" value="{booking/building_id}"/>
	            <xsl:value-of select="booking/building_name"/>
            </dd>
            <dt><label for="field_resources"><xsl:value-of select="php:function('lang', 'Resources')"/></label></dt>
            <dd>
                <div id="resources_container"><xsl:value-of select="php:function('lang', 'Select a building first')"/></div>
            </dd>
        </dl>
        <dl class="form-col">
            <dt style="margin-top: 100px;"><label for="field_org"><xsl:value-of select="php:function('lang', 'Organization')"/></label></dt>
            <dd>
	            <xsl:value-of select="booking/organization_name"/>
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
            <dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')"/></label></dt>
            <dd>
                <div class="datetime-picker">
                <input id="field_from" name="from_" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="booking/from_"/></xsl:attribute>
                </input>
                </div>
            </dd>
            <dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')"/></label></dt>
            <dd>
                <div class="datetime-picker">
                <input id="field_to" name="to_" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="booking/to_"/></xsl:attribute>
                </input>
                </div>
            </dd>
			<dt><label for="field_repeat_until"><xsl:value-of select="php:function('lang', 'Recurring booking')" /></label></dt>
			<dd>
				<label>
					<input type="checkbox" name="outseason" id="outseason">
						<xsl:if test="outseason='on'">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
					<xsl:value-of select="php:function('lang', 'Out season')" />
				</label>
			</dd>
			<dd>
				<label>
					<input type="checkbox" name="recurring" id="recurring">
						<xsl:if test="recurring='on'">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
					<xsl:value-of select="php:function('lang', 'Repeat until')" />
				</label>
			</dd>
			<dd class="date-picker">
				<input id="field_repeat_until" name="repeat_until" type="text">
					<xsl:attribute name="value"><xsl:value-of select="repeat_until"/></xsl:attribute>
				</input>
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
            <input type="submit">
			<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
			</input>
            <a class="cancel" href="" onclick="history.back(1); return false">
                <xsl:value-of select="php:function('lang', 'Go back')"/>
            </a>
        </div>
    </form>
    </div>
    <script type="text/javascript">
        YAHOO.booking.initialSelection = <xsl:value-of select="booking/resources_json"/>;
    </script>
</xsl:template>
