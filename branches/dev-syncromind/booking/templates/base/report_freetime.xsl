<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style type="text/css">
		#field_weekday label{margin:0px;text.align:left;width:auto;}
	</style>
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="data/tabs"/>
			<div id="report_freetime">
				<xsl:choose>
					<xsl:when test="show = 'gui'">
						<div class="pure-control-group">
							<!--label><xsl:value-of select="php:function('lang', 'From')" /></label>
							<div class="date-picker">
								<input id="field_from" name="from" type="text">
									<xsl:attribute name="value"><xsl:value-of select="from"/></xsl:attribute>
								</input>
							</div-->
							<label for="start_date">
								<h4>
									<xsl:value-of select="php:function('lang', 'From')" />
								</h4>
							</label>
							<input class="datetime" id="from" name="from" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="from"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<!--label><xsl:value-of select="php:function('lang', 'To')" /></label>
							<div class="date-picker">
								<input id="field_to" name="to" type="text">
									<xsl:attribute name="value"><xsl:value-of select="to"/></xsl:attribute>
								</input>
							</div-->
							<label for="end_date">
								<h4>
									<xsl:value-of select="php:function('lang', 'To')" />
								</h4>
							</label>
							<input class="datetime" id="to" name="to" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="to"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label for="field_building" style="vertical-align:top;">
								<h4>
									<xsl:value-of select="php:function('lang', 'buildings')"/>
								</h4>
							</label>
							<select id="field_building" name="building[]" size="10" multiple="multiple" class="full-width">
								<xsl:for-each select="buildings">
									<xsl:sort select="name"/>
									<option>
										<xsl:if test="../building = id">
											<xsl:attribute name="selected">selected</xsl:attribute>
										</xsl:if>
										<xsl:attribute name="value">
											<xsl:value-of select="id"/>
										</xsl:attribute>
										<xsl:value-of select="name"/>
									</option>
								</xsl:for-each>
							</select>
						</div>
						<div class="pure-control-group">
							<label for="field_weekday" style="vertical-align:top;">
								<h4>
									<xsl:value-of select="php:function('lang', 'Weekdays')" />
								</h4>
							</label>
							<ul id="field_weekday" style="display:inline-block;list-style:none;padding:0px;margin:0px;">
								<li>
									<label>
										<input type="checkbox" value="1" name="weekdays[]" />
										<xsl:value-of select="php:function('lang', 'Monday')" />
									</label>
								</li>
								<li>
									<label>
										<input type="checkbox" value="2" name="weekdays[]" />
										<xsl:value-of select="php:function('lang', 'Tuesday')" />
									</label>
								</li>
								<li>
									<label>
										<input type="checkbox" value="3" name="weekdays[]" />
										<xsl:value-of select="php:function('lang', 'Wednesday')" />
									</label>
								</li>
								<li>
									<label>
										<input type="checkbox" value="4" name="weekdays[]" />
										<xsl:value-of select="php:function('lang', 'Thursday')" />
									</label>
								</li>
								<li>
									<label>
										<input type="checkbox" value="5" name="weekdays[]" />
										<xsl:value-of select="php:function('lang', 'Friday')" />
									</label>
								</li>
								<li>
									<label>
										<input type="checkbox" value="6" name="weekdays[]" />
										<xsl:value-of select="php:function('lang', 'Saturday')" />
									</label>
								</li>
								<li>
									<label>
										<input type="checkbox" value="0" name="weekdays[]" />
										<xsl:value-of select="php:function('lang', 'Sunday')" />
									</label>
								</li>
							</ul>
						</div>									
					</xsl:when>
					<xsl:otherwise>
						<script type="text/javascript">
							var eventParams = {};
						</script>
						<table id="report" class="pure-table pure-table-bordered">
							<thead>
								<tr>
									<th>
										<xsl:value-of select="php:function('lang', 'Building')"/>
									</th>
									<th>
										<xsl:value-of select="php:function('lang', 'Resource')"/>
									</th>
									<th>
										<xsl:value-of select="php:function('lang', 'From')"/>
									</th>
									<th>
										<xsl:value-of select="php:function('lang', 'To')"/>
									</th>
									<th>
										<xsl:value-of select="php:function('lang', 'actions')"/>
									</th>
								</tr>
							</thead>
							<tbody>
								<xsl:for-each select="allocations">
									<tr>
										<td>
											<xsl:value-of select="building_name"/>
										</td>
										<td>
											<xsl:value-of select="resource_name"/>
										</td>
										<td>
											<xsl:value-of select="php:function('pretty_timestamp', from_)"/>
										</td>
										<td>
											<xsl:value-of select="php:function('pretty_timestamp', to_)"/>
										</td>
										<td>
											<script type="text/javascript">
												eventParams[<xsl:value-of select="counter"/>] = <xsl:value-of select="event_params"/>;
												var eventaddURL = phpGWLink('index.php', {menuaction:'menuaction=booking.uievent.add'});
											</script>
											<a href="#" onclick="JqueryPortico.booking.postToUrl(eventaddURL, eventParams[{counter}]);">Lag arrangement</a>
										</td>
									</tr>
								</xsl:for-each>
							</tbody>
						</table>
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</div>
		<div class="form-buttons">
			<xsl:if test="show = 'gui'">
				<input type="submit" class="pure-button pure-button-primary">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Create report')"/>
					</xsl:attribute>
				</input>
			</xsl:if>
			<xsl:if test="show != 'gui'">
				<input type="button" class="pure-button pure-button-primary" name="cancel">
					<xsl:attribute name="onclick">window.location="<xsl:value-of select="allocations/cancel_link"/>"</xsl:attribute>
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Cancel')" />
					</xsl:attribute>
				</input>
			</xsl:if>
		</div>
	</form>
</xsl:template>
