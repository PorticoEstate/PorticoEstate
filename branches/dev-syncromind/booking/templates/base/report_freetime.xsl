<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--div id="content"-->

		<xsl:call-template name="msgbox"/>
		<!--xsl:call-template name="yui_booking_i18n"/-->

		<xsl:choose>
			<xsl:when test="show = 'gui'">
				<!--dl class="form">
					<dt class="heading">
						<xsl:value-of select="php:function('lang', 'Free time')" />
					</dt>
				</dl-->

				<form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form">
                                    <input type="hidden" name="tab" value=""/>
                                    <div id="tab-content">
                                        <xsl:value-of disable-output-escaping="yes" select="data/tabs"/>
                                        <div id="report_freetime">
                                            <div class="pure-control-group">
                                                    <!--label><xsl:value-of select="php:function('lang', 'From')" /></label>
                                                        <div class="date-picker">
                                                                <input id="field_from" name="from" type="text">
                                                                        <xsl:attribute name="value"><xsl:value-of select="from"/></xsl:attribute>
                                                                </input>
                                                        </div-->
                                                        <label>
                                                            <xsl:value-of select="php:function('lang', 'From')" />
                                                        </label>
                                                        <input class="datetime" id="start_date" name="start_date" type="text">
                                                            <xsl:attribute name="value"><xsl:value-of select="from"/></xsl:attribute>
                                                        </input>
                                            </div>
                                            <div class="pure-control-group">
                                                    <!--label><xsl:value-of select="php:function('lang', 'To')" /></label>
                                                            <div class="date-picker">
                                                                    <input id="field_to" name="to" type="text">
                                                                            <xsl:attribute name="value"><xsl:value-of select="to"/></xsl:attribute>
                                                                    </input>
                                                            </div-->
                                                        <label>
                                                            <xsl:value-of select="php:function('lang', 'To')" />
                                                        </label>
                                                        <input class="datetime" id="end_date" name="end_date" type="text">
                                                            <xsl:attribute name="value"><xsl:value-of select="to"/></xsl:attribute>
                                                        </input>

                                            </div>
                                            <div class="clr" />
                                            <div class="pure-control-group">
                                                    <label><xsl:value-of select="php:function('lang', 'buildings')"/></label>
                                                            <select id="field_building" name="building[]" size="10" multiple="multiple" class="full-width">
                                                                    <xsl:for-each select="buildings">
                                                                            <xsl:sort select="name"/>
                                                                            <option>
                                                                                    <xsl:if test="../building = id">
                                                                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                                                                    </xsl:if>
                                                                                    <xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
                                                                                    <xsl:value-of select="name"/>
                                                                            </option>
                                                                    </xsl:for-each>
                                                            </select>
                                            </div>
                                            <div class="pure-control-group">
                                                    <dt><label for="field_weekday"><xsl:value-of select="php:function('lang', 'Weekdays')" /></label></dt>
                                                    <dd>
                                                            <label><input type="checkbox" value="1" name="weekdays[]" /> <xsl:value-of select="php:function('lang', 'Monday')" /></label><br />
                                                            <label><input type="checkbox" value="2" name="weekdays[]" /> <xsl:value-of select="php:function('lang', 'Tuesday')" /></label><br />
                                                            <label><input type="checkbox" value="3" name="weekdays[]" /> <xsl:value-of select="php:function('lang', 'Wednesday')" /></label><br />
                                                            <label><input type="checkbox" value="4" name="weekdays[]" /> <xsl:value-of select="php:function('lang', 'Thursday')" /></label><br />
                                                            <label><input type="checkbox" value="5" name="weekdays[]" /> <xsl:value-of select="php:function('lang', 'Friday')" /></label><br />
                                                            <label><input type="checkbox" value="6" name="weekdays[]" /> <xsl:value-of select="php:function('lang', 'Saturday')" /></label><br />
                                                            <label><input type="checkbox" value="0" name="weekdays[]" /> <xsl:value-of select="php:function('lang', 'Sunday')" /></label><br />
                                                    </dd>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-buttons">
                                        <input type="submit" class="pure-button pure-button-primary">
                                                <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create report')"/></xsl:attribute>
                                        </input>
                                    </div>
				</form>
			</xsl:when>
			<xsl:otherwise>
				<dl class="form">
					<dt class="heading">
						<xsl:value-of select="php:function('lang', 'Free time')" />
					</dt>
				</dl>

				<script type="text/javascript">
					var eventParams = {};
				</script>
				<table id="report">
					<thead>
						<tr>
							<th><xsl:value-of select="php:function('lang', 'Building')"/></th>
							<th><xsl:value-of select="php:function('lang', 'Resource')"/></th>
							<th><xsl:value-of select="php:function('lang', 'From')"/></th>
							<th><xsl:value-of select="php:function('lang', 'To')"/></th>
							<th><xsl:value-of select="php:function('lang', 'actions')"/></th>
						</tr>
					</thead>
					<tbody>
						<xsl:for-each select="allocations">
							<tr>
								<td><xsl:value-of select="building_name"/></td>
								<td><xsl:value-of select="resource_name"/></td>
								<td><xsl:value-of select="php:function('pretty_timestamp', from_)"/></td>
								<td><xsl:value-of select="php:function('pretty_timestamp', to_)"/></td>
								<td>
									<script type="text/javascript">
										eventParams[<xsl:value-of select="counter"/>] = <xsl:value-of select="event_params"/>;
									</script>
									<a href="#" onclick="YAHOO.booking.postToUrl('index.php?menuaction=booking.uievent.add', eventParams[{counter}]);">Lag arrangement</a>
								</td>
							</tr>
						</xsl:for-each>
					</tbody>
				</table>
			</xsl:otherwise>
		</xsl:choose>
	<!--/div-->
</xsl:template>
