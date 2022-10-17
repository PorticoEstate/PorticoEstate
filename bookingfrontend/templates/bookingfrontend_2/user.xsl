<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<div class="container wrapper">
		<div class="col mb-4">
			<xsl:call-template name="msgbox"/>
		</div>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="user/tabs"/>
			<input type="hidden" name="tab" value=""/>
			<div id="user" class="booking-container">
				<fieldset class="border p-2">
					<legend  class="w-auto">
						<xsl:value-of select="php:function('lang', 'saved data')" />
					</legend>
					<table class="table table-hover">
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'name')" />
							</td>
							<td>
								<xsl:value-of select="user/name"/>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'ssn')" />
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="substring (user/customer_ssn, 1, 4) != '0000'">
										<xsl:value-of select="substring (user/customer_ssn, 1, 6)" />
										<xsl:text>*****</xsl:text>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="user/customer_ssn" />
									</xsl:otherwise>
								</xsl:choose>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'Customer number')" />
							</td>
							<td>
								<xsl:if test="user/customer_number and normalize-space(user/customer_number)">
									<span class="form-control">
										<xsl:value-of select="user/customer_number" />
									</span>
								</xsl:if>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'Homepage')" />
							</td>
							<td>
								<xsl:if test="user/homepage and normalize-space(user/homepage)">
									<a target="blank" href="{user/homepage}" class="form-control">
										<xsl:value-of select="user/homepage" />
									</a>
								</xsl:if>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'Email')" />
							</td>
							<td>
								<a href="mailto:{user/email}">
									<xsl:value-of select="user/email"/>
								</a>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'Phone')" />
							</td>
							<td>
								<xsl:value-of select="user/phone"/>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'Street')" />
							</td>
							<td>
								<xsl:value-of select="user/street"/>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'Zip code')" />
							</td>
							<td>
								<xsl:value-of select="user/zip_code"/>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'Postal City')" />
							</td>
							<td>
								<xsl:value-of select="user/city"/>
							</td>
						</tr>
					</table>

				</fieldset>
					<button class="btn btn-secondary mt-2" onclick="window.location.href='{user/edit_link}';">
						<xsl:value-of select="php:function('lang', 'Edit')" />
					</button>
					<input type="button" class="btn btn-secondary mt-2 ml-3" name="cancel">
						<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="user/cancel_link"/>"</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'Cancel')" />
						</xsl:attribute>
					</input>
			</div>

			<div id="applications">
				<xsl:for-each select="user/datatable_def">
					<xsl:if test="container = 'datatable-container_0'">
						<xsl:call-template name="table_setup">
							<xsl:with-param name="container" select ='container'/>
							<xsl:with-param name="requestUrl" select ='requestUrl'/>
							<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
							<xsl:with-param name="data" select ='data'/>
							<xsl:with-param name="config" select ='config'/>
							<xsl:with-param name="class" select="'table table-striped table-bordered'" />
						</xsl:call-template>
					</xsl:if>
				</xsl:for-each>
			</div>
			<div id="invoice">
				<xsl:for-each select="user/datatable_def">
					<xsl:if test="container = 'datatable-container_1'">
						<xsl:call-template name="table_setup">
							<xsl:with-param name="container" select ='container'/>
							<xsl:with-param name="requestUrl" select ='requestUrl'/>
							<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
							<xsl:with-param name="data" select ='data'/>
							<xsl:with-param name="config" select ='config'/>
							<xsl:with-param name="class" select="'table table-striped table-bordered'" />
						</xsl:call-template>
					</xsl:if>
				</xsl:for-each>
			</div>
			<xsl:if test="user/delegate_data = 1">
				<div id="delegate">
					<xsl:for-each select="user/datatable_def">
						<xsl:if test="container = 'datatable-container_2'">
							<xsl:call-template name="table_setup">
								<xsl:with-param name="container" select ='container'/>
								<xsl:with-param name="requestUrl" select ='requestUrl'/>
								<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
								<xsl:with-param name="data" select ='data'/>
								<xsl:with-param name="config" select ='config'/>
								<xsl:with-param name="class" select="'table table-striped table-bordered'" />
							</xsl:call-template>
						</xsl:if>
					</xsl:for-each>
				</div>
			</xsl:if>
		</div>
	</div>
</xsl:template>
