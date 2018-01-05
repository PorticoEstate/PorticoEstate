<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style type="text/css">
		.time-picker {display: inline;}
	</style>
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="season/tabs"/>
			<div id="season_boundaries" class="booking-container">
				<table id="boundary-table" class="pure-table pure-table-bordered">
					<thead>
						<tr>
							<th>
								<xsl:value-of select="php:function('lang', 'Week day')" />
							</th>
							<th>
								<xsl:value-of select="php:function('lang', 'From')" />
							</th>
							<th>
								<xsl:value-of select="php:function('lang', 'To')" />
							</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<xsl:choose>
							<xsl:when test="count(boundaries/*) &gt; 0">
								<xsl:for-each select="boundaries">
									<tr>
										<td>
											<xsl:value-of select="wday_name"/>
										</td>
										<td>
											<xsl:value-of select="from_"/>
										</td>
										<td>
											<xsl:value-of select="to_"/>
										</td>
										<td>
											<xsl:if test="../season/permission/write">
												<a href="{delete_link}">
													<xsl:value-of select="php:function('lang', 'Delete')" />
												</a>
											</xsl:if>
										</td>
									</tr>
								</xsl:for-each>
							</xsl:when>
							<xsl:otherwise>
								<td colspan='4'>
									<xsl:value-of select="php:function('lang', 'No Data.')"/>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</tbody>
				</table>
				<xsl:if test="season/permission/write">
					<div class="pure-g">
						<div class="pure-u-1">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'Add Boundary')" />
									</h3>
								</legend>
							</div>
							<div class="pure-control-group">
								<label for="field_status">
									<xsl:value-of select="php:function('lang', 'Week day')" />
								</label>
								<select name="wday">
									<option value="1">
										<xsl:value-of select="php:function('lang', 'Monday')" />
									</option>
									<option value="2">
										<xsl:value-of select="php:function('lang', 'Tuesday')" />
									</option>
									<option value="3">
										<xsl:value-of select="php:function('lang', 'Wednesday')" />
									</option>
									<option value="4">
										<xsl:value-of select="php:function('lang', 'Thursday')" />
									</option>
									<option value="5">
										<xsl:value-of select="php:function('lang', 'Friday')" />
									</option>
									<option value="6">
										<xsl:value-of select="php:function('lang', 'Saturday')" />
									</option>
									<option value="7">
										<xsl:value-of select="php:function('lang', 'Sunday')" />
									</option>
								</select>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'From')" />
								</label>
								<div class="time-picker">
									<input id="field_from" name="from_" type="text">
										<xsl:attribute name="value">
											<xsl:value-of select="boundary/from_"/>
										</xsl:attribute>
									</input>
								</div>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'To')" />
								</label>
								<div class="time-picker">
									<input id="field_to" name="to_" type="text">
										<xsl:attribute name="value">
											<xsl:value-of select="boundary/to_"/>
										</xsl:attribute>
									</input>
								</div>
							</div>
							<div class="pure-control-group">
								<label>&nbsp;</label>
								<input type="submit" class="pure-button pure-button-primary">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('lang', 'Add')"/>
									</xsl:attribute>
								</input>
							</div>
						</div>
					</div>
				</xsl:if>
				<!-- <form action="" method="POST">
					<dl class="form">
						<dt class="heading"><xsl:value-of select="php:function('lang', 'Copy boundaries')" /></dt>
						<input type="search"/>
						<input type="submit">
							<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Search')"/></xsl:attribute>
						</input>
						<div id="foo_container"/>
						<input type="submit">
							<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Clone boundaries')"/></xsl:attribute>
						</input>
					</dl>
					<div class="form-buttons">
						<input type="submit">
							<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Add')"/></xsl:attribute>
						</input>
						<a class="cancel">
							<xsl:attribute name="href"><xsl:value-of select="season/cancel_link"/></xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Cancel')"/>
						</a>
					</div>
				</form> -->
			</div>
		</div>
		<div class="form-buttons">
			<input type="button" class="pure-button pure-button-primary" name="cencel">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>
