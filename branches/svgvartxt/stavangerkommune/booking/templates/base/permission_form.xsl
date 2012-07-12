<xsl:template match="data">
	<div id="content">
		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>
		<xsl:apply-templates select="permission"/>
	</div>
</xsl:template>

<xsl:template match="data/permission" xmlns:php="http://php.net/xsl">
	<script type="text/javascript">
		YAHOO.booking.objectType = "<xsl:value-of select="object_type"/>";
		YAHOO.booking.objectAutocomplete = <xsl:value-of select="inline"/> == 0;
	</script>

	<!-- Add pathway later -->
	<!--ul class="pathway">
	<li>
		<a>
			<xsl:attribute name="href"><xsl:value-of select="buildings_link"/></xsl:attribute>
			<xsl:value-of select="top-nav-bar-buildings" />
		</a>
	</li>
	<li><xsl:value-of select="php:function('lang', 'Buildings')"/></li>
	</ul-->

	<form action="" method="POST">    
		<dl class="form-col">
			<xsl:if test="id">
				<!-- An update, add id column -->
				<input name='field_id' type='hidden'>
					<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
				</input>
			</xsl:if>

			<!-- Role -->
			<dt>
				<label for="field_role"><xsl:value-of select="php:function('lang', 'Role')" /></label>
			</dt>
			<dd>
				<xsl:value-of select="node()"/>
				<select name='role' id='field_role'>
					<option value=''><xsl:value-of select="php:function('lang', 'Select role...')" /></option>
					<xsl:for-each select="available_roles/*">
						<option>
							<xsl:if test="../../role = local-name()">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>

							<xsl:attribute name="value"><xsl:value-of select="local-name()"/></xsl:attribute>
							<xsl:value-of select="php:function('lang', string(node()))"/>
						</option>
					</xsl:for-each>
				</select>
			</dd>

			<!-- Subject -->
			<dt>
				<label for="field_subject"><xsl:value-of select="php:function('lang', 'Account')" /></label>
			</dt>
			<dd>
				<div class="autocomplete">
					<input id="field_subject_name" name="subject_name" type="text">
						<xsl:attribute name="value"><xsl:value-of select="subject_name"/></xsl:attribute>
					</input>
					<input id="field_subject_id" name="subject_id" type="hidden">
						<xsl:attribute name="value"><xsl:value-of select="subject_id"/></xsl:attribute>
					</input>
					<div id="subject_container"/>
				</div>
			</dd>
		</dl>

		<dl class="form-col">
			<!-- Object -->
			<dt>
				<label for="field_object_name"><xsl:value-of select="php:function('lang', string(object_type_label))" /></label>
			</dt>
			<dd>
				<div class="autocomplete">
					<input id="field_object_name" name="object_name" type="text">
						<xsl:attribute name="value"><xsl:value-of select="object_name"/></xsl:attribute>
						<xsl:if test="inline = '1'">
							<xsl:attribute name="disabled">disabled</xsl:attribute>
						</xsl:if>
					</input>
					<input id="field_object_id" name="object_id" type="hidden">
						<xsl:attribute name="value"><xsl:value-of select="object_id"/></xsl:attribute>
					</input>
					<div id="object_container"/>
				</div>
			</dd>
		</dl>

		<div class="clr"/>
		<div class="form-buttons">
			<input type="submit">
				<xsl:attribute name="value">
					<xsl:choose>
						<xsl:when test="id">
							<xsl:value-of select="php:function('lang', 'Update')"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="php:function('lang', 'Create')"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
			</input>
			<a class="cancel">
				<xsl:attribute name="href"><xsl:value-of select="cancel_link"/></xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')"/>
			</a>
		</div>
	</form>
</xsl:template>