<!-- $Id$ -->
<!-- item  -->

<xsl:template name="view_procedure" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_booking_i18n"/>
<xsl:variable name="dateformat"><xsl:value-of select="dateformat" /></xsl:variable>

<div class="yui-content">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value = "{value_id}">
				</input>
				<dl class="proplist-col">
					<dt>
						<label for="title"><xsl:value-of select="php:function('lang','Procedure title')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<input type="text" name="title" id="title" value="{procedure/title}" size="100"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="procedure/title" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="revision_no"><xsl:value-of select="php:function('lang','Procedure revision')" /></label>
					</dt>
					<dd>
						<xsl:value-of select="procedure/revision_no" />
					</dd>
					<dt>
						<label for="control_area"><xsl:value-of select="php:function('lang','Control area')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<select id="control_area" name="control_area">
								<xsl:apply-templates select="control_area/options"/>
							</select>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="procedure/control_area_name" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="start_date"><xsl:value-of select="php:function('lang','Procedure start date')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:value-of disable-output-escaping="yes" select="start_date"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="startdate"><xsl:value-of select="procedure/start_date" /></xsl:variable>
							<xsl:value-of select="php:function('date', $dateformat, $startdate)" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="revision_date"><xsl:value-of select="php:function('lang','Procedure revision date')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:value-of disable-output-escaping="yes" select="revision_date"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:if test="procedure/revision_date != 0">
								<xsl:variable name="revisiondate"><xsl:value-of select="procedure/revision_date" /></xsl:variable>
								<xsl:value-of select="php:function('date', $dateformat, $revisiondate)" />
							</xsl:if>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<xsl:if test="procedure/end_date != 0">
					<dt>
						<label for="end_date"><xsl:value-of select="php:function('lang','Procedure end date')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:value-of disable-output-escaping="yes" select="end_date"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="enddate"><xsl:value-of select="procedure/end_date" /></xsl:variable>
							<xsl:value-of select="php:function('date', $dateformat, $enddate)" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					</xsl:if>
					<dt>
						<label for="purpose"><xsl:value-of select="php:function('lang','Procedure purpose')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<textarea id="purpose" name="purpose" rows="5" cols="60"><xsl:value-of select="procedure/purpose" disable-output-escaping="yes"/></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="procedure/purpose" disable-output-escaping="yes"/>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="responsibility"><xsl:value-of select="php:function('lang','Procedure responsibility')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<textarea id="responsibility" name="responsibility" rows="5" cols="60"><xsl:value-of select="procedure/responsibility" /></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="procedure/responsibility" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="description"><xsl:value-of select="php:function('lang','Procedure description')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<textarea id="description" name="description" rows="5" cols="60"><xsl:value-of select="procedure/description" disable-output-escaping="yes"/></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="procedure/description" disable-output-escaping="yes"/>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="reference"><xsl:value-of select="php:function('lang','Procedure Reference')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<textarea id="reference" name="reference" rows="5" cols="60"><xsl:value-of select="procedure/reference" disable-output-escaping="yes"/></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="procedure/reference" disable-output-escaping="yes"/>
						</xsl:otherwise>
					</xsl:choose>
					</dd>				
				</dl>
				
				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
							<xsl:variable name="lang_revisit"><xsl:value-of select="php:function('lang', 'revisit')" /></xsl:variable>
							<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
							<input type="submit" name="save_procedure" value="{$lang_save}" title = "{$lang_save}" />
							<input type="submit" name="revisit_procedure" value="{$lang_revisit}" title = "{$lang_revisit}" />
							<input type="submit" name="cancel_procedure" value="{$lang_cancel}" title = "{$lang_cancel}" />
						</xsl:when>
						<xsl:when test="inactive">
							<xsl:variable name="lang_back"><xsl:value-of select="php:function('lang', 'back')" /></xsl:variable>
							<input type="button" value="{$lang_back}" title="{$lang_back}" onclick="javascript: history.go(-1);" class="btn"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<input type="submit" name="edit_procedure" value="{$lang_edit}" title = "{$lang_edit}" />
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</form>
			<xsl:choose>
				<xsl:when test="values != ''">
					<table cellpadding="10" cellspacing="10" align="left">
						<xsl:call-template name="table_header_history"/>
						<xsl:call-template name="values_history"/>
					</table>
				</xsl:when>
			</xsl:choose>
		</div>
	</div>
</xsl:template>

<xsl:template name="table_header_history">
		<tr class="th">
			<xsl:for-each select="table_header" >
				<td class="th_text" style="padding-right: 10px;">
					<xsl:value-of select="header"/>
				</td>
			</xsl:for-each>
		</tr>
	</xsl:template>

	<xsl:template name="values_history">
		<xsl:for-each select="values" >
			<tr>
				<xsl:for-each select="row" >
					<xsl:variable name="proc_link"><xsl:value-of select='link'/></xsl:variable>
					<td align="right" style="padding-right: 10px;">
						<a href="{$proc_link}"><xsl:value-of select="revision_no"/></a>
					</td>
					<td align="left" style="padding-right: 10px;">
						<xsl:value-of select="title"/>
					</td>
					<td align="left" style="padding-right: 10px;">
						<xsl:value-of select="start_date"/>
					</td>
					<td align="left" style="padding-right: 10px;">
						<xsl:value-of select="end_date"/>
					</td>
				</xsl:for-each>
			</tr>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template match="options">
		<option value="{id}">
			<xsl:if test="selected">
				<xsl:attribute name="selected" value="selected" />
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>
