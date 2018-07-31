
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
	</xsl:choose>

</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="edit">
    <div class="content">
        <style type="text/css">
                #floating-box {
                position: relative;
                z-index: 1000;
                }
                #submitbox {
                display: none;
                }
        </style>

        <div id='receipt'></div>
        <div>
            <xsl:variable name="form_action">
                <xsl:value-of select="form_action"/>
            </xsl:variable>

            <form id="form" name="form" method="post" action="{$form_action}" onsubmit="return process_list()" class="pure-form pure-form-aligned">
                    <div id="tab-content">
                            <xsl:value-of disable-output-escaping="yes" select="tabs"/>
                            <div id="floating-box">
                                <div id="submitbox">
                                    <xsl:variable name="lang_cancel">
                                        <xsl:value-of select="php:function('lang', 'cancel')"/>
                                    </xsl:variable>
                                    <xsl:variable name="lang_save">
                                        <xsl:value-of select="php:function('lang', 'next')"/>
                                    </xsl:variable>

                                    <table width="200px">
                                        <tbody>
                                            <tr>
                                                <td width="200px">
                                                    <input type="button" class="pure-button pure-button-primary" name="save" id="save_button" onClick="validate_submit();">
                                                        <xsl:attribute name="value">
                                                            <xsl:value-of select="$lang_save"/>
                                                        </xsl:attribute>
                                                        <xsl:attribute name="title">
                                                            <xsl:value-of select="$lang_save"/>
                                                        </xsl:attribute>
                                                    </input>
                                                </td>
                                                <td>
                                                    <input type="button" class="pure-button pure-button-primary" name="done" value="{$lang_cancel}" onClick="window.location = '{cancel_url}';">
                                                        <xsl:attribute name="title">
                                                            <xsl:value-of select="php:function('lang', 'Back to the ticket list')"/>
                                                        </xsl:attribute>
                                                    </input>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>

                            <div id="categories">
                                <fieldset>
                                    <div class="pure-form pure-form-stacked">
                                        <div class="pure-g">
                                            <div class="pure-u-1 pure-u-md-1-3">
                                                <label>
                                                    <xsl:value-of select="php:function('lang', 'category')"/>
                                                </label>
                                                <select id="all_cats" name="all_cats" class="pure-input-1">
                                                    <xsl:apply-templates select="categories/options"/>
                                                </select>
                                                <img src="{image_loader}" class="processing" align="absmiddle"></img>
                                            </div>                                                
                                        </div>
                                        <div class="pure-g">
                                            <div class="pure-u-1 pure-u-md-1-3">
                                                <label>
                                                    <xsl:value-of select="php:function('lang', 'all persons')"/>
                                                </label>
                                                <select multiple="true" id="all_persons" name="all_persons" class="pure-input-1">
                                                    <xsl:apply-templates select="all_persons/options"/>
                                                </select>
                                            </div>
                                            <div class="pure-u-1 pure-u-md-1-6">
                                                <label for="last-name"> </label>
                                                <div class="pure-input-1">
                                                    <button type="button" class="button-xsmall pure-button selector-add"> &gt;&gt; </button><br/>
                                                    <button type="button" class="button-xsmall pure-button selector-remove"> &lt;&lt; </button>
                                                </div>
                                            </div>
                                            <div class="pure-u-1 pure-u-md-1-3">
                                                <label>
                                                    <xsl:value-of select="php:function('lang', 'current persons')"/>
                                                </label>
                                                <select multiple="true" id="current_persons" name="current_persons[]" class="pure-input-1">
                                                    <xsl:apply-templates select="current_persons/options"/>
                                                </select>
                                            </div>
                                        </div>
                                    </div>                                                           
                                </fieldset>
                            </div>
                    </div>
                    <div id="submit_group_bottom" class="proplist-col">
                            <xsl:variable name="lang_save">
                                    <xsl:value-of select="php:function('lang', 'save')"/>
                            </xsl:variable>
                            <input type="submit" class="pure-button pure-button-primary" name="save" id="save_button_bottom">
                                    <xsl:attribute name="value">
                                            <xsl:value-of select="$lang_save"/>
                                    </xsl:attribute>
                                    <xsl:attribute name="title">
                                            <xsl:value-of select="$lang_save"/>
                                    </xsl:attribute>
                            </input>
                            <xsl:variable name="cancel_url">
                                    <xsl:value-of select="cancel_url"/>
                            </xsl:variable>
                            <input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{cancel_url}';">
                                    <xsl:attribute name="value">
                                            <xsl:value-of select="php:function('lang', 'cancel')"/>
                                    </xsl:attribute>
                            </input>
                    </div>
            </form>
        </div>
    </div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
