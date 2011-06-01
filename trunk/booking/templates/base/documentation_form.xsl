<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<script type="text/javascript">
		YAHOO.booking.documentOwnerType = "<xsl:value-of select="document/owner_type"/>";
		YAHOO.booking.documentOwnerAutocomplete = <xsl:value-of select="document/inline"/> == 0;
	</script>
	
    <div id="content">
    	<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

    	<form action="" method="POST" enctype='multipart/form-data'>
			<dl class="form">
				<xsl:if test="document/id">
					<dt class="heading"><xsl:value-of select="php:function('lang', 'Edit manual')" /></dt>
				</xsl:if>
				<xsl:if test="not(document/id)">
					<dt class="heading"><xsl:value-of select="php:function('lang', 'Upload manual')" /></dt>
				</xsl:if>
				<xsl:if test="document/id">
					<!-- An update, add id column -->
					<input name='field_id' type='hidden'>
						<xsl:attribute name="value"><xsl:value-of select="document/id"/></xsl:attribute>
					</input>
				</xsl:if>
				<dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Document')" /></label></dt>
	            <dd>
	                <input name="name" id='field_name'>
						<xsl:attribute name="value"><xsl:value-of select="document/name"/></xsl:attribute>
						<xsl:attribute name="type">
							<xsl:choose>
								<xsl:when test="document/id">text</xsl:when>
								<xsl:otherwise>file</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
	                    
						<xsl:if test="document/id">
							<xsl:attribute name="disabled" value="disabled"/>
						</xsl:if>
						
						<xsl:attribute name='title'><xsl:value-of select="document/name"/></xsl:attribute>
	                </input>
	            </dd>
			</dl>
			<dl class="form-col">

	            <dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
	            <dd>
	                <textarea name="description" id='field_description'><xsl:value-of select="document/description"/></textarea>
	            </dd>
	        </dl>
	
	        <dl class="form-col">
				
				<dt><label for="field_category"><xsl:value-of select="php:function('lang', 'Category')" /></label></dt>
				<dd>
					<select name='category' id='field_category'>
						<option value=''><xsl:value-of select="php:function('lang', 'Select Category...')" /></option>
						<xsl:for-each select="document/document_types/*">
							<option>
								<xsl:if test="../../category = local-name()">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
						
								<xsl:attribute name="value"><xsl:value-of select="local-name()"/></xsl:attribute>
								<xsl:value-of select="php:function('lang', string(node()))"/>
							</option>
						</xsl:for-each>
					</select>
				</dd>
				
			</dl>

	        <div class="clr"/>
	        <div class="form-buttons">
            <input type="submit">
				<xsl:attribute name="value">
					<xsl:choose>
						<xsl:when test="document/id">
							<xsl:value-of select="php:function('lang', 'Update')"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="php:function('lang', 'Create')"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="document/cancel_link"/></xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    	</form>
    </div>
</xsl:template>
