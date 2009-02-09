<xsl:template match="data">
    <h3>New Building</h3>

    <form action="" method="POST">
        <dl>
            <dt><label for="field_name">Name</label></dt>
            <dd><input id="field_name" name="name" type="text"/></dd>
            <dt><label for="field_homepage">Homepage</label></dt>
            <dd><input id="field_homepage" name="homepage" type="text"/></dd>
        </dl>
        <input type="submit" value="Add"/>
    </form>

</xsl:template>
