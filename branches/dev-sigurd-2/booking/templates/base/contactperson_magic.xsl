<xsl:template name="contactpersonmagic" xmlns:php="http://php.net/xsl">
    <div id="contactpersonform" style="visibility:hidden;">
        <div class="hd"><xsl:value-of select="php:function('lang', 'New contact')" /></div>
        <div class="bd">
            <form action="/index.php?menuaction=booking.uicontactperson.edit" method="POST">
                <xsl:call-template name="contactpersonfields" />
            </form>
        </div>
    </div>
<script type="text/javascript">
var lang_submit = '<xsl:value-of select="php:function('lang', 'Save')"/>';
var lang_cancel = '<xsl:value-of select="php:function('lang', 'Cancel')" />';
<![CDATA[
var handleSubmit = function() {this.submit();};
var handleCancel = function() {this.cancel();};
var handleSuccess = function(o) {/*alert(o.responseText);*/};
var handleFailure = function(o) {
    alert("Submission failed: " + o.status);
};

YAHOO.booking.contactpersonform = new YAHOO.widget.Dialog("contactpersonform", 
{
    width : "30em",
    fixedcenter : true,
    visible : false, 
    effect: [
        {effect:YAHOO.widget.ContainerEffect.FADE,duration: 0.75},
        {effect:YAHOO.widget.ContainerEffect.SLIDE,duration: 1}
    ],
    constraintoviewport : true,
    buttons : [
        { text:lang_submit, handler:handleSubmit, isDefault:true },
        { text:lang_cancel, handler:handleCancel }
    ]
});

YAHOO.booking.contactpersonform.callback = { success: handleSuccess, failure: handleFailure };
YAHOO.booking.contactpersonform.render();

function createContact() {
    YAHOO.booking.contactpersonform.show();
}

]]>
</script>

</xsl:template>
