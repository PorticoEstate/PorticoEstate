<!-- $Id$ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>

	<!-- ==================  ADD CHECKLIST  ========================= -->
	<div id="main_content" class="medium">
		
		<!-- ==================  CHECK LIST TOP SECTION  ===================== -->
		<xsl:call-template name="check_list_top_section" />
	
		<!-- ==================  CHECKLIST DETAILS  ===================== -->
		<div id="check_list_details">
			<h3 class="box_header">Sjekklistedetaljer</h3>
		
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicheck_list.save_check_list')" />
			</xsl:variable>
		
			<form id="frm_add_check_list" action="{$action_url}" method="post">
				<xsl:variable name="control_id">
					<xsl:value-of select="control/id"/>
				</xsl:variable>
				<input type="hidden" name="control_id" value="{$control_id}" />
				<xsl:variable name="type">
					<xsl:value-of select="type"/>
				</xsl:variable>
				<input type="hidden" name="type" value="{$type}" />

				<xsl:variable name="location_code">
					<xsl:value-of select="location_array/location_code"/>
				</xsl:variable>		

				<xsl:choose>
					<xsl:when test="type = 'component'">
						<xsl:variable name="location_id">
							<xsl:value-of select="check_list/location_id"/>
						</xsl:variable>
						<input type="hidden" name="location_id" value="{$location_id}" />
						<xsl:variable name="component_id">
							<xsl:value-of select="check_list/component_id"/>
						</xsl:variable>
						<input type="hidden" name="component_id" value="{$component_id}" />
						<input type="hidden" name="location_code" value="{$location_code}" />
					</xsl:when>
					<xsl:otherwise>
						<input type="hidden" name="location_code" value="{$location_code}" />
					</xsl:otherwise>
				</xsl:choose>
			
				<fieldset>
					<!-- STATUS -->
					<div class="row">
						<xsl:if test="check_list/error_msg_array/status != ''">
							<xsl:variable name="error_msg">
								<xsl:value-of select="check_list/error_msg_array/status" />
							</xsl:variable>
							<div class='input_error_msg'>
								<xsl:value-of select="php:function('lang', $error_msg)" />
							</div>
						</xsl:if>
						<label>Status</label>
						<xsl:variable name="status">
							<xsl:value-of select="check_list/status"/>
						</xsl:variable>
						<select id="status" name="status">
							<xsl:choose>
								<xsl:when test="check_list/status = 0">
									<option value="1">Utført</option>
									<option value="0" SELECTED="SELECTED">Ikke utført</option>
									<option value="3">Kansellert</option>
								</xsl:when>
								<xsl:when test="check_list/status = 1">
									<option value="1" SELECTED="SELECTED">Utført</option>
									<option value="0">Ikke utført</option>
									<option value="3">Kansellert</option>
								</xsl:when>
								<xsl:when test="check_list/status = 3">
									<option value="3" SELECTED="SELECTED">Kansellert</option>
									<option value="0">Ikke utført</option>
									<option value="1">Utført</option>
								</xsl:when>
								<xsl:otherwise>
									<option value="0" SELECTED="SELECTED">Ikke utført</option>
									<option value="1">Utført</option>
									<option value="3">Kansellert</option>
								</xsl:otherwise>
							</xsl:choose>
						</select>
					</div>
					<!-- DEADLINE -->
					<div class="row">
						<xsl:if test="check_list/error_msg_array/deadline != ''">
							<xsl:variable name="error_msg">
								<xsl:value-of select="check_list/error_msg_array/deadline" />
							</xsl:variable>
							<div class='input_error_msg'>
								<xsl:value-of select="php:function('lang', $error_msg)" />
							</div>
						</xsl:if>
						<label>Fristdato</label>
						<input type="text" id="deadline_date" name="deadline_date" class="date" readonly="readonly" >
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('date', $date_format, number(check_list/deadline))"/>
							</xsl:attribute>
						</input>
					</div>
					<!-- PLANNED DATE -->
					<div class="row">
						<label>Planlagt dato</label>
						<input type="text" id="planned_date" name="planned_date" class="date" readonly="readonly">
							<xsl:if test="check_list/planned_date != 0 and check_list/planned_date != ''">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('date', $date_format, number(check_list/planned_date))"/>
								</xsl:attribute>
							</xsl:if>
						</input>
					</div>
					<!-- COMPLETED DATE -->
					<div class="row">
						<xsl:if test="check_list/error_msg_array/completed_date != ''">
							<xsl:variable name="error_msg">
								<xsl:value-of select="check_list/error_msg_array/completed_date" />
							</xsl:variable>
							<div class='input_error_msg'>
								<xsl:value-of select="php:function('lang', $error_msg)" />
							</div>
						</xsl:if>
						<label>Utført dato</label>
						<input type="text" id="completed_date" name="completed_date" class="date" readonly="readonly" >
							<xsl:if test="check_list/completed_date != 0 and check_list/completed_date != ''">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/>
								</xsl:attribute>
							</xsl:if>
						</input>
					</div>
				</fieldset>
				<!-- ASSIGNMET -->
				<div class="row">
					<label>Tildelt</label>
					<select name="assigned_to">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'select')"/>
						</xsl:attribute>
						<option value="0">
							<xsl:value-of select="php:function('lang', 'select')"/>
						</option>
						<xsl:apply-templates select="user_list/options"/>
					</select>
				</div>
				<!-- COMMENT -->
				<div class="comment">
					<label>Kommentar</label>
					<textarea>
						<xsl:attribute name="name">comment</xsl:attribute>
						<xsl:value-of select="check_list/comment"/>
					</textarea>
				</div>
			
				<div class="form-buttons">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save_check_list')" />
					</xsl:variable>
					<input class="btn" type="submit" value="Lagre detaljer" />
				</div>
			</form>	
		</div>
	</div>
</xsl:template>

	<!-- New template-->
	<xsl:template match="options">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"/>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>
