<xsl:template match="data" xmlns:php="http://php.net/xsl">
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

		.toggle-box + label + div {
		display: none;
		margin-bottom: 10px;
		}

		.toggle-box:checked + label + div {
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

		#expandcontractdiv {border:1px dotted #dedede; margin:0 0 .5em 0; padding:0.4em;}
		#treeDiv { background: #fff; padding:1em; margin-top:1em; }
	</style>
	<xsl:call-template name="msgbox"/>
	<div>
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="activities">
				<input type="hidden" name="application_id" value="{booking/application_id}"/>
				<form id="queryForm" method="GET" action="">
					<input class="toggle-box" id="header1" type="checkbox" />
					<label for="header1">
						<xsl:value-of select="php:function('lang', 'toolbar')"/>
					</label>
					<div id="toolbar">
						<!--xsl:if test="item/text and normalize-space(item/text)"-->
						<table id="toolbar_table" class="pure-table">
							<thead>
								<tr>
									<th>
										<xsl:value-of select="php:function('lang', 'name')"/>
									</th>
									<th>
										<xsl:value-of select="php:function('lang', 'item')"/>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
									</td>
									<td>
										<xsl:if test="links/add">
											<input type="button" class="pure-button pure-button-primary">
												<xsl:attribute name="onclick">javascript:window.open('<xsl:value-of select="links/add"/>', "_self");</xsl:attribute>
												<xsl:attribute name="value">
													<xsl:value-of select="php:function('lang', 'Add Activity')" />
												</xsl:attribute>
												<xsl:attribute name="id">new-button</xsl:attribute>
											</input>
										</xsl:if>
									</td>

								</tr>
								<tr>
									<td></td>
									<td>
										<xsl:if test="not(show_all='1')">
											<input type="button" class="pure-button pure-button-primary">
												<xsl:attribute name="onclick">javascript:window.open('<xsl:value-of select="links/show_inactive"/>', "_self");</xsl:attribute>
												<xsl:attribute name="value">
													<xsl:value-of select="php:function('lang', 'show all')" />
												</xsl:attribute>
												<xsl:attribute name="id">new-button</xsl:attribute>
											</input>
										</xsl:if>
										<xsl:if test="show_all='1'">
											<input type="button" class="pure-button pure-button-primary">
												<xsl:attribute name="onclick">javascript:window.open('<xsl:value-of select="links/hide_inactive"/>', "_self");</xsl:attribute>
												<xsl:attribute name="value">
													<xsl:value-of select="php:function('lang', 'Show only active')" />
												</xsl:attribute>
												<xsl:attribute name="id">new-button</xsl:attribute>
											</input>
										</xsl:if>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</form>
				<div id="tree_container">
					<legend>
						<h3>
							<xsl:value-of select="php:function('lang', 'Current Activities')" />
						</h3>
					</legend>
					<script type="text/javascript">
						var activities = null;
						<xsl:if test="treedata != ''">
							activities = <xsl:value-of select="treedata"/>;
						</xsl:if>
					</script>
					<!-- markup for expand/contract links -->
					<div id="treecontrol">
						<a id="collapse" title="Collapse the entire tree below" href="#">
							<xsl:value-of select="php:function('lang', 'collapse all')"/>
						</a>
						<xsl:text> | </xsl:text>
						<a id="expand" title="Expand the entire tree below" href="#">
							<xsl:value-of select="php:function('lang', 'expand all')"/>
						</a>
					</div>
					<div id="treeDiv"></div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$("#treeDiv").jstree({
		"core" : {
		"multiple" : false,
		"themes" : { "stripes" : true },
		"data" : activities,
		},
		"plugins" : [ "themes","html_data","ui","state" ]
		});

		var count1 = 0;
		$("#treeDiv").bind("select_node.jstree", function (event, data) {
		count1 += 1;
		var divd = data.instance.get_node(data.selected[0]).original['href'];
		if(count1 > 1)
		{
		window.location.href = divd;
		}
		});

		$('#collapse').on('click',function(){
		$(this).attr('href','javascript:;');
		$('#treeDiv').jstree('close_all');
		})
		$('#expand').on('click',function(){
		$(this).attr('href','javascript:;');
		$('#treeDiv').jstree('open_all');
		});
	</script>
</xsl:template>
