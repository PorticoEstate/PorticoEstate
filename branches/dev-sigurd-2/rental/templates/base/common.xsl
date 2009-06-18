<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:phpgw="http://phpgroupware.org/functions"
	xmlns:func="http://exslt.org/functions"
	extension-element-prefixes="func"
	exclude-result-prefixes="phpgw">
	
	<!--
	Function
	phpgw:conditional( expression $test, mixed $true, mixed $false )
	Evaluates test expression and returns the contents in the true variable if
	the expression is true and the contents of the false variable if its false

	Returns mixed
	-->
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
	
	<xsl:template match="phpgw" xmlns:php="http://php.net/xsl">
		<xsl:call-template name="javascriptDateFunction"/>
		<script>
			YAHOO.rental.setupDatasource = new Array();
			
			function initCalendar(inputFieldID, divContainerID, tableCalendarClassName, calendarTitle) 
			{
				var cal = new YAHOO.widget.Calendar(tableCalendarClassName,divContainerID,{navigator:true, title:calendarTitle, close:true, start_weekday:1});
				cal.render();
				cal.hide();
				YAHOO.util.Event.addListener(inputFieldID,'click',onClickOnInput,cal,true);	
				cal.selectEvent.subscribe(onCalendarSelect,[inputFieldID,cal],false);
			}
			
			function onClickOnInput(event)
			{
				this.show();	
			}
			
			function onCalendarSelect(type,args,array){
				var firstDate = args[0][0];
				var day = firstDate[1] + "";
				var month = firstDate[2] + "";
				var year = firstDate[0] + "";
				var date = day + "/" + month + "/" + year;
				document.getElementById(array[0]).value = formatDate('<xsl:value-of select="//dateFormat"/>',Math.round(Date.parse(date)/1000));
				array[1].hide();
			}
			
		</script>
		<div id="rental_user_error">
			<xsl:value-of select="data/error"/>
		</div>
		<div id="rental_user_message">
			<xsl:value-of select="data/message"/>
		</div>
		<xsl:call-template name="pageForm"/>
		<xsl:call-template name="pageContent"/>
	</xsl:template>

	<xsl:template name="datasource-definition">
		<xsl:param name="number">1</xsl:param>
		<xsl:param name="form"></xsl:param>
		<xsl:param name="filters">[]</xsl:param>
		<xsl:param name="container_name"></xsl:param>
		<xsl:param name="context_menu_labels">[]</xsl:param>
		<xsl:param name="context_menu_actions">[]</xsl:param>
		<xsl:param name="columnDefinitions">[]</xsl:param>
		<xsl:param name="source"></xsl:param>
		<script>
			YAHOO.rental.setupDatasource.push(function() {
		        this.dataSourceURL = '<xsl:value-of select="$source"/>';
				this.columnDefs = <xsl:value-of select="$columnDefinitions"/>;
				this.formBinding = '<xsl:value-of select="$form"/>';
				this.filterBinding = <xsl:value-of select="$filters"/>;
				this.containerName = '<xsl:value-of select="$container_name"/>';
				this.contextMenuName = 'contextMenu<xsl:value-of select="$number"/>';
				this.contextMenuLabels = <xsl:value-of select="$context_menu_labels"/>;
				this.contextMenuActions = <xsl:value-of select="$context_menu_actions"/>;
			});
		</script>
	</xsl:template>
	<xsl:template name="javascriptDateFunction">
		<script>
			<xsl:text disable-output-escaping="yes">
			function formatDate ( format, timestamp ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
    // +      parts by: Peter-Paul Koch (http://www.quirksmode.org/js/beat.html)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: MeEtc (http://yass.meetcweb.com)
    // +   improved by: Brad Touesnard
    // +   improved by: Tim Wiel
    // +   improved by: Bryan Elliott
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: David Randall
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   derived from: gettimeofday
    // %        note 1: Uses global: php_js to store the default timezone
    // *     example 1: date('H:m:s \\m \\i\\s \\m\\o\\n\\t\\h', 1062402400);
    // *     returns 1: '09:09:40 m is month'
    // *     example 2: date('F j, Y, g:i a', 1062462400);
    // *     returns 2: 'September 2, 2003, 2:26 am'
    // *     example 3: date('Y W o', 1062462400);
    // *     returns 3: '2003 36 2003'
    // *     example 4: x = date('Y m d', (new Date()).getTime()/1000); // 2009 01 09
    // *     example 4: (x+'').length == 10
    // *     returns 4: true
 
    var jsdate=(
        (typeof(timestamp) == 'undefined') ? new Date() : // Not provided
        (typeof(timestamp) == 'number') ? new Date(timestamp*1000) : // UNIX timestamp
        new Date(timestamp) // Javascript Date()
    ); // , tal=[]
    var pad = function(n, c){
        if( (n = n + "").length &lt; c ) {
            return new Array(++c - n.length).join("0") + n;
        } else {
            return n;
        }
    };
    var _dst = function (t) {
        // Calculate Daylight Saving Time (derived from gettimeofday() code)
        var dst=0;
        var jan1 = new Date(t.getFullYear(), 0, 1, 0, 0, 0, 0);  // jan 1st
        var june1 = new Date(t.getFullYear(), 6, 1, 0, 0, 0, 0); // june 1st
        var temp = jan1.toUTCString();
        var jan2 = new Date(temp.slice(0, temp.lastIndexOf(' ')-1));
        temp = june1.toUTCString();
        var june2 = new Date(temp.slice(0, temp.lastIndexOf(' ')-1));
        var std_time_offset = (jan1 - jan2) / (1000 * 60 * 60);
        var daylight_time_offset = (june1 - june2) / (1000 * 60 * 60);
 
        if (std_time_offset === daylight_time_offset) {
            dst = 0; // daylight savings time is NOT observed
        }
        else {
            // positive is southern, negative is northern hemisphere
            var hemisphere = std_time_offset - daylight_time_offset;
            if (hemisphere >= 0) {
                std_time_offset = daylight_time_offset;
            }
            dst = 1; // daylight savings time is observed
        }
        return dst;
    };
    var ret = '';
    var txt_weekdays = ["Sunday","Monday","Tuesday","Wednesday",
        "Thursday","Friday","Saturday"];
    var txt_ordin = {1:"st",2:"nd",3:"rd",21:"st",22:"nd",23:"rd",31:"st"};
    var txt_months =  ["", "January", "February", "March", "April",
        "May", "June", "July", "August", "September", "October", "November",
        "December"];
 
    var f = {
        // Day
            d: function(){
                return pad(f.j(), 2);
            },
            D: function(){
                var t = f.l();
                return t.substr(0,3);
            },
            j: function(){
                return jsdate.getDate();
            },
            l: function(){
                return txt_weekdays[f.w()];
            },
            N: function(){
                return f.w() + 1;
            },
            S: function(){
                return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th';
            },
            w: function(){
                return jsdate.getDay();
            },
            z: function(){
                return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0;
            },
 
        // Week
            W: function(){
                var a = f.z(), b = 364 + f.L() - a;
                var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;
 
                if(b &lt;= 2 &amp;&amp; ((jsdate.getDay() || 7) - 1) &lt;= 2 - b){
                    return 1;
                } 
                if(a &lt;= 2 &amp;&amp; nd >= 4 &amp;&amp; a >= (6 - nd)){
                    nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
                    return date("W", Math.round(nd2.getTime()/1000));
                }
                return (1 + (nd &lt;= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
            },
 
        // Month
            F: function(){
                return txt_months[f.n()];
            },
            m: function(){
                return pad(f.n(), 2);
            },
            M: function(){
                var t = f.F();
                return t.substr(0,3);
            },
            n: function(){
                return jsdate.getMonth() + 1;
            },
            t: function(){
                var n;
                if( (n = jsdate.getMonth() + 1) == 2 ){
                    return 28 + f.L();
                }
                if( n &amp; 1 &amp;&amp; n &lt; 8 || !(n &amp; 1) &amp;&amp; n > 7 ){
                    return 31;
                }
                return 30;
            },
 
        // Year
            L: function(){
                var y = f.Y();
                return (!(y &amp; 3) &amp;&amp; (y % 1e2 || !(y % 4e2))) ? 1 : 0;
            },
            o: function(){
                if (f.n() === 12 &amp;&amp; f.W() === 1) {
                    return jsdate.getFullYear()+1;
                }
                if (f.n() === 1 &amp;&amp; f.W() >= 52) {
                    return jsdate.getFullYear()-1;
                }
                return jsdate.getFullYear();
            },
            Y: function(){
                return jsdate.getFullYear();
            },
            y: function(){
                return (jsdate.getFullYear() + "").slice(2);
            },
 
        // Time
            a: function(){
                return jsdate.getHours() > 11 ? "pm" : "am";
            },
            A: function(){
                return f.a().toUpperCase();
            },
            B: function(){
                // peter paul koch:
                var off = (jsdate.getTimezoneOffset() + 60)*60;
                var theSeconds = (jsdate.getHours() * 3600) +
                                 (jsdate.getMinutes() * 60) +
                                  jsdate.getSeconds() + off;
                var beat = Math.floor(theSeconds/86.4);
                if (beat > 1000) {
                    beat -= 1000;
                }
                if (beat &lt; 0) {
                    beat += 1000;
                }
                if ((String(beat)).length == 1) {
                    beat = "00"+beat;
                }
                if ((String(beat)).length == 2) {
                    beat = "0"+beat;
                }
                return beat;
            },
            g: function(){
                return jsdate.getHours() % 12 || 12;
            },
            G: function(){
                return jsdate.getHours();
            },
            h: function(){
                return pad(f.g(), 2);
            },
            H: function(){
                return pad(jsdate.getHours(), 2);
            },
            i: function(){
                return pad(jsdate.getMinutes(), 2);
            },
            s: function(){
                return pad(jsdate.getSeconds(), 2);
            },
            u: function(){
                return pad(jsdate.getMilliseconds()*1000, 6);
            },
 
        // Timezone
            e: function () {
/*                var abbr='', i=0;
                if (this.php_js &amp;&amp; this.php_js.default_timezone) {
                    return this.php_js.default_timezone;
                }
                if (!tal.length) {
                    tal = timezone_abbreviations_list();
                }
                for (abbr in tal) {
                    for (i=0; i &lt; tal[abbr].length; i++) {
                        if (tal[abbr][i].offset === -jsdate.getTimezoneOffset()*60) {
                            return tal[abbr][i].timezone_id;
                        }
                    }
                }
*/
                return 'UTC';
            },
            I: function(){
                return _dst(jsdate);
            },
            O: function(){
               var t = pad(Math.abs(jsdate.getTimezoneOffset()/60*100), 4);
               t = (jsdate.getTimezoneOffset() > 0) ? "-"+t : "+"+t;
               return t;
            },
            P: function(){
                var O = f.O();
                return (O.substr(0, 3) + ":" + O.substr(3, 2));
            },
            T: function () {
/*                var abbr='', i=0;
                if (!tal.length) {
                    tal = timezone_abbreviations_list();
                }
                if (this.php_js &amp;&amp; this.php_js.default_timezone) {
                    for (abbr in tal) {
                        for (i=0; i &lt; tal[abbr].length; i++) {
                            if (tal[abbr][i].timezone_id === this.php_js.default_timezone) {
                                return abbr.toUpperCase();
                            }
                        }
                    }
                }
                for (abbr in tal) {
                    for (i=0; i &lt; tal[abbr].length; i++) {
                        if (tal[abbr][i].offset === -jsdate.getTimezoneOffset()*60) {
                            return abbr.toUpperCase();
                        }
                    }
                }
*/
                return 'UTC';
            },
            Z: function(){
               return -jsdate.getTimezoneOffset()*60;
            },
 
        // Full Date/Time
            c: function(){
                return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P();
            },
            r: function(){
                return f.D()+', '+f.d()+' '+f.M()+' '+f.Y()+' '+f.H()+':'+f.i()+':'+f.s()+' '+f.O();
            },
            U: function(){
                return Math.round(jsdate.getTime()/1000);
            }
    };
 
    return format.replace(/[\\]?([a-zA-Z])/g, function(t, s){
        if( t!=s ){
            // escaped
            ret = s;
        } else if( f[s] ){
            // a date function exists
            ret = f[s]();
        } else{
            // nothing special
            ret = s;
        }
        return ret;
    });
}
			</xsl:text>
		</script>
	</xsl:template>
</xsl:stylesheet>