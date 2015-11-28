<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div>
        <div id="details">
            <xsl:choose>
                <xsl:when test="message">
                    <div class="success">
                        <xsl:value-of select="message" />
                    </div>
                </xsl:when>
                <xsl:when test="error">
                    <div class="error">
                        <xsl:value-of select="error" />
                    </div>
                </xsl:when>
            </xsl:choose>
        </div>
        <h1><xsl:value-of select="php:function('lang', 'new_organization')" /></h1>
        <div>
            <xsl:value-of select="php:function('lang', 'required_fields')" />
        </div>
        <form action="" method="post" id="form" name="form">
            <input type="hidden" name="activity">
                <xsl:attribute name="value">
                    
                </xsl:attribute>
            </input>
            <dl>
                <input type="hidden" name="organization_id" id="organization_id" value="new_org" />
                <dt><label for="orgname">Organisasjonsnavn (*)</label></dt>
                <dd><input type="text" name="orgname" size="100" /></dd>
                <dt><label for="orgno">Organisasjonsnummer</label></dt>
                <dd><input type="text" name="orgno" /></dd>
                <dt><label for="district">Bydel (*)</label></dt>
                <dd>
                    <select name="org_district">
                        <option value="0">Ingen bydel valgt</option>
                        <xsl:for-each select="districts">
                            <option value=""></option>
                        </xsl:for-each>
                    </select>
                </dd>
                <dt><label for="homepage">Hjemmeside</label></dt>
                <dd><input type="text" name="homepage" size="100" /></dd>
                <dt><label for="email">E-post (*)</label></dt>
                <dd><input type="text" name="email" /></dd>
                <dt><label for="phone">Telefon (*)</label></dt>
                <dd><input type="text" name="phone" /></dd>
                <dt><label for="street">Gate (*)</label></dt>
                <dd>
                    <input type="text" name="address" id="address" onkeyup="javascript:get_address_search()" />
                    <div id="address_container" />
                </dd>
                <dt><label for="number">Husnummer</label></dt>
                <dd><input type="text" name="number" /><br /></dd>
                <dt><label for="postaddress">Postnummer og Sted (*)</label></dt>
                <dd><input type="text" name="postaddress" size="100" /></dd>
                <dt><label for="org_description">Beskrivelse (*)</label></dt>
                <dd><textarea rows="10" cols="100" name="org_description"></textarea></dd>
                <hr />
                <b>Kontaktperson 1</b><br />
                <dt>
                    <label for="contact1_name">Navn (*)</label>
                    <input type="text" name="org_contact1_name" size="100" />
                </dt>
                <dt>
                    <label for="contact1_phone">Telefon (*)</label>
                    <input type="text" name="org_contact1_phone" />
                </dt>
                <dt>
                    <label for="contact1_mail">E-post (*)</label>
                    <input type="text" name="org_contact1_mail" />
                </dt><br /><br /><br />
                <b>Kontaktperson 2</b><br />
                <dt>
                    <label for="contact2_name">Navn</label>
                    <input type="text" name="org_contact2_name" size="100" />
                </dt>
                <dt>
                    <label for="contact2_phone">Telefon</label>
                    <input type="text" name="org_contact2_phone" />
                </dt>
                <dt>
                    <label for="contact2_mail">E-post</label>
                    <input type="text" name="org_contact2_mail" />
                </dt>
                <hr />
                <div class="form-buttons">
                    <input type="submit" name="save_organization" onclick="return allOK();">
                        <xsl:attribute name="value">
                        </xsl:attribute>
                    </input>
                </div>
            </dl>
        </form>
    </div>
</xsl:template>