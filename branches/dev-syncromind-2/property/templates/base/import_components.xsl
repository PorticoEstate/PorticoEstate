<!-- $Id: generic_document.xsl 14792 2016-03-01 18:59:36Z sigurdne $ -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>

	<div id="document_edit_tabview">

		<xsl:value-of select="validator"/>
		
			<div id="tab-content">					
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
						
				<div id="locations">
					<form name="form" class="pure-form pure-form-aligned" id="form" action="" method="post">	
						<div class="pure-control-group">
							<label for="vendor">
								<xsl:value-of select="php:function('lang', 'type')" />
							</label>
							<select id="type_id" name="type_id">
								<xsl:apply-templates select="type_filter/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label for="vendor">
								<xsl:value-of select="php:function('lang', 'category')" />
							</label>
							<select id="cat_location_id" name="cat_location_id">
								<xsl:apply-templates select="category_filter/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label for="vendor">
								<xsl:value-of select="php:function('lang', 'district')" />
							</label>
							<select id="district_id" name="district_id">
								<xsl:apply-templates select="district_filter/options"/>
							</select>
						</div>				
						<div class="pure-control-group">
							<label for="vendor">
								<xsl:value-of select="php:function('lang', 'part of town')" />
							</label>
							<select id="part_of_town_id" name="part_of_town_id">
								<xsl:apply-templates select="part_of_town_filter/options"/>
							</select>
						</div>								

						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_0'">
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl' />
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
									<xsl:with-param name="tabletools" select ='tabletools' />
									<xsl:with-param name="config" select ='config' />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>		
					</form>		
				</div>
				
				<div id="upload">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'location')"/>
						</label>
						<div id="location_name" class='pure-custom'></div>
						<input type="hidden" id="location_code" name="location_code" value=""></input>
					</div>
					<!--
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'upload file')"/>
						</label>
						<input type="file" name="file" size="40">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Select file to upload')"/>
							</xsl:attribute>
						</input>
					</div>	-->
					<xsl:value-of disable-output-escaping="yes" select="form_file_upload"/>
					<div class="pure-control-group">
						<label></label>
						<input type="submit" name="importsubmit" size="40">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'Start import')"/>
							</xsl:attribute>
						</input>
					</div>					
				</div>
				
			</div>
	</div>

</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected = 'selected' or selected = 1">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:attribute name="title" value="description" />
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
