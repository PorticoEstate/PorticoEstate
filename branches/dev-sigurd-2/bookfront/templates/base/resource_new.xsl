<xsl:template match="data">
    <style type="text/css">
    #myAutoComplete {
        width:25em; /* set width here or else widget will expand to fit its container */
        padding-bottom:2em;
    }
    </style>
    <h3>New Resource</h3>

    <form action="" method="POST">
        <dl>
            <dt><label for="field_name">Name</label></dt>
            <dd><input id="field_name" name="name" type="text"/></dd>
            <dt><label for="field_building">Building</label></dt>
            <dd>
                <div id="myAutoComplete">
                <input id="field_building_id" name="building_id" type="hidden"/>
                <input id="field_building_name" type="text"/>
                <div id="building_container"/>
            </div>
            </dd>
        </dl>
        <input type="submit" value="Add"/>
    </form>
</xsl:template>
