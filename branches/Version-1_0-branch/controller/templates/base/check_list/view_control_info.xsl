<!-- $Id: edit_check_list.xsl 8478 2012-01-03 12:36:37Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
		
	<script>
		$(function() {
			
			$("#view_control_details").click(function(){
				var requestUrl = $(this).attr("href");
							
				$.ajax({
				  type: 'POST',
				  url: requestUrl,
				  success: function(data) {
				  	$("#tab_content").html(data);
				  }
				});
			
				return false;
			});
			
			$("#view_control_items").click(function(){
				var requestUrl = $(this).attr("href");
							
				$.ajax({
				  type: 'POST',
				  url: requestUrl,
				  success: function(data) {
				  	$("#tab_content").html(data);
				  }
				});
			
				return false;
			});
			
			$("#view_procedures").click(function(){
				var requestUrl = $(this).attr("href");
							
				$.ajax({
				  type: 'POST',
				  url: requestUrl,
				  success: function(data) {
				  	$("#tab_content").html(data);
				  }
				});
			
				return false;
			});
			
		});
	</script>
		
	<h1>Sjekkliste for <xsl:value-of select="location_array/loc1_name"/></h1>
	
	<div id="edit_check_list_menu" class="hor_menu">
		<a id="view_check_list">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.edit_check_list_for_location</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
			</xsl:attribute>
			Vis info om sjekkliste
		</a>
		
		<a id="view_control_details" class="active">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.view_control_info</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
			</xsl:attribute>
			Vis info om kontroll
		</a>
	</div>
				
	<div class="tab_menu">
		<a id="view_control_details" class="active">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_control_details</xsl:text>
				<xsl:text>&amp;control_id=</xsl:text>
				<xsl:value-of select="control/id"/>
			</xsl:attribute>
			Kontrolldetaljer
		</a>
		<a id="view_control_items">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_control_items</xsl:text>
				<xsl:text>&amp;control_id=</xsl:text>
				<xsl:value-of select="control/id"/>
			</xsl:attribute>
			Kontrollpunkter
		</a>
		<a id="view_procedures">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uiprocedure.view_procedures_for_control</xsl:text>
				<xsl:text>&amp;control_id=</xsl:text>
				<xsl:value-of select="control/id"/>
			</xsl:attribute>
			Prosedyrer
		</a>
	</div>
		
	<div id="tab_content" class="content_wrp">
	
	<fieldset>
		<dl class="proplist-col">
				<dt>
					<label>Kontrollområde</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<select id="control_area_id" name="control_area_id">
							<xsl:for-each select="control_areas_array">
								<xsl:choose>
									<xsl:when test="id != $control_area_id">
										<option value="{id}">
											<xsl:value-of disable-output-escaping="yes" select="title"/>
										</option>
									</xsl:when>
									<xsl:otherwise>
										<option value="{id}" selected="selected">
											<xsl:value-of disable-output-escaping="yes" select="title"/>
										</option>
									</xsl:otherwise>
								</xsl:choose>								
							</xsl:for-each>
						</select>
						<select id="control_area_id" name="control_area_id">
							<xsl:apply-templates select="control_areas_array2/options"/>
						</select>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/control_area_name" />
					</xsl:otherwise>
				</xsl:choose>
				</dd>
				<dt>
					<label>Prosedyre</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<select id="procedure_id" name="procedure_id">
							<xsl:for-each select="procedures_array">
								<xsl:choose>
									<xsl:when test="id != $control_procedure_id">
										<option value="{id}">
											<xsl:value-of disable-output-escaping="yes" select="title"/>
										</option>
									</xsl:when>
									<xsl:otherwise>
										<option value="{id}" selected="selected">
											<xsl:value-of disable-output-escaping="yes" select="title"/>
										</option>
									</xsl:otherwise>
								</xsl:choose>								
							</xsl:for-each>
						</select>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/procedure_name" />
					</xsl:otherwise>
				</xsl:choose>
				</dd>
				<dt>
					<label for="title">Tittel</label>
				</dt>
				<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<input type="text" name="title" id="title" value="{control/title}" size="80"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="control/title" />
						</xsl:otherwise>
					</xsl:choose>
				</dd>
				<dt>
					<label for="start_date">Startdato</label>
				</dt>
				<dd>
					<input>
				      <xsl:attribute name="id">start_date</xsl:attribute>
				      <xsl:attribute name="name">start_date</xsl:attribute>
				      <xsl:attribute name="type">text</xsl:attribute>
				      <xsl:if test="control/start_date != ''">
				      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(control/start_date))"/></xsl:attribute>
				      </xsl:if>
				    </input>
				</dd>
				<dt>
					<label for="end_date">Sluttdato</label>
				</dt>
				<dd>
					<input>
				      <xsl:attribute name="id">end_date</xsl:attribute>
				      <xsl:attribute name="name">end_date</xsl:attribute>
				      <xsl:attribute name="type">text</xsl:attribute>
				      <xsl:if test="control/end_date != 0">
				      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(control/end_date))"/></xsl:attribute>
				      </xsl:if>
				    </input>
				</dd>
				<dt>
					<label>Frekvenstype</label>
				</dt>
				<dd>
					<select id="repeat_type" name="repeat_type">
						<option value="0">Ikke angitt</option>
						<option value="1">Dag</option>
						<option value="2">Uke</option>
						<option value="3">Måned</option>
						<option value="5">År</option>
					</select>
				</dd>
				<dt>
					<label>Frekvens</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<input size="2" type="text" name="repeat_interval" value="{control/repeat_interval}" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/repeat_interval" />
					</xsl:otherwise>
				</xsl:choose>
				</dd>
				<dt>
					<label>Rolle</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<select id="responsibility_id" name="responsibility_id">
							<xsl:for-each select="role_array">
								<option value="{id}">
									<xsl:value-of disable-output-escaping="yes" select="name"/>
								</option>
							</xsl:for-each>
						</select>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/role_name" />
					</xsl:otherwise>
				</xsl:choose>
				</dd>
				<dt>
					<label for="description">Beskrivelse</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<textarea cols="70" rows="5" name="description" id="description"><xsl:value-of select="control/description" /></textarea>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/description" disable-output-escaping="yes"/>
					</xsl:otherwise>
				</xsl:choose>
				</dd>
			</dl>
	</fieldset>

	</div>
</div>
</xsl:template>
