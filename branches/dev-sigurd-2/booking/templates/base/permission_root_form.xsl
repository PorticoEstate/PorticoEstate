<xsl:template match="data">
	<div id="content">
		<xsl:call-template name="msgbox"/>
		<xsl:apply-templates select="permission"/>
	</div>
</xsl:template>

<xsl:template match="data/permission" xmlns:php="http://php.net/xsl">

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
				<xsl:text>Cancel</xsl:text>
			</a>
		</div>
	</form>
</xsl:template>
