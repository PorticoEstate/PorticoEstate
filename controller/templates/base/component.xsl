<!-- $Id$ -->
<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>

<xsl:template match="data">
	<h2>
		<xsl:value-of select="datatable_name"/>
	</h2>
	<div class="content">
		<div id="receipt"></div>

		<div class="pure-g">
			<div class="pure-u-1 pure-u-md-1-2">
				<xsl:apply-templates select="form" />
			</div>
			<div class="pure-u-1 pure-u-md-1-2">
				<xsl:call-template name="icon_color_map" />
			</div>
		</div>


		<xsl:apply-templates select="paging"/>
		<div id="list_flash">
			<xsl:call-template name="msgbox"/>
		</div>
		<xsl:apply-templates select="datatable"/>
		<xsl:apply-templates select="form/list_actions"/>
	</div>
	
</xsl:template>

<xsl:template match="form">
	<form id="queryForm">
		<xsl:attribute name="method">
			<xsl:value-of select="phpgw:conditional(not(method), 'GET', method)"/>
		</xsl:attribute>

		<xsl:attribute name="action">
			<xsl:value-of select="phpgw:conditional(not(action), '', action)"/>
		</xsl:attribute>
		<xsl:apply-templates select="toolbar"/>
	</form>

	<form id="update_table_dummy" method='POST' action='' >
	</form>

</xsl:template>


<xsl:template match="toolbar" xmlns:php="http://php.net/xsl">
	<style id='toggle-box-css' type='text/css' scoped='scoped'>
		.toggle-box {
		display: none;
		}

		.toggle-box + label {
		cursor: pointer;
		display: block;
		font-weight: bold;
		line-height: 21px;
		margin-bottom: 5px;
		}

		.toggle-box + label + #toolbar {
		display: none;
		margin-bottom: 10px;
		}

		.toggle-box:checked + label + #toolbar {
		display: block;
		}

		.toggle-box + label:before {
		background-color: #4F5150;
		-webkit-border-radius: 10px;
		-moz-border-radius: 10px;
		border-radius: 10px;
		color: #FFFFFF;
		content: "+";
		display: block;
		float: left;
		font-weight: bold;
		height: 20px;
		line-height: 20px;
		margin-right: 5px;
		text-align: center;
		width: 20px;
		}

		.toggle-box:checked + label:before {
		content: "\2212";
		}
	</style>
	<div id="active_filters"></div>

	<input class="toggle-box" id="header1" type="checkbox" />
	<label for="header1">
		<xsl:value-of select="php:function('lang', 'filter')"/>
	</label>

	<div id="toolbar">
		<form class="pure-form pure-form-stacked">
				<xsl:for-each select="item">
					<div class="pure-u-1 pure-u-md-1-3">
						<label>
							<xsl:attribute name="for">
								<xsl:value-of select="phpgw:conditional(not(name), '', name)"/>
							</xsl:attribute>
							<xsl:value-of select="phpgw:conditional(not(text), '', text)"/>
						</label>

						<xsl:variable name="filter_key" select="concat('filter_', name)"/>
						<xsl:variable name="filter_key_name" select="concat(concat('filter_', name), '_name')"/>
						<xsl:variable name="filter_key_id" select="concat(concat('filter_', name), '_id')"/>
		
						<xsl:choose>
							<xsl:when test="type = 'date-picker'">
								<div class="date-picker">
									<input class="pure-u-24-24" id="filter_{name}" name="filter_{name}" value="{value}" type="text">
										<xsl:attribute name="value">
											<xsl:value-of select="../../../filters/*[local-name() = $filter_key]"/>
										</xsl:attribute>
									</input>
								</div>
							</xsl:when>
							<xsl:when test="type = 'filter'">
								<xsl:variable name="name">
									<xsl:value-of select="name"/>
								</xsl:variable>
								<select id="{$name}" name="{$name}" class="pure-u-24-24">
									<xsl:attribute name="onchange">
										<xsl:value-of select="phpgw:conditional(not(onchange), '', onchange)"/>
									</xsl:attribute>
									<xsl:for-each select="list">
										<xsl:variable name="id">
											<xsl:value-of select="id"/>
										</xsl:variable>
										<option value="{$id}">
											<xsl:if test="selected = '1'">
												<xsl:attribute name="selected">
													<xsl:text>selected</xsl:text>
												</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="name"/>
										</option>
									</xsl:for-each>
								</select>
							</xsl:when>
							<xsl:otherwise>
								<input id="innertoolbar">
									<xsl:attribute name="type">
										<xsl:value-of select="phpgw:conditional(not(type), '', type)"/>
									</xsl:attribute>
									<xsl:attribute name="name">
										<xsl:value-of select="phpgw:conditional(not(name), '', name)"/>
									</xsl:attribute>
									<xsl:attribute name="onclick">
										<xsl:value-of select="phpgw:conditional(not(onclick), '', onclick)"/>
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="phpgw:conditional(not(value), '', value)"/>
									</xsl:attribute>
									<xsl:attribute name="href">
										<xsl:value-of select="phpgw:conditional(not(href), '', href)"/>
									</xsl:attribute>
									<xsl:attribute name="class">
										<xsl:value-of select="phpgw:conditional(not(class), '', class)"/>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</xsl:for-each>

				<div class="pure-u-1">
					<label for='location_code'>
						<xsl:value-of select="php:function('lang', 'location')"/>
					</label>
					<input type="hidden" id="location_code" name="location_code" />
					<input type="text" id="location_name" name="location_name" />
					<div id="location_container"/>
				</div>
				<div id = 'extra_row' class="pure-u-1">
					<label for='extra_filter'>
						Extra
					</label>
					<div id="extra_filter">
					</div>
				</div>
			<input type ='hidden' id='filtered_location_id'/>
		</form>
	</div>
</xsl:template>

<xsl:template match="datatable">
	<style>
		#components {
		font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
		width: 100%;
		border-collapse: collapse;
		}

		#components td, #components th {
		font-size: 1em;
		border: 1px solid #98bf21;
		padding: 3px 7px 2px 7px;
		}

		#components th {
		font-size: 1.1em;
		text-align: left;
		padding-top: 5px;
		padding-bottom: 4px;
		background-color: green;
		color: #ffffff;
		}

		#components tr.alt td {
		color: #000000;
		background-color: #EAF2D3;
		}
		#summary {
		font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
		width: 100%;
		border-collapse: collapse;
		}

		#summary td, #summary th {
		font-size: 1em;
		border: 1px solid #98bf21;
		padding: 3px 7px 2px 7px;
		}

		#summary th {
		font-size: 1.1em;
		text-align: left;
		padding-top: 5px;
		padding-bottom: 4px;
		background-color: green;
		color: #ffffff;
		}

		#summary tr.alt td {
		color: #000000;
		background-color: #EAF2D3;
		}
	</style>

	<xsl:call-template name="datasource-definition" />
</xsl:template>


<xsl:template name="datasource-definition">
	<table id="components">
		<thead>
			<tr>
				<td id='checkall'>
				</td>
				<td id='total_records'>
				</td>
				<td id='control_text'>
				</td>
				<td id='sum_text'>
				</td>
				<td id='monthsum'>
				</td>
				<td id='month1'>
				</td>
				<td id='month2'>
				</td>
				<td id='month3'>
				</td>
				<td id='month4'>
				</td>
				<td id='month5'>
				</td>
				<td id='month6'>
				</td>
				<td id='month7'>
				</td>
				<td id='month8'>
				</td>
				<td id='month9'>
				</td>
				<td id='month10'>
				</td>
				<td id='month11'>
				</td>
				<td id='month12'>
				</td>
			</tr>
		</thead>
		<thead>
			<tr>
				<xsl:for-each select="//datatable/field">
					<th id = "head{id}">
						<xsl:value-of select="label"/>
					</th>
				</xsl:for-each>
			</tr>
		</thead>
		<tbody id="tbody"></tbody>
	</table>
	<div id="status_summary">
		
	</div>
	<script type="text/javascript">
		var show_months = [];
		<xsl:for-each select="//datatable/months">
			show_months.push(<xsl:value-of select="key"/>);
		</xsl:for-each>

	</script>

	<div id="dialog-form" title="Egne timer">
		<p>Godkjenner du denne uten avvik?</p>
		<form>
			<!--<fieldset>-->
			<xsl:if test="//required_actual_hours = '1'">
				<div class="pure-control-group">
					<label>Egne Timer</label>
					<input class="pure-input-1" type="number" step="0.01" min="1" required='required'>
						<xsl:attribute name="id">billable_hours</xsl:attribute>
						<xsl:attribute name="name">billable_hours</xsl:attribute>
					</input>
				</div>
			</xsl:if>
			<input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
			</input>
			<!--</fieldset>-->
		</form>
	</div>
	 
</xsl:template>
