<!-- $Id$ -->
<xsl:template match="data" name="edit_check_list" xmlns:php="http://php.net/xsl">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<xsl:variable name="session_url">
		<xsl:text>&amp;</xsl:text>
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>

	<div id="main_content" class="medium">
		
		<xsl:call-template name="check_list_top_section">
			<xsl:with-param name="active_tab">view_details</xsl:with-param>
		</xsl:call-template>

			
		<!-- ==================  CHECKLIST DETAILS  ===================== -->
		<div id="check_list_details">
			<h3 class="box_header">Sjekklistedetaljer::<xsl:value-of select="current_month_name"/></h3>
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicheck_list.save_check_list')" />
			</xsl:variable>
			<form id="frm_update_check_list" action="{$action_url}" method="post">	
				<xsl:variable name="check_list_id">
					<xsl:value-of select="check_list/id"/>
				</xsl:variable>
				<input id="check_list_id" type="hidden" name="check_list_id" value="{$check_list_id}" />
				<xsl:if test="check_list_locked = '1'">
					<div class='input_error_msg'>
						<xsl:value-of select="php:function('lang', 'error_msg_control_passed_due_date')" />
					</div>
				</xsl:if>
				<fieldset class="col_1">
					<div class="row">
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
					<div class="row">
						<label>Skal utføres innen</label>
						<xsl:value-of select="php:function('date', $date_format, number(check_list/deadline))"/>
						<input id="deadline_date" name="deadline_date" type="hidden">
							<xsl:if test="check_list/deadline != 0 or check_list/deadline != ''">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('date', $date_format, number(check_list/deadline))"/>
								</xsl:attribute>
							</xsl:if>
						</input>
						<input type="hidden" id="original_deadline_date" name="original_deadline_date" >
							<xsl:attribute name="value">
								<xsl:value-of select="check_list/original_deadline"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="row">
						<xsl:if test="check_list/error_msg_array/planned_date != ''">
							<xsl:variable name="error_msg">
								<xsl:value-of select="check_list/error_msg_array/planned_date" />
							</xsl:variable>
							<div class='input_error_msg'>
								<xsl:value-of select="php:function('lang', $error_msg)" />
							</div>
						</xsl:if>
						<label>Planlagt dato</label>
						<input class="date" readonly="readonly">
							<xsl:attribute name="id">planned_date</xsl:attribute>
							<xsl:attribute name="name">planned_date</xsl:attribute>
							<xsl:attribute name="type">text</xsl:attribute>
							<xsl:if test="check_list/planned_date != 0 and check_list/planned_date != ''">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('date', $date_format, number(check_list/planned_date))"/>
								</xsl:attribute>
							</xsl:if>
						</input>
					</div>
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
						<input class="date" >
							<xsl:attribute name="id">completed_date</xsl:attribute>
							<xsl:attribute name="name">completed_date</xsl:attribute>
							<xsl:attribute name="type">text</xsl:attribute>
							<xsl:if test="check_list/completed_date != 0 and check_list/completed_date != ''">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/>
								</xsl:attribute>
							</xsl:if>
						</input>
					</div>
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
					<xsl:if test="required_actual_hours = 1">
						<div class="row">
							<label>Egne Timer</label>
							<input class="date">
								<xsl:attribute name="id">billable_hours</xsl:attribute>
								<xsl:attribute name="name">billable_hours</xsl:attribute>
								<xsl:attribute name="type">text</xsl:attribute>
							</input>
							<xsl:text> </xsl:text>
							<xsl:value-of select="check_list/billable_hours"/>
						</div>
					</xsl:if>
				</fieldset>
				<fieldset class="col_2">
					<div class="row">
						<label>Antall åpne saker</label>
						<xsl:value-of select="check_list/num_open_cases"/>
					</div>
					<div class="row">
						<label>Antall ventende saker</label>
						<xsl:value-of select="check_list/num_pending_cases"/>
					</div>
				</fieldset>
		    
				<div class="comment">
					<label>Kommentar</label>
					<textarea>
						<xsl:attribute name="name">comment</xsl:attribute>
						<xsl:value-of select="check_list/comment"/>
					</textarea>
				</div>

				<div class="comment">
					<label>
						<xsl:value-of select="php:function('lang', 'files')"/>
					</label>
					<div class="pure-u pure-custom" >
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_0'">
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl'/>
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
									<xsl:with-param name="data" select ='data'/>
									<xsl:with-param name="tabletools" select ='tabletools' />
									<xsl:with-param name="config" select ='config'/>
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</div>
				</div>
				<script type="text/javascript">
					var multi_upload_parans = <xsl:value-of select="multi_upload_parans"/>;
				</script>
				<div class="comment">
					<xsl:call-template name="file_upload"/>
				</div>

				<xsl:if test="check_list_locked != '1'">
					<div class="form-buttons">
						<xsl:variable name="lang_save">
							<xsl:value-of select="php:function('lang', 'save_check_list')" />
						</xsl:variable>
						<input class="btn" type="submit" name="save_control" value="Lagre detaljer" />
					</div>
				</xsl:if>
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
