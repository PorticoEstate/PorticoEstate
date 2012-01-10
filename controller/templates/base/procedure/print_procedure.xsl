<!-- $Id: procedure_item.xsl 8503 2012-01-06 08:13:27Z erikhl $ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="dateformat"><xsl:value-of select="dateformat" /></xsl:variable>

<div style="margin:40px 0 0 40px;">
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
					<input type="text" name="reference" id="reference" value="{procedure/reference}"  />
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="procedure/reference" />
				</xsl:otherwise>
			</xsl:choose>
			</dd>	
			<dt>
			<label for="attachment"><xsl:value-of select="php:function('lang','Procedure Attachment')" /></label>
			</dt>
			<dd>
			<xsl:choose>
				<xsl:when test="editable">
					<input type="text" name="attachment" id="attachment" value="{procedure/attachment}"  />
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="procedure/attachment" />
				</xsl:otherwise>
			</xsl:choose>
			</dd>			
		</dl>		
</div>
<style>
		.btn{
			background: none repeat scroll 0 0 #2647A0;
		    color: #FFFFFF;
		    display: inline-block;
		    margin-right: 5px;
		    padding: 5px 10px;
		    text-decoration: none;
		    border: 1px solid #173073;
		    cursor: pointer;
		}
		
		ul{
			list-style: none outside none;
		}
		
		li{
			list-style: none outside none;
		}
		
		ul.groups li {
		    padding: 3px 0;
		}
		
		ul.groups li.odd{
		    background: none repeat scroll 0 0 #DBE7F5;
		}
		
		ul.groups h3 {
		    font-size: 18px;
		    margin: 0 0 5px;
		}
		
		</style>
		<a style="margin:20px 0 0 40px;" href="#print" class="btn" onClick="window.print()">Skriv ut</a>
</xsl:template>