<!-- $Id$ -->

<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
		<xsl:when test="users">
			<xsl:apply-templates select="users"/>
		</xsl:when>

	</xsl:choose>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>


<xsl:template match="users" xmlns:php="http://php.net/xsl">
<script type="text/javascript">
    $(document).ready(function() {

        $('.user_roles').multiselect({
            templates: {
                li: '<li><div style="display:inline;"><a><label></label></a></div></li>'
            }
        });

		$("#control_area_id").change(function ()
		{
			var control_area_id = $(this).val();

			if(control_area_id == -1)
			{

				$("#user_table tbody").empty();
			}
		 });

    });
</script>
	<section id="tabs">
		<div class="container">
			<div class="row">
				<div id="tab-content" class="col-xs-12 ">

					<xsl:value-of disable-output-escaping="yes" select="tabs"/>
					<div id="users">
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
					<div id="vendors">
					</div>

				</div>
			</div>
		</div>
	</section>

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