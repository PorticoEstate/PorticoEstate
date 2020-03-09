<!-- $Id$ -->

<xsl:template match="add_ticket" xmlns:php="http://php.net/xsl">
	<div class="container">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<div class="d-flex flex-row">
					<xsl:call-template name="msgbox"/>
				</div>
			</xsl:when>
		</xsl:choose>


		<form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}" class="was-validated">
			<xsl:if test="noform != 1">
				<fieldset class="border p-2">
					<legend  class="w-auto">
						<xsl:value-of select="php:function('lang', 'ticket')" />
					</legend>
					<div class="form-group">
						<label>
							<xsl:value-of select="php:function('lang', 'category')" />
						</label>
						<select name="values[cat_id]" required="required" class="form-control">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'category')" />
							</xsl:attribute>
							<xsl:apply-templates select="category_list"/>
						</select>
					</div>
					<div class="form-group">
						<label>
							<xsl:value-of select="php:function('lang', 'subject')" />
						</label>
						<input type="text" name="values[title]" value="{title}" required="required" class="form-control">
						</input>
					</div>
					<div class="form-group">
						<label>
							<xsl:value-of select="php:function('lang', 'locationdesc')" />
						</label>
						<input type="text" name="values[locationdesc]" value="{locationdesc}" required="required" class="form-control"></input>
					</div>
					<div class="form-group">
						<label>
							<xsl:value-of select="php:function('lang', 'description')" />
						</label>
						<textarea cols="50" rows="10" name="values[description]" wrap="virtual" required="required" class="form-control">
							<xsl:value-of select="description"/>
						</textarea>
					</div>
					<div class="form-group">
						<label>
							<xsl:value-of select="php:function('lang', 'file')" />
						</label>
						<input type="file" name="file" size="50" class="form-control-file">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'file')" />
							</xsl:attribute>
						</input>
					</div>
					<div class="ticket_content attributes">
						<xsl:apply-templates select="custom_attributes/attributes"/>
					</div>
				</fieldset>

				<div class="form-group mt-2">
					<xsl:variable name="lang_send">
						<xsl:value-of select="php:function('lang', 'send')" />
					</xsl:variable>
					<label>
						<input type="submit" class="btn btn-primary" name="values[save]" value="{$lang_send}" title='{$lang_send}'/>
					</label>
				</div>
			</xsl:if>

		</form>
	</div>
</xsl:template>

<xsl:template match="category_list">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>


