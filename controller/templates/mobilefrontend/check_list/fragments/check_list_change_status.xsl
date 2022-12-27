<xsl:template name="check_list_change_status" xmlns:php="http://php.net/xsl">
	<xsl:param name="active_tab" />

	<div class="row mt-3">
		<div id="check_list_change_status" class="container">
			<!-- ==================  CHANGE STATUS FOR CHECKLIST  ===================== -->
			<xsl:choose>
				<xsl:when test="check_list/id != 0 and $active_tab != 'view_details'">
					<div class="col-12">
						<xsl:variable name="action_url">
							<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicheck_list.update_status,phpgw_return_as:json')" />
						</xsl:variable>
						<form id="update-check-list-status" class="done form-inline d-grid" action="{$action_url}" method="post">
							<input type="hidden" name="check_list_id" value="{check_list/id}" />
							<xsl:choose>
								<xsl:when test="check_list/status = 0">
										<label>
											<xsl:value-of select="php:function('lang', 'check list status')" />:
										</label>
										<span id="status_text">
											<xsl:value-of select="php:function('lang', 'not performed')" />
										</span>
									<input id='update-check-list-status-value' type="hidden" name="status" value="1" />
									<input id="status_submit" type="submit" class="mt-3 btn btn-warning d-flex">
										<xsl:attribute name="value">
											<xsl:value-of select="php:function('lang', 'change status')" />
										</xsl:attribute>
									</input>
								</xsl:when>
								<xsl:otherwise>
										<label>
											<xsl:value-of select="php:function('lang', 'check list status')" />:
										</label>
										<span id="status_text">
											<xsl:value-of select="php:function('lang', 'performed')" />
										</span>
									<input id='update-check-list-status-value' type="hidden" name="status" value="0" />
									<input type="submit" class="mt-3 btn btn-success btn-block">
										<xsl:attribute name="value">
											<xsl:value-of select="php:function('lang', 'change status')" />
										</xsl:attribute>
									</input>
								</xsl:otherwise>
							</xsl:choose>
						</form>
					</div>
				</xsl:when>
			</xsl:choose>
		</div>
	</div>

</xsl:template>
