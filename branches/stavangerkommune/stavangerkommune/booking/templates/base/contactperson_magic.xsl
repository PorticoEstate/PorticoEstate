<xsl:template name="contactpersonmagic" xmlns:php="http://php.net/xsl">
    <div id="contactpersonform" style="visibility:hidden;">
        <div class="hd"><xsl:value-of select="php:function('lang', 'New contact')" /></div>
        <div class="bd">
            <form method="POST" id="thepersonform">
				<xsl:attribute name="action"><xsl:value-of select="contact_form_link" /></xsl:attribute>
                <xsl:call-template name="contactpersonfields" />
            </form>
        </div>
    </div>
<script type="text/javascript">
var lang_submit = '<xsl:value-of select="php:function('lang', 'Save')"/>';
var lang_cancel = '<xsl:value-of select="php:function('lang', 'Cancel')" />';
var contactFormLink = '<xsl:value-of select="contact_form_link" />';
var newContactText = '<xsl:value-of select="php:function('lang', 'New contact')" />';
var editContactText = '<xsl:value-of select="php:function('lang', 'Edit contact')" />'
<![CDATA[
var handleSubmit = function() {this.submit();};
var handleCancel = function() {this.cancel();};
var handleSuccess = function(o) {/*alert(o.responseText);*/};
var handleFailure = function(o) {
    alert("Submission failed: " + o.status);
};

YAHOO.booking.contactpersonform = new YAHOO.widget.Dialog("contactpersonform", 
{
    width : "40em",
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
    var fields = new Array(
        'contact-field-name',
        'contact-field-ssn',
        'contact-field-homepage', 
        'contact-field-phone',
        'contact-field-email',
        'contact-field-description'
    );
    document.getElementById("thepersonform").setAttribute("action", contactFormLink);
    YAHOO.util.Dom.getElementsByClassName('hd', 'div')[0].innerHTML = newContactText;
    var i = -1;
    while(fields.length > ++i) {
        document.getElementById(fields[i]).value = "";
    }
    YAHOO.booking.contactpersonform.show();
}
var handleEditSuccess = function(o) {
    try {
        var data = YAHOO.lang.JSON.parse(o.responseText);
    }
        catch (e) {
        alert("Invalid product data");
        return;
    }
    YAHOO.util.Dom.getElementsByClassName('hd', 'div')[0].innerHTML = editContactText;
    document.getElementById('contact-field-name').value        = data.ResultSet.Result.name;
    document.getElementById('contact-field-ssn').value         = data.ResultSet.Result.ssn;
    document.getElementById('contact-field-homepage').value    = data.ResultSet.Result.homepage;
    document.getElementById('contact-field-phone').value       = data.ResultSet.Result.phone;
    document.getElementById('contact-field-email').value       = data.ResultSet.Result.email;
    document.getElementById('contact-field-description').value = data.ResultSet.Result.description;
}
editCallback = { success: handleEditSuccess, failure: handleFailure };
function editContact(fieldid) {
    var contactid = document.getElementById(fieldid).value;
    var sUrl = 'index.php?menuaction=' + endpoint + '.uicontactperson.index&phpgw_return_as=json&id='+ contactid;
    document.getElementById("thepersonform").setAttribute("action", contactFormLink + "&id=" + contactid);
    YAHOO.util.Connect.asyncRequest('GET', sUrl, editCallback, null); 
    YAHOO.booking.contactpersonform.show();
}

]]>
</script>

</xsl:template>
