/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function() {
    
    $('#add-date-link').click(function(){
        var add = $(this);
        var html = '';
        
        if (!this.counter) { this.counter = 0; }
                         
        html = '<div class="date-container">'+
                '<a class="close-btn btnclose" href="javascript:void(0);">-</a>'+
                '<div class="pure-control-group">'+
                        '<label for="start_date_'+this.counter+'"><h4>!from</h4></label>'+
                        '<input class="time pure-input-2-3" id="start_date_'+this.counter+'" name="start_date_'+this.counter+'" type="text">'+
                              // '<xsl:if test="activity/start_date != """>'+
    //                                    '<xsl:attribute name="value">'+
    //                                            '<xsl:value-of select="php:function("date", $datetime_format, number(activity/start_date))"/>'+
    //                                    '</xsl:attribute>'+
    //                            '</xsl:if>'+
    //
    //                            '<xsl:attribute name="data-validation">'+
    //                                '<xsl:text>required</xsl:text>'+
    //                            '</xsl:attribute>'+ 
                        '</input>'+
                '</div>'+
                '<div class="pure-control-group">'+
                        '<label for="end_date_'+this.counter+'"><h4>!to</h4></label>'+
//                        <xsl:if test="activity/error_msg_array/end_date != ''">
//                                <xsl:variable name="error_msg">
//                                        <xsl:value-of select="activity/error_msg_array/end_date" />
//                                </xsl:variable>
//                                <div class='input_error_msg'>
//                                        <xsl:value-of select="php:function('lang', $error_msg)" />
//                                </div>
//                        </xsl:if>
                        '<input class="time pure-input-2-3" id="end_date_'+this.counter+'" name="end_date_'+this.counter+'" type="text">'+
//                                <xsl:if test="activity/end_date != ''">
//                                        <xsl:attribute name="value">
//                                                <xsl:value-of select="php:function('date', $datetime_format, number(activity/end_date))"/>
//                                        </xsl:attribute>
//                                </xsl:if>
                        '</input>'+
                '</div>'
         '</div>';
        
	this.counter++;
        
        add.parent().parent().children('#dates-container').append(html);
        
        $( ".time" ).datetimepicker({ 
                dateFormat: 'dd/mm/yy',
                showWeek: true,
                changeMonth: true,
                changeYear: true,
                showOn: "button",
                showButtonPanel:true,
                buttonImage: "/portico/phpgwapi/templates/base/images/cal.png",
                buttonText: "Select date",
                buttonImageOnly: true
        });
        
    });
    
});

$(document).on("click",".btnclose",function(){
    var the = $(this);
    RemoveDate(the);
});

RemoveDate = function(the){
    the.parent().remove();
}

