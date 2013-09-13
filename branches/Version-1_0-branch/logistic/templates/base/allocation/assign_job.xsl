<!-- $Id: edit_check_list.xsl 8374 2011-12-20 07:45:04Z vator $ -->
<xsl:template match="data" name="assign_job" xmlns:php="http://php.net/xsl">

<div id="main_content" class="medium">
	
	<div id="check-list-heading">
		<div class="box">
			<h1>
				<xsl:value-of select="php:function('lang', 'assign ticket')" />
			</h1>
			<h2>
				<xsl:value-of select="requirement_descr"/>
			</h2>

		</div>
		
	</div>
	
		<!-- =======================  INFO ABOUT MESSAGE  ========================= -->
		<div id="caseMessage" class="box ext">
			<xsl:choose>
				<xsl:when test="assign_requirement_json !=''">
				
				<xsl:variable name="action_url">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:logistic.uirequirement.send_job_ticket')" />
				</xsl:variable>
		
			<script type="text/javascript">
				var requirement_id = <xsl:value-of select="requirement_id"/>;
			</script>

				<form ENCTYPE="multipart/form-data" id="frmRegCaseMessage" action="{$action_url}" method="post">
					
					<input name="assign_requirement" type="text" value="{assign_requirement_json}" />					
					
					<!-- === TITLE === -->

				    <table>
				    	<tr>
				    		<td>
								<div class="row">
									<label>
										<xsl:value-of select="php:function('lang', 'title')" />
									</label>
									<input name="message_title" type="text" class="required" value = '{title}' size= '{title_size}'/>
								</div>
							</td>
						</tr>
				    	<tr>
				    		<td>
								<!-- === CATEGORY === -->
								<div class="row">
									<label>
										<xsl:value-of select="php:function('lang', 'category')" />
									</label>
									 <select name="message_cat_id" class="required">
									 	<option value="0"><xsl:value-of select="php:function('lang', 'select')" /></option>
										<xsl:for-each select="categories/cat_list">
											<xsl:variable name="cat_id"><xsl:value-of select="./cat_id"/></xsl:variable>
											<option value="{$cat_id}">
												<xsl:value-of select="./name"/>
											</option>			
										</xsl:for-each>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="row">
									<label>
										<xsl:value-of select="php:function('lang', 'Priority')"/>
									</label>
									<select name="priority" >
										<xsl:attribute name="title"><xsl:value-of select="php:function('lang', 'select')"/></xsl:attribute>
										<xsl:apply-templates select="priority_list/options"/>
									</select>
									</div>
							</td>
						</tr>
				    	<tr>
				    		<td>
								<div class="row">
									<label>
										<xsl:value-of select="php:function('lang', 'message')" />
									</label>
									<textarea name="message" cols="60" rows="10">
										<xsl:value-of select="message"/>
									</textarea>
								</div>
					<!-- === UPLOAD FILE === -->
							</td>
						</tr>
				    	<tr>
				    		<td>
								<div class="row">
									<label>Filvedlegg:</label>
									<input type="file" id="file" name="file" >
										<xsl:attribute name="accept">image/*</xsl:attribute>
										<xsl:attribute name="capture">camera</xsl:attribute>				    
									</input>
								</div>
							</td>
						</tr>
					</table>					
					<div class="form-buttons">
						<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
						<input class="btn" type="submit" name="save_control" value="Send melding" title="{$lang_save}" />
					</div>
				</form>			
				</xsl:when>
				<xsl:otherwise>
					nothing..
				</xsl:otherwise>
			</xsl:choose>
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

