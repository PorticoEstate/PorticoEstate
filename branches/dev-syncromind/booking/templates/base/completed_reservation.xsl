<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
	        	<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
  	</func:result>
</func:function>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<!--xsl:call-template name="yui_booking_i18n"/>
<div id="content">
        <ul class="pathway">
                <li>
                        <a href="{reservation/reservations_link}">
                                <xsl:value-of select="php:function('lang', 'Completed Reservations')" />
                        </a>
                </li>
                <li>
                        <xsl:value-of select="php:function('lang', string(reservation/reservation_type))"/>
                </li>
                <li>
                        <xsl:value-of select="reservation/id"/>
                </li>
        </ul-->
        
    <div id='form' class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="reservation/tabs"/>
            <div id="completed_reservation">
                <field>
                    <h1>
                        <xsl:value-of select="php:function('lang', string(reservation/reservation_type))"/> #<xsl:value-of select="reservation/id"/>
                    </h1>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Cost')" /></h4>
                        </label>
                        <span><xsl:value-of select="reservation/cost"/></span>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Customer Type')" /></h4>
                        </label>
                        <span><xsl:value-of select="php:function('lang', string(reservation/customer_type))"/></span>
                    </div>
                    <div class="pure-control-group">
                        <xsl:copy-of select="phpgw:booking_customer_identifier_show(reservation, 'Customer ID')"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'From')" /></h4>
                        </label>
                        <span><xsl:value-of select="reservation/from_"/></span>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'To')" /></h4>
                        </label>
                        <span><xsl:value-of select="reservation/to_"/></span>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Exported')" /></h4>
                        </label>
                        <xsl:if test="reservation/exported_link">
                            <a href="{reservation/exported_link}"><xsl:value-of select="reservation/exported"/></a>
                        </xsl:if>
                        <xsl:if test="not(reservation/exported_link)">
                            <span><xsl:value-of select="reservation/exported"/></span>
                        </xsl:if>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Export File')" /></h4>
                        </label>
                        <xsl:if test="reservation/export_file_id/href">
                            <a href="{reservation/export_file_id/href}"><xsl:value-of select="reservation/export_file_id/label"/></a>
                        </xsl:if>
                        <xsl:if test="not(reservation/export_file_id/href)">
                            <span><xsl:value-of select="reservation/export_file_id/label"/></span>
                        </xsl:if>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Order id')" /></h4>
                        </label>
                        <span><xsl:value-of select="reservation/invoice_file_order_id"/></span>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Season')" /></h4>
                        </label>
                        <xsl:choose>
                            <xsl:when test="reservation/season_name">
                                <a href="{reservation/season_link}"><xsl:value-of select="reservation/season_name"/></a>
                            </xsl:when>
                            <xsl:otherwise>
                                <span><xsl:value-of select="php:function('lang', 'N/A')" /></span>
                            </xsl:otherwise>
			</xsl:choose>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Organization')" /></h4>
                        </label>
                        <xsl:choose>
                            <xsl:when test="reservation/organization_name">
                                <a href="{reservation/organization_link}"><xsl:value-of select="reservation/organization_name"/></a>
                            </xsl:when>
                            <xsl:otherwise>
                                <span><xsl:value-of select="php:function('lang', 'N/A')" /></span>
                            </xsl:otherwise>
			</xsl:choose>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Article Description')" /></h4>
                        </label>
                        <div class="description" style="display:inline-block;max-width:80%;"><xsl:value-of select="reservation/article_description" disable-output-escaping="yes"/></div>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Description')" /></h4>
                        </label>
                        <div class="description" style="display:inline-block;max-width:80%;"><xsl:value-of select="reservation/description" disable-output-escaping="yes"/></div>
                    </div>
                </field>
            </div>
        </div>
    </div>
    <div class="form-buttons">
        <xsl:if test="reservation/exported=php:function('lang', 'No') or reservation/exported = ''">
            <xsl:if test="show_edit_button = 1">
                <button onclick="window.location.href='{reservation/edit_link}'">
                    <xsl:value-of select="php:function('lang', 'Edit')" />
                </button>
            </xsl:if>
        </xsl:if>

        <button onclick='window.location.href="{reservation/reservation_link}"'>
            <xsl:value-of select="php:function('ucfirst', php:function('lang', string(reservation/reservation_type)))" />
        </button>

        <!-- TODO: Add application_link where necessary -->
        <button onclick='window.location.href="{reservation/application_link}"' disabled="1">
            <xsl:value-of select="php:function('lang', 'Application')" />
        </button>
    </div>
	<!--/div-->
</xsl:template>
