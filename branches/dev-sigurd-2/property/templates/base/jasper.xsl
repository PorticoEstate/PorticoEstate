<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	

<!-- add / edit  -->
	<xsl:template match="edit" xmlns:php="http://php.net/xsl">
		<div align="left">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$form_action}">		
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
				<xsl:choose>
					<xsl:when test="value_id != ''">
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'id')" />
							</td>
							<td>
								<xsl:value-of select="value_id"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>	
				<tr>
					<td valign="top">
						<xsl:value-of select="php:function('lang', 'file')" />
						<input type="hidden"  name="values[file_name_orig]" value="{value_file_name_orig}" />
					</td>
					<td>
						<input type="file" size="50" name="file">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'upload file')" />
							</xsl:attribute>
						</input>
					</td>
				</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'descr')" />
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[descr]" wrap="virtual">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'descr')" />
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>		
					</textarea>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="php:function('lang', 'location')" />
				</td>
				<td>
					<select name="values[location]">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Select submodule')" />
						</xsl:attribute>
						<option value="">
							<xsl:value-of select="php:function('lang', 'No location')" />
						</option>
						<xsl:apply-templates select="location_list"/>
					</select>			
				</td>

			</tr>
			<tr>
				<td>
					<xsl:value-of select="php:function('lang', 'input type')" />
				</td>
				<td>
					<select name="values[input type]">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'input type')" />
						</xsl:attribute>
						<option value="">
							<xsl:value-of select="php:function('lang', 'input type')" />
						</option>
						<xsl:apply-templates select="input_type_list"/>
					</select>			
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="php:function('lang', 'input name')" />
				</td>
				<td>
					<input type="text" name="values[input_name]" value="{value_input_name}" size="12" >
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'input name')" />
						</xsl:attribute>
					</input>			
				</td>
			</tr>
		</table>
		<table cellpadding="2" cellspacing="2" width="50%" align="center">
			<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
			<xsl:variable name="lang_apply"><xsl:value-of select="php:function('lang', 'apply')" /></xsl:variable>
			<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
			<tr height="50">
				<td>
					<input type="submit" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'save')" />
						</xsl:attribute>
					</input>
				</td>
				<td>
					<input type="submit" name="values[apply]" value="{$lang_apply}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'apply')" />
						</xsl:attribute>
					</input>
				</td>
				<td>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'cancel')" />
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>

	<xsl:template match="input_type_list">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected" />
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>

	<xsl:template match="location_list">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected" />
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>

