<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

	<dl class="form">
    	<dt class="heading"><xsl:value-of select="php:function('lang', 'New Booking')"/></dt>
	</dl>
    <xsl:call-template name="msgbox"/>

    <form action="" method="POST">
        <dl class="form-col">
            <dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Name')"/></label></dt>
            <dd>
                <input id="field_name" name="name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="booking/name"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')"/></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_building_id" name="building_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="booking/building_id"/></xsl:attribute>
                    </input>
                    <input id="field_building_name" name="building_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="booking/building_name"/></xsl:attribute>
                    </input>
                    <div id="building_container"/>
                </div>
            </dd>
            <dt><label for="field_season"><xsl:value-of select="php:function('lang', 'Season')"/></label></dt>
            <dd>
                <div id="season_container"><xsl:value-of select="php:function('lang', 'Select a building first')"/></div>
            </dd>
            <dt><label for="field_resources"><xsl:value-of select="php:function('lang', 'Resources')"/></label></dt>
            <dd>
                <div id="resources_container"><xsl:value-of select="php:function('lang', 'Select a building first')"/></div>
            </dd>
        </dl>
        <dl class="form-col">
            <dt><label for="field_group"><xsl:value-of select="php:function('lang', 'Group')"/></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_group_id" name="group_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="booking/group_id"/></xsl:attribute>
                    </input>
                    <input id="field_group_name" name="group_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="booking/group_name"/></xsl:attribute>
                    </input>
                    <div id="group_container"/>
                </div>
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
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')"/></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="booking/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')"/>
            </a>
        </div>
    </form>
    </div>
    <script type="text/javascript">
        YAHOO.booking.season_id = '<xsl:value-of select="booking/season_id"/>';
        YAHOO.booking.initialSelection = <xsl:value-of select="booking/resources_json"/>;
    </script>
</xsl:template>
