<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

        <dl class="form">
            <dt class="heading"><xsl:value-of select="php:function('lang', 'Delete Event')"/></dt>
        </dl>
        <xsl:call-template name="msgbox"/>
        <xsl:call-template name="yui_booking_i18n"/>
        <dl class="form">
            <xsl:if test="can_delete_events=1">
                <dd><xsl:value-of select="php:function('lang', 'Event Delete Information')"/></dd>
            </xsl:if>
            <xsl:if test="can_delete_events=0">
                <dd><xsl:value-of select="php:function('lang', 'Event Delete Information2')"/></dd>
            </xsl:if>

        </dl>
        <div class="clr"/>
        <form action="" method="POST">
            <dl class="form-col">
                <dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')" /></label></dt>
                <dd>
                    <div>
                        <xsl:value-of select="event/building_name"/>
                    </div>
                </dd>
                <dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
                <dd>
                    <div>
                        <xsl:value-of select="event/description"/>
                    </div>
                </dd>
                <dt><label for="field_activity"><xsl:value-of select="php:function('lang', 'Activity')" /></label></dt>
                <dd>
                    <div>
                        <xsl:for-each select="activities">
                            <xsl:if test="../event/activity_id = id">
                                <xsl:value-of select="name"/>
                            </xsl:if>
                        </xsl:for-each>
                    </div>
                </dd>
                <dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
                <dd>
                    <div>
                        <xsl:value-of select="event/from_"/>
                    </div>
                </dd>
                <dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')"/></label></dt>
                <dd>
                    <div>
                        <xsl:value-of select="event/to_"/>
                    </div>
                </dd>
            </dl>
            <div style='clear:left; padding:0; margin:0'/>

            <dl class="form-col">
                <dt><label for="field_message"><xsl:value-of select="php:function('lang', 'Message')" /></label></dt>
                <dd class="yui-skin-sam">
                    <textarea id="field-message" name="message" type="text"><xsl:value-of select="system_message/message"/></textarea>
                </dd>
            </dl>
            <div class="form-buttons">
                <input type="submit">
                    <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Delete')"/></xsl:attribute>
                </input>
                <a class="cancel">
                    <xsl:attribute name="href"><xsl:value-of select="event/cancel_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Cancel')"/>
                </a>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        YAHOO.event.initialSelection = <xsl:value-of select="booking/resources_json"/>;
        var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
        <![CDATA[
        var descEdit = new YAHOO.widget.SimpleEditor('field-message', {
            height: '150px',
            width: '522px',
            dompath: true,
            animate: true,
    	    handleSubmit: true,
            toolbar: {
                titlebar: '',
                buttons: [
                   { group: 'textstyle', label: ' ',
                        buttons: [
                            { type: 'push', label: 'Bold', value: 'bold' },
                            { type: 'separator' },
                            { type: 'push', label: 'HTML Link CTRL + SHIFT + L', value: 'createlink'}
                        ]
                    }
                ]
            }
        });
        descEdit.render();
        ]]>
    </script>
</xsl:template>
