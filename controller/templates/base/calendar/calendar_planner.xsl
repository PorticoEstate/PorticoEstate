
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="start">
			<xsl:apply-templates select="start"/>
		</xsl:when>
		<xsl:when test="monthly">
			<xsl:apply-templates select="monthly"/>
		</xsl:when>
		<xsl:when test="notification">
			<xsl:apply-templates select="notification"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>


<xsl:template match="start" xmlns:php="http://php.net/xsl">
	<style>
		.table-hover-cells > tbody > tr > th:hover,
		.table-hover-cells > tbody > tr > td:hover {
		background-color: #f5f5f5;
		}

		.table-hover-cells > tbody > tr > th.active:hover,
		.table-hover-cells > tbody > tr > td.active:hover,
		.table-hover-cells > tbody > tr.active > th:hover,
		.table-hover-cells > tbody > tr.active > td:hover {
		background-color: #e8e8e8;
		}

		.table-hover.table-hover-cells > tbody > tr:hover > th:hover,
		.table-hover.table-hover-cells > tbody > tr:hover > td:hover {
		background-color: #e8e8e8;
		}

		.table-hover.table-hover-cells > tbody > tr.active:hover > th:hover,
		.table-hover.table-hover-cells > tbody > tr.active:hover > td:hover {
		background-color: #d8d8d8;
		}

		h1 > .divider:before,
		h2 > .divider:before,
		h3 > .divider:before,
		h4 > .divider:before,
		h5 > .divider:before,
		h6 > .divider:before,
		.h1 > .divider:before,
		.h2 > .divider:before,
		.h3 > .divider:before,
		.h4 > .divider:before,
		.h5 > .divider:before,
		.h6 > .divider:before {
		color: #777;
		content: "\0223E\0020";
		}


	</style>
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<xsl:variable name="current_year">
		<xsl:value-of select="current_year"/>
	</xsl:variable>

	<form method="post" id="form" action="{form_action}">

		<div class="row">
			<div class="mt-5 container">
				<div class="form-group">
					<fieldset>
						<legend>Velg kontroll</legend>

						<label for="control_area_id">
							<xsl:value-of select="php:function('lang', 'control type')"/>
						</label>
						<select id="control_area_id" name="control_area_id" class="form-control">
							<xsl:apply-templates select="control_area_list/options"/>
						</select>

						<label for="control_id">
							<xsl:value-of select="php:function('lang', 'control')"/>
						</label>
						<select id="control_id" name="control_id" class="form-control" onchange="this.form.submit()">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select control type')"/>
							</xsl:attribute>
							<xsl:apply-templates select="control_type_list/options"/>
						</select>

						<!--						<label for="entity_group_id">
							<xsl:value-of select="php:function('lang', 'entity group')"/>
						</label>
						<select id="entity_group_id" name="entity_group_id" class="form-control" onchange="this.form.submit()">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</xsl:attribute>
							<xsl:apply-templates select="entity_group_list/options"/>
						</select>-->

						<label for="part_of_town_id">
							<xsl:value-of select="php:function('lang', 'part of town')"/>
						</label>
						<select id="part_of_town_id" name="part_of_town_id[]" class="form-control">
							<xsl:attribute name="multiple">
								<xsl:text>true</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select part of town')"/>
							</xsl:attribute>
							<xsl:apply-templates select="part_of_town_list/options"/>
						</select>

					</fieldset>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="mt-5 container">
				<div class="text-center clearfix">
					<span class="float-left">
						<a href="#">
							<button type="submit" name="prev_year" value="1" class="btn btn-secondary">&lt;
								<xsl:value-of select="prev_year"/>
							</button>
						</a>
					</span>

					<span class="float-right">
						<a href="#">
							<button type="submit" name="next_year" value="1" class="btn btn-secondary">
								<xsl:value-of select="next_year"/> &gt;
							</button>
						</a>
					</span>
					<span class="float-none">
						<h4>
							<xsl:value-of select="$current_year"/>
							<input type="hidden" name="current_year" value="{$current_year}"/>
						</h4>
					</span>
				</div>
				<div class="mt-3 row datagrid table-responsive">
					<table class="table table-bordered table-hover-cells">
						<thead>
							<tr>
								<th>
									<h5>Bydel</h5>
								</th>
								<xsl:for-each select="first_half_year">
									<th>
										<h5>
											<xsl:value-of select="name"/>
										</h5>
									</th>
								</xsl:for-each>
							</tr>
						</thead>
						<tbody>
							<xsl:apply-templates select="calendar_content1/rows">
								<xsl:with-param name="current_year" select ='$current_year'/>
							</xsl:apply-templates>

						</tbody>
						<thead>
							<tr>
								<th>
									<h5>Bydel</h5>
								</th>
								<xsl:for-each select="second_half_year">
									<th>
										<h5>
											<xsl:value-of select="name"/>
										</h5>
									</th>
								</xsl:for-each>
							</tr>
						</thead>
						<tbody>
							<xsl:apply-templates select="calendar_content2/rows">
								<xsl:with-param name="current_year" select ='$current_year'/>
							</xsl:apply-templates>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</form>

	<div class="container">
		<div class="clearfix">
			<span class="float-left">
				<a href="#">
					<button type="button" class="btn btn-warning">Nullstill kalender</button>
				</a>
			</span>
		</div>
	</div>

</xsl:template>


<xsl:template match="monthly" xmlns:php="http://php.net/xsl">
	<style>
		.table-hover-cells > tbody > tr > th:hover,
		.table-hover-cells > tbody > tr > td:hover {
		background-color: #f5f5f5;
		}

		.table-hover-cells > tbody > tr > th.active:hover,
		.table-hover-cells > tbody > tr > td.active:hover,
		.table-hover-cells > tbody > tr.active > th:hover,
		.table-hover-cells > tbody > tr.active > td:hover {
		background-color: #e8e8e8;
		}

		.table-hover.table-hover-cells > tbody > tr:hover > th:hover,
		.table-hover.table-hover-cells > tbody > tr:hover > td:hover {
		background-color: #e8e8e8;
		}

		.table-hover.table-hover-cells > tbody > tr.active:hover > th:hover,
		.table-hover.table-hover-cells > tbody > tr.active:hover > td:hover {
		background-color: #d8d8d8;
		}

		h1 > .divider:before,
		h2 > .divider:before,
		h3 > .divider:before,
		h4 > .divider:before,
		h5 > .divider:before,
		h6 > .divider:before,
		.h1 > .divider:before,
		.h2 > .divider:before,
		.h3 > .divider:before,
		.h4 > .divider:before,
		.h5 > .divider:before,
		.h6 > .divider:before {
		color: #777;
		content: "\0223E\0020";
		}

		table th, table td {
		width: 200px;
		}
		table span {
		height: 30px;
		width: 100%;
		}
	</style>

	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<div class="mt-5 container">
		<div class="row">
			<div class="col">
				<h3>
					<xsl:value-of select="current_month"/>
					<xsl:text> </xsl:text>
					<xsl:value-of select="current_year"/>
				</h3>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<p style="font-size: 14px">I <xsl:value-of select="current_month"/> er det satt opp befaring i <xsl:value-of select="part_of_town"/>:
				</p>
			</div>
			<div class="col">
				<div class="clearfix">
					<span class="float-right" style="font-size: 14px">Legg til ny <i class="far fa-plus-square"></i></span>
				</div>
				<div class="mt-2 clearfix">
					<span class="float-right" style="font-size: 14px">Merk som inaktiv <i class="far fa-trash-alt"></i></span>
				</div>
			</div>
		</div>


		<div class="text-center clearfix">
			<span class="float-left">
				<a href="{prev_month_url}">
					<button type="button" name="prev_year" value="1" class="btn btn-secondary">&lt;
						<xsl:value-of select="prev_month"/>
					</button>
				</a>
			</span>

			<span class="float-right">
				<a href="{next_month_url}">
					<button type="button" name="next_year" value="1" class="btn btn-secondary">
						<xsl:value-of select="next_month"/> &gt;
					</button>
				</a>
			</span>
			<span class="float-none">
				<h4>
					<xsl:value-of select="current_month"/>
					<xsl:text> </xsl:text>
					<xsl:value-of select="current_year"/>
				</h4>
			</span>
		</div>

		<div class="container datagrid table-responsive">
			<xsl:value-of disable-output-escaping="yes" select="calendar"/>
		</div>

		<!--https://jsfiddle.net/d1wnk1bg/8/-->


		<div class="container">
			<div class="clearfix">
				<span class="float-left">
					<a href="{start_url}">
						<button type="button" class="btn btn-warning">Gå tilbake</button>
					</a>
				</span>
				<!--				<span class="ml-2 float-left">
					<a href="#">
						<button type="button" class="btn btn-warning">Nullstill kalender</button>
					</a>
				</span>-->
				<span class="float-right">
					<a href="{send_notification_url}">
						<button type="button" class="btn btn-success">Gå til utsending</button>
					</a>
				</span>
				<span class="mr-2 float-right">
					<button type="button" class="btn btn-success" onclick="save_schedule();">Lagre</button>
				</span>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="notification" xmlns:php="http://php.net/xsl">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<div class="mt-5 container">
		<div class="row">
			<div class="col">
				<h3>Send varsel</h3>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<p style="font-size: 14px">Det vil sendes varsel til følgende skoler og barnehager
				</p>
			</div>
		</div>

		
		<div class="container datagrid table-responsive">
			<table class="mt-2 table table-hover">
				<thead>
					<tr>
						<th>
							<h5>#</h5>
						</th>
						<th>
							<h5>Enhet</h5>
						</th>
						<th>
							<h5>Epostadresse</h5>
						</th>
						<th>
							<h5>Dato</h5>
						</th>
					</tr>
				</thead>
				<tbody>

					<xsl:for-each select="control_info">
						<tr>
							<td>
								<input name="send_email_{location_id}_{id}" type="checkbox" value="1">
									<xsl:if test="selected = 1">
										<xsl:attribute name="checked">
											<xsl:text>true</xsl:text>
										</xsl:attribute>
									</xsl:if>
									
								</input>
							</td>
							<td>
								<xsl:value-of select="address"/>
							</td>
							<td>
								<input type="text" name="email_{location_id}_{id}" value="postmottak.....skole@bergen.kommune.no"/>
							</td>
							<td>
								<xsl:value-of select="date"/>
							</td>
						</tr>

					</xsl:for-each>
				</tbody>
			</table>
		</div>

		<div class="container">
			<div class="clearfix">
				<span class="float-left">
					<a href="{monthly_url}">
						<button type="button" class="btn btn-warning">Gå tilbake</button>
					</a>
				</span>
				<span class="float-right">
					<a href="">
						<button type="button" class="btn btn-success">Send varsel</button>
					</a>
				</span>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>


<xsl:template match="rows">
	<xsl:param name="current_year"/>
	<tr>
		<th scope="row">
			<xsl:value-of select="header"/>
		</th>
		<xsl:for-each select="cell_data">
			<td onClick="open_monthly('{part_of_town_id}', '{$current_year}', '{month}');">
				<xsl:if test="registered &gt; 0">
					<span class="ml-3 float-left">
						<xsl:value-of select="registered"/>
						<xsl:text>/</xsl:text>
						<xsl:value-of select="planned"/>
						<xsl:text>/</xsl:text>
						<xsl:value-of select="completed"/>
					</span>
					<xsl:if test="registered = completed">
						<span class="float-right">
							<i class="fas fa-check float-right"></i>
						</span>
					</xsl:if>
				</xsl:if>

			</td>
		</xsl:for-each>
	</tr>
</xsl:template>

