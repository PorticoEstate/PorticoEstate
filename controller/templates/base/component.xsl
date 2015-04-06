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
	<xsl:apply-templates select="form" />
	<xsl:apply-templates select="paging"/>
	<div id="list_flash">
		<xsl:call-template name="msgbox"/>
	</div>
	<xsl:apply-templates select="datatable"/> 
	<xsl:apply-templates select="form/list_actions"/>
	
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


<xsl:template match="toolbar">
	<div id="toolbar">
		<table>
			<tr>
				<xsl:for-each select="item">
					<xsl:variable name="filter_key" select="concat('filter_', name)"/>
					<xsl:variable name="filter_key_name" select="concat(concat('filter_', name), '_name')"/>
					<xsl:variable name="filter_key_id" select="concat(concat('filter_', name), '_id')"/>
		
					<xsl:choose>
						<xsl:when test="type = 'date-picker'">
							<td>
								<div class="date-picker">
									<input id="filter_{name}" name="filter_{name}" type="text">
										<xsl:attribute name="value">
											<xsl:value-of select="../../../filters/*[local-name() = $filter_key]"/>
										</xsl:attribute>
									</input>
								</div>
							</td>
						</xsl:when>
						<xsl:when test="type = 'filter'">
							<td>
								<xsl:variable name="name">
									<xsl:value-of select="name"/>
								</xsl:variable>
					
								<select id="{$name}" name="{$name}">
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
							</td>
						</xsl:when>
						<xsl:otherwise>
							<td valign="top">
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
							</td>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
			</tr>
			<xsl:if test="item/text and normalize-space(item/text)">
				<thead>
					<tr>
						<xsl:for-each select="item">
							<td>
								<xsl:if test="name">
									<label>
										<xsl:attribute name="for">
											<xsl:value-of select="phpgw:conditional(not(name), '', name)"/>
										</xsl:attribute>
										<xsl:value-of select="phpgw:conditional(not(text), '', text)"/>
									</label>
								</xsl:if>
							</td>
						</xsl:for-each>
					</tr>
				</thead>
			</xsl:if>
		</table>
	</div>
</xsl:template>

<xsl:template match="datatable">
	<div id="paginator"/>
	<div id="datatable-container"/>
	<xsl:call-template name="datasource-definition" />
</xsl:template>


<xsl:template name="datasource-definition">
	<script type="text/javascript">
<![CDATA[
		update_table = function()
		{
			var requestUrl = $("#queryForm").attr("action");
			requestUrl += '&phpgw_return_as=json' + "&" + $("#queryForm").serialize();

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: requestUrl,
				success: function(data) {
					if( data != null)
					{
						$("#tbody").html(data.tbody);
					}
				}
			});

		};
]]>
	</script>
	<table id="datatable-container" class="display cell-border compact responsive no-wrap" width="100%">
		<thead>
			<tr>
				<xsl:for-each select="//datatable/field">
					<th>
						<xsl:value-of select="label"/>
					</th>
				</xsl:for-each>
			</tr>
		</thead>
		<tbody id="tbody"></tbody>
	</table>
	 
</xsl:template>
