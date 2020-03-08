<!-- $Id$ -->

<xsl:template match="add_ticket" xmlns:php="http://php.net/xsl">
	<!--FIX ME...-->
		<br/>
		<br/>

	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<xsl:call-template name="msgbox"/>
		</xsl:when>
	</xsl:choose>

	<form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
		<div id="details">
			<xsl:if test="noform != 1">
				<div class="pure-form pure-form-aligned">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'category')" />
						</label>
						<select name="values[cat_id]" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'category')" />
							</xsl:attribute>
							<xsl:apply-templates select="category_list"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'subject')" />
						</label>
						<input type="text" name="values[title]" value="{title}"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'locationdesc')" />
						</label>
						<input type="text" name="values[locationdesc]" value="{locationdesc}"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'description')" />
						</label>
						<textarea cols="50" rows="10" name="values[description]" wrap="virtual">
							<xsl:value-of select="description"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'file')" />
						</label>
						<input type="file" name="file" size="50">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'file')" />
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<xsl:variable name="lang_send">
							<xsl:value-of select="php:function('lang', 'send')" />
						</xsl:variable>
						<label>
							<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_send}" title='{$lang_send}'/>
						</label>
					</div>
				</div>
				<div class="ticket_content attributes">
					<xsl:apply-templates select="custom_attributes/attributes"/>
				</div>
			</xsl:if>
		</div>
	</form>
</xsl:template>

<xsl:template match="category_list">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>


