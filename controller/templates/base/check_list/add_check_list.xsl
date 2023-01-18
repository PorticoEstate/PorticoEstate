<!-- $Id$ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<xsl:variable name="serie_id">
		<xsl:value-of select="serie_id" />
	</xsl:variable>


	<!-- ==================  ADD CHECKLIST  ========================= -->
	<div id="main_content" class="medium">
		
		<!-- ==================  CHECK LIST TOP SECTION  ===================== -->
		<xsl:call-template name="check_list_top_section" >
			<xsl:with-param name="active_tab">view_details</xsl:with-param>
		</xsl:call-template>
	
		<!-- ==================  CHECKLIST DETAILS  ===================== -->
		<div id="check_list_details">
		
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicheck_list.save_check_list')" />
			</xsl:variable>
		
			<form id="frm_add_check_list" action="{$action_url}" method="post" class="pure-form pure-form-stacked">
				<fieldset>
					<legend>
						<h3>Sjekklistedetaljer::<xsl:value-of select="current_month_name"/></h3>
					</legend>
					<xsl:variable name="control_id">
						<xsl:value-of select="control/id"/>
					</xsl:variable>
					<input type="hidden" name="control_id" value="{$control_id}" />
					<input type="hidden" name="serie_id" value="{$serie_id}" />
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
			
					<!-- STATUS -->
					<div class="pure-control-group">
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
						<select id="status" name="status" class="pure-input-1">
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
					<div class="pure-g">
						<div class="pure-u-1 pure-u-md-1-3">
							<!-- DEADLINE -->
							<div class="pure-control-group">
								<xsl:if test="check_list/error_msg_array/deadline != ''">
									<xsl:variable name="error_msg">
										<xsl:value-of select="check_list/error_msg_array/deadline" />
									</xsl:variable>
									<div class='input_error_msg'>
										<xsl:value-of select="php:function('lang', $error_msg)" />
									</div>
								</xsl:if>
								<label>Fristdato</label>
								<xsl:value-of select="php:function('date', $date_format, number(check_list/deadline))"/>
								<input type="hidden" id="deadline_date" name="deadline_date" >
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('date', $date_format, number(check_list/deadline))"/>
									</xsl:attribute>
								</input>
								<input type="hidden" id="original_deadline_date" name="original_deadline_date" >
									<xsl:attribute name="value">
										<xsl:value-of select="check_list/deadline"/>
									</xsl:attribute>
								</input>
							</div>
						</div>
						<div class="pure-u-1 pure-u-md-1-3">
							<!-- PLANNED DATE -->
							<div class="pure-control-group">
								<xsl:if test="check_list/error_msg_array/planned_date != ''">
									<xsl:variable name="error_msg">
										<xsl:value-of select="check_list/error_msg_array/planned_date" />
									</xsl:variable>
									<div class='input_error_msg'>
										<xsl:value-of select="php:function('lang', $error_msg)" />
									</div>
								</xsl:if>
								<label>Planlagt dato</label>
								<input type="text" id="planned_date" name="planned_date" class="date" readonly="readonly">
									<xsl:if test="check_list/planned_date != 0 and check_list/planned_date != ''">
										<xsl:attribute name="value">
											<xsl:value-of select="php:function('date', $date_format, number(check_list/planned_date))"/>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
						</div>
						<div class="pure-u-1 pure-u-md-1-3">
							<!-- COMPLETED DATE -->
							<div class="pure-control-group">
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
						</div>
					</div>
					<!-- ASSIGNMET -->
					<div class="pure-control-group">
						<label>Tildelt</label>
						<select name="assigned_to" class="pure-input-1">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</xsl:attribute>
							<option value="0">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:apply-templates select="user_list/options"/>
						</select>
					</div>
					<xsl:if test="required_actual_hours = 1">
						<div class="pure-control-group">
							<label>Egne Timer</label>
							<input type="number" step="0.01"  class="pure-input-1" required="required">
								<xsl:attribute name="id">billable_hours</xsl:attribute>
								<xsl:attribute name="name">billable_hours</xsl:attribute>
							</input>
							<xsl:text> </xsl:text>
							<xsl:value-of select="check_list/billable_hours"/>
						</div>
					</xsl:if>

					<!-- COMMENT -->
					<div class="pure-control-group">
						<label>Kommentar</label>
						<textarea class="pure-input-1">
							<xsl:attribute name="name">comment</xsl:attribute>
							<xsl:value-of select="check_list/comment"/>
						</textarea>
					</div>
					<div id="submit_group">
						<button  id="save_check_list" class="save_check_list pure-button pure-button-primary" type="submit" name="save_check_list" value="1">
							<i class="fa fa-floppy-o" aria-hidden="true"></i>
							<xsl:text> </xsl:text>
							<xsl:value-of select="php:function('lang', 'plan')" />
						</button>
						<button id="submit_ok" class="submit_ok pure-button pure-button-primary"  type="submit" name="submit_ok" value="1">
							<i class="fa fa-check-square-o" aria-hidden="true"></i>
							<xsl:text> </xsl:text>
							<xsl:value-of select="php:function('lang', 'performed without deviation')" />
						</button>
						<button id="submit_deviation" class="submit_deviation pure-button pure-button-primary" type="submit" name="submit_deviation" value="1">
							<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
							<xsl:text> </xsl:text>
							<xsl:value-of select="php:function('lang', 'deviation')" />
						</button>
					</div>
				</fieldset>
			</form>	
		</div>
		<xsl:for-each select="integration">
			<div id="{section}">
				<iframe id="{section}_content" width="100%" height="{height}" src="{src}">
					<p>Your browser does not support iframes.</p>
				</iframe>
			</div>
		</xsl:for-each>
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
