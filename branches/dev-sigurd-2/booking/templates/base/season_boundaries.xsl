<xsl:template match="data">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="season/buildings_link"/></xsl:attribute>
                    Buildings
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="season/building_link"/></xsl:attribute>
                    <xsl:value-of select="season/building_name"/>
                </a>
            </li>
            <li>Seasons</li>
            <li>
				<a>
                    <xsl:attribute name="href"><xsl:value-of select="season/season_link"/></xsl:attribute>
					<xsl:value-of select="season/name"/>
				</a>
			</li>
            <li>Boundaries</li>
        </ul>

        <xsl:call-template name="msgbox"/>

		<table>
			<thead>
				<tr><th>Weekday</th><th>From</th><th>To</th></tr>
			</thead>
			<xsl:for-each select="boundaries">
				<tr>
					<td><xsl:value-of select="wday"/></td>
					<td><xsl:value-of select="from_"/></td>
					<td><xsl:value-of select="to_"/></td>
				</tr>
			</xsl:for-each>
			<tbody>
				
			</tbody>
		</table>
		<form action="" method="POST">
		<dl class="form">
			<dt><label>Season boundaries</label></dt>
			<dd>
			</dd>
			<dt><label for="field_status">Week day</label></dt>
			<dd>
				<select name="wday">
					<option value="1">Monday</option>
					<option value="2">Tueday</option>
					<option value="3">Wednesday</option>
					<option value="4">Thursday</option>
					<option value="5">Friday</option>
					<option value="6">Saturday</option>
					<option value="7">Sunday</option>
				</select>
			</dd>
			<dt><label>From</label></dt>
			<dd>
				<div class="time-picker">
					<input id="field_from" name="from_" type="text">
                    	<xsl:attribute name="value"><xsl:value-of select="boundary/from_"/></xsl:attribute>
					</input>
				</div>
			</dd>
			<dt><label>To</label></dt>
			<dd>
				<div class="time-picker">
					<input id="field_to" name="to_" type="text">
                    	<xsl:attribute name="value"><xsl:value-of select="boundary/to_"/></xsl:attribute>
					</input>
				</div>
			</dd>
		</dl>
		<div class="form-buttons">
			<input type="submit" value="Add"/>
			<a class="cancel">
				<xsl:attribute name="href"><xsl:value-of select="season/cancel_link"/></xsl:attribute>
				Cancel
			</a>
		</div>
	</form>
	</div>
</xsl:template>
