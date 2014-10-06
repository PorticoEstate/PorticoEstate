<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

	<dl class="form">
    	<dt class="heading"><xsl:value-of select="php:function('lang', 'New allocation')"/></dt>
	</dl>
    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

    <form action="" method="POST">
		<input type="hidden" name="application_id" value="{allocation/application_id}"/>

        <dl class="form-col">

            <dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')" /></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_building_id" name="building_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="allocation/building_id"/></xsl:attribute>
                    </input>
                    <input id="field_building_name" name="building_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="allocation/building_name"/></xsl:attribute>
                    </input>
                    <div id="building_container"/>
                </div>
            </dd>

            <dt><label for="field_org"><xsl:value-of select="php:function('lang', 'Organization')" /></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_org_id" name="organization_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="allocation/organization_id"/></xsl:attribute>
                    </input>
                    <input id="field_org_name" name="organization_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="allocation/organization_name"/></xsl:attribute>
                    </input>
                    <div id="org_container"/>
                </div>
            </dd>

			<dt><label for="field_weekday"><xsl:value-of select="php:function('lang', 'Weekday')" /></label></dt>
			<dd>
				<select name="weekday" id="field_weekday">
					<option value="monday">
						<xsl:if test="../allocation/weekday = 'monday'">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', 'Monday')" />	
					</option>
					<option value="tuesday">
						<xsl:if test="weekday = 'tuesday'">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', 'Tuesday')" />
					</option>
					<option value="wednesday">
						<xsl:if test="weekday = 'wednesday'">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', 'Wednesday')" />
					</option>
					<option value="thursday">
						<xsl:if test="weekday = 'thursday'">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', 'Thursday')" />
					</option>
					<option value="friday">
						<xsl:if test="weekday = 'friday'">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', 'Friday')" />
					</option>
					<option value="saturday">
						<xsl:if test="weekday = 'saturday'">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', 'Saturday')" />
					</option>
					<option value="sunday">
						<xsl:if test="weekday = 'sunday'">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', 'Sunday')" />
					</option>

				</select>
			</dd>

            <dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
            <dd>
                <div class="time-picker">
                <input id="field_from" name="from_" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="allocation/from_"/></xsl:attribute>
                </input>
                </div>
            </dd>

            <dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')" /></label></dt>
            <dd>
                <div class="time-picker">
                <input id="field_to" name="to_" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="allocation/to_"/></xsl:attribute>
                </input>
                </div>
            </dd>
			<dt><label for="field_repeat_until"><xsl:value-of select="php:function('lang', 'Recurring allocation')" /></label></dt>
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
			<dt><xsl:value-of select="php:function('lang', 'Interval')" /></dt>
			<dd>
				<xsl:value-of select="../field_interval" />
				<select id="field_interval" name="field_interval">
					<option value="1">
						<xsl:if test="interval=1">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', '1 week')" />
					</option>
					<option value="2">
						<xsl:if test="interval=2">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', '2 weeks')" />
					</option>
					<option value="3">
						<xsl:if test="interval=3">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', '3 weeks')" />
					</option>
					<option value="4">
						<xsl:if test="interval=4">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="php:function('lang', '4 weeks')" />
					</option>
				</select>
			</dd>



        </dl>
        <dl class="form-col">

            <dt><label for="field_season"><xsl:value-of select="php:function('lang', 'Season')" /></label></dt>
            <dd>
                <div id="season_container"><xsl:value-of select="php:function('lang', 'Select a building first')" /></div>
            </dd>

            <dt><label for="field_cost"><xsl:value-of select="php:function('lang', 'Cost')" /></label></dt>
            <dd>
                <input id="field_cost" name="cost" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="allocation/cost"/></xsl:attribute>
                </input>
            </dd>

            <dt><label for="field_resources"><xsl:value-of select="php:function('lang', 'Resources')" /></label></dt>
            <dd>
                <div id="resources_container"><xsl:value-of select="php:function('lang', 'Select a building first')" /></div>
            </dd>


        </dl>
        <div class="form-buttons">
            <input type="submit">
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')"/></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="allocation/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    </div>
    <script type="text/javascript">
        YAHOO.booking.season_id = '<xsl:value-of select="allocation/season_id"/>';
        YAHOO.booking.initialSelection = <xsl:value-of select="allocation/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
    </script>
</xsl:template>
