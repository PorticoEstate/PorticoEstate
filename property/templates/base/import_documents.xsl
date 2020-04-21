<!-- $Id$ -->


<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang',  'building part', 'branch', 'doument type')"/>


		$(document).ready(function() {

 
		});
	</script>
	<div class="container">
		<div class="row">
			<h1>Step 1: Order refefrence</h1>
		</div>
		<form class="pure-form pure-form-aligned">
			<div class="pure-control-group">
				<label >
					<xsl:value-of select="php:function('lang', 'order id')"/>
				</label>
				<input id="order_id" required="required"></input>
			</div>
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
		<div class="form-group">

			- Matrikkelinfo: Gnr/Bnr
			- Lokasjonskode/Objekt
			- Bygningsnr

		</div>

		<div class="row">
			<h1>2) laste opp alle dokumentene til venterommet</h1>

			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'upload files')"/>
				</label>

				<xsl:call-template name="multi_upload_file_inline">
					<xsl:with-param name="class">pure-input-3-4 pure-custom</xsl:with-param>
					<xsl:with-param name="multi_upload_action">
						<xsl:value-of select="multi_upload_action"/>
					</xsl:with-param>
				</xsl:call-template>
			</div>


			<ul>
				<li>
					– og supplere individuelt (alle eller utvalg) med resten av informasjonen på same måte som vi nettopp har etablert for multi-tagging:
				</li>
				<ul>
					<li>
						-- Dokumentkategori
					</li>
					<li>
						-- Fag
					</li>
					<li>
						-- Bygningsdel
					</li>
				</ul>
			</ul>
		</div>

		<fieldset class="pure-form pure-form-aligned">

			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'doument type')"/>
				</label>

				<select id='doument_type' multiple="multiple">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'select')"/>
					</xsl:attribute>
					<xsl:apply-templates select="doument_type_list/options"/>
				</select>
			</div>
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'branch')"/>
				</label>

				<select id='branch' multiple="multiple">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'select')"/>
					</xsl:attribute>
					<xsl:apply-templates select="branch_list/options"/>
				</select>
			</div>
			<div class="pure-control-group">
				<xsl:variable name="lang_building_part">
					<xsl:value-of select="php:function('lang', 'building part')"/>
				</xsl:variable>
				<label>
					<xsl:value-of select="$lang_building_part"/>
				</label>

				<select id="building_part" class="pure-input-3-4"  multiple="multiple">
					<xsl:attribute name="title">
						<xsl:value-of select="$lang_building_part"/>
					</xsl:attribute>
					<xsl:attribute name="data-validation">
						<xsl:text>required</xsl:text>
					</xsl:attribute>
					<xsl:attribute name="data-validation-error-msg">
						<xsl:value-of select="$lang_building_part"/>
					</xsl:attribute>
					<xsl:apply-templates select="building_part_list/options"/>
				</select>
			</div>


			<div class="pure-control-group">
				<xsl:for-each select="datatable_def">
					<xsl:if test="container = 'datatable-container_0'">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'files')"/>
							</label>
							<div class="pure-custom pure-u-md-3-4" >
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl'/>
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
									<xsl:with-param name="data" select ='data'/>
									<xsl:with-param name="tabletools" select ='tabletools' />
									<xsl:with-param name="config" select ='config'/>
								</xsl:call-template>
							</div>
						</div>
					</xsl:if>
				</xsl:for-each>
			</div>
		</fieldset>



		<div class="row">

			<h1>3) Validere at krav til detaljer er oppfylt</h1>

			<h1>4) Legge på evnt merknader</h1>

			<h1>5) Importere filene til BraArkiv</h1>

			<h1>6) Rydde på venterommet</h1>
		</div>


		<form method="post" id="form" action="{form_action}">

			<div class="row">
				<div class="mt-5 container">
					<div class="form-group">
						<fieldset>
							<legend>Velg kontroll</legend>

							<label for="control_area_id">
								<xsl:value-of select="php:function('lang', 'control area')"/>
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
							<label for="part_of_town_id">
								<xsl:value-of select="php:function('lang', 'part of town')"/>
							</label>
							<select id="part_of_town_id" name="part_of_town_id" class="form-control" onchange="this.form.submit()">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'select')"/>
								</xsl:attribute>
								<xsl:apply-templates select="part_of_town_list/options"/>
							</select>
						</fieldset>
					</div>
				</div>
			</div>
		</form>
		<div class="row">
			<div class="mt-5 container">
				<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{form_action}">
					<table border="0" cellspacing="2" cellpadding="2" class="pure-table pure-table-bordered " id='user_table'>
						<thead>
							<tr>
								<th>
									<xsl:value-of select="php:function('lang', 'user')"/>

								</th>
								<th>
									<xsl:value-of select="php:function('lang', 'role')"/>

								</th>
								<th>
									<xsl:value-of select="php:function('lang', 'last login')"/>

								</th>
								<th>
									<xsl:value-of select="php:function('lang', 'status')"/>
								</th>
							</tr>
						</thead>
						<tbody>
							<xsl:apply-templates select="user_data"/>
						</tbody>
					</table>
					<xsl:apply-templates select="cat_add"/>
				</form>

			</div>
		</div>

	</div>

</xsl:template>


<xsl:template match="user_data" xmlns:php="http://php.net/xsl">
	<tr>
		<td>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</td>
		<td align="center">
			<select name="values[{control_id}][{part_of_town_id}][{id}][new][]" multiple="multiple" class="user_roles">
				<!--				<option value="">
					<xsl:value-of select="php:function('lang', 'select')"/>
				</option>-->
				<xsl:apply-templates select="../roles/options">
					<xsl:with-param name="selected" select="selected_role"/>
				</xsl:apply-templates>
			</select>
			<input type="hidden" name="values[{control_id}][{part_of_town_id}][{id}][original]" value="{original_value}"/>
		</td>
		<td>
			<xsl:value-of select="lastlogin"/>
		</td>
		<td>
			<xsl:value-of select="status"/>
		</td>
	</tr>
</xsl:template>


<!-- BEGIN cat_list -->

<xsl:template match="edit">

	<section id="tabs">
		<div class="container">
			<div class="row">
				<div id="tab-content" class="col-xs-12 ">

					<xsl:value-of disable-output-escaping="yes" select="tabs"/>

					<div id="category_assignment">
						<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{form_action}">
							<table border="0" cellspacing="2" cellpadding="2" class="pure-table pure-table-bordered ">
								<xsl:apply-templates select="cat_header"/>
								<xsl:apply-templates select="cat_data"/>
							</table>
							<xsl:apply-templates select="cat_add"/>
						</form>

					</div>
					<div id="vendors">
					</div>

				</div>
			</div>
		</div>
	</section>
	
</xsl:template>

<!-- BEGIN cat_header -->

<xsl:template match="cat_header">
	<tr class="th">
		<th width="45%">
			<xsl:value-of select="lang_name"/>
		</th>
		<th width="45%" align="center">
			<xsl:value-of select="lang_edit"/>
		</th>
	</tr>
</xsl:template>

<!-- BEGIN cat_data -->

<xsl:template match="cat_data" xmlns:php="http://php.net/xsl">
	<tr>
		<td>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</td>
		<td align="center">
			<select name="values[{control_id}]" >
				<option value="">
					<xsl:value-of select="php:function('lang', 'select')"/>
				</option>
				<xsl:apply-templates select="cat_list/options"/>
			</select>
		</td>
	</tr>
</xsl:template>

<!-- BEGIN cat_add -->

<xsl:template match="cat_add">
	<table>
		<tr height="50" valign="bottom">
			<td colspan="2">
				<xsl:variable name="lang_add">
					<xsl:value-of select="php:function('lang', 'save')"/>
				</xsl:variable>
				<input type="submit" name="save" value="{$lang_add}" class="pure-button pure-button-primary" >
					<xsl:attribute name="title">
						<xsl:value-of select="lang_add_statustext"/>
					</xsl:attribute>
				</input>
			</td>
			<td colspan="3" align="right">
				<xsl:variable name="cancel_url">
					<xsl:value-of select="//cancel_url"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{$cancel_url}';">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'cancel')"/>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</table>
</xsl:template>

<!-- END cat_list -->


<xsl:template match="options">
	<xsl:param name="selected"/>
	<option value="{id}">
		<!--<xsl:if test="selected = 1 or id = $selected or contains($selected, id )">-->
		<xsl:if test="selected = 1 or id = $selected">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>