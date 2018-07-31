
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="import">
			<xsl:apply-templates select="import" />
		</xsl:when>
		<xsl:when test="export">
			<xsl:apply-templates select="export" />
		</xsl:when>                
	</xsl:choose>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="import">
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

            <form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned" enctype="multipart/form-data">
                <div id="tab-content">
                    <xsl:value-of disable-output-escaping="yes" select="tabs"/>

                    <input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>

                    <div id="import">
                        <fieldset>
                            <div>
                                <h3>Import from LDIF, CSV, or VCard</h3>
                                <div class="pure-control-group">                                           
                                    <label></label>
                                    <div class="pure-custom">
                                        <div>
                                            <ol>
                                                <li>
                                                    <p>In Netscape, open the Addressbook and select <b>Export</b> from the <b>File</b> menu.
                                                        The file exported will be in LDIF format.
                                                    </p>
                                                    <p>Or, in Outlook, select your Contacts folder, select <b>Import 
                                                      and Export...</b> from the <b>File</b> 
                                                      menu and export your contacts into a comma separated text (CSV) file.
                                                    </p>
                                                    <p>Or, in Palm Desktop 4.0 or greater, visit your addressbook and select <b>Export</b> from the <b>File</b> menu.
                                                      The file exported will be in VCard (only v.2 vCards) format.</p>
                                                    <p></p>
                                                </li>
                                                <li>Enter the path to the exported file here:
                                                    <input name="tsvfile" size="48" value="" type="file"></input><p></p>
                                                </li>
                                                <li>Select the type of conversion:
                                                    <select id="conv_type" name="conv_type">
                                                        <xsl:apply-templates select="conv/options"/>
                                                    </select><p></p>
                                                </li>
                                                <li>Select Category:
                                                    <select id="fcat_id" name="fcat_id">
                                                        <xsl:apply-templates select="categories/options"/>
                                                    </select>
                                                </li>
                                                <li><input name="private" value="private" checked="" type="checkbox"/>Mark records as private</li>
                                                <li><input name="download" value="Debug output in browser" checked="" type="checkbox"/>Debug output in browser</li>
                                            </ol>
                                        </div>                                                      
                                    </div>                                             
                                </div>                                                                                                                                                                                                                                    
                            </div>                                                           
                        </fieldset>
                    </div>
                </div>
                <div id="submit_group_bottom" class="proplist-col">
                    <xsl:variable name="lang_save">
                            <xsl:value-of select="php:function('lang', 'submit')"/>
                    </xsl:variable>
                    <input type="submit" class="pure-button pure-button-primary" name="convert">
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

<xsl:template xmlns:php="http://php.net/xsl" match="export">
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

            <form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned" enctype="multipart/form-data">
                <div id="tab-content">
                    <xsl:value-of disable-output-escaping="yes" select="tabs"/>

                    <input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>

                    <div id="export">
                        <fieldset>
                            <div>
                                <div class="pure-control-group">                                           
                                    <label></label>
                                    <div class="pure-custom">
                                        <div>
                                            <ol>
                                                <li>Select the type of conversion:
                                                    <select id="conv_type" name="conv_type">
                                                        <xsl:apply-templates select="conv/options"/>
                                                    </select><p></p>
                                                </li>
                                                <li>Export file name:<input name="tsvfilename" value="export.txt"/></li>
                                                <li>Select Category:
                                                    <select id="fcat_id" name="fcat_id">
                                                        <xsl:apply-templates select="categories/options"/>
                                                    </select><p></p>
                                                </li>
                                                <li><input name="download" checked="" type="checkbox"/>Download export file (Uncheck to debug output in browser)</li>
                                                <p>OpenOffice export only uses the following options:</p>
                                                <li><input name="both_types" checked="" type="checkbox"/>OpenOffice.org only - Include fields from both types of contacts? (Uncheck to only include fields from previous screen)</li>
                                                <li><input name="sub_cats" checked="" type="checkbox"/>OpenOffice.org only - Include field for category names (only sub-categories if category is selected)</li>
                                            </ol>
                                        </div>                                                      
                                    </div>                                             
                                </div>                                                                                                                                                                                                                                    
                            </div>                                                           
                        </fieldset>
                    </div>
                </div>
                <div id="submit_group_bottom" class="proplist-col">
                    <xsl:variable name="lang_save">
                            <xsl:value-of select="php:function('lang', 'submit')"/>
                    </xsl:variable>
                    <input type="submit" class="pure-button pure-button-primary" name="convert">
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
