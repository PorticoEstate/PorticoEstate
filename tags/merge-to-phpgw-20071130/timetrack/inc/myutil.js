function capitalizeEveryWord(userText) {

        var holdArray = userText.split(" ");
        var modifieduserText="";

        for (var j = 0; j < holdArray.length; j++) {
                modifieduserText = modifieduserText+holdArray[j].substring(0,1).toUpperCase();
                modifieduserText = modifieduserText+holdArray[j].substring(1);
                modifieduserText = modifieduserText+" ";
        }

        userText = modifieduserText;

        return userText;
}

function capitalizeFirstWord(userText) {
	var modifieduserText="";

	modifieduserText = modifieduserText+userText.substring(0,1).toUpperCase();
	modifieduserText = modifieduserText+userText.substring(1);

	userText = modifieduserText;
        return userText;
}

function capitalizeAll(userText) {
	var modifieduserText=userText.toUpperCase();
	userText = modifieduserText;
	return userText;
}

// Added inc parameter to pass increment values. May need a method to convert that also
// to a decimal value (i.e. 15 minutes translates to .25 hours)
function CheckNum(z,min,max,inc) {
  var x = (z.value);
  if (isNaN(x)){
    alert("This input box only accepts numbers.\n Please change value to a numeric format.");
    z.value = min;
    z.focus();
    return;
  }
  if (x > max){
    alert("The value cannot be more than "+max+".\n Please correct the value.");
    z.value = max;
    z.focus();
  }
  if (x < min){
    alert("The value cannot be less than "+min+".\n Please correct the value.");
    z.value = min;
    z.focus();
  }
 if (inc > 1){
    z.value = Math.floor(x/inc) * inc;
 }
}

// Modeled after gethours but reversed to set end_time from whours input
// interval parameter is used to specify smallest minute increments allowed.
function Calc_endtime(myform,whours,starttime,endtime,tslice) {
 var s_hour = new Number(eval("document." + myform + "." + starttime + "_h.value"));
 var s_min = new Number(eval("document." + myform + "." + starttime + "_m.value"));
 var s_ampm = eval("document." + myform + "." + starttime + "_ampm[1].checked");
 var inverse_interval = new Number(1.0/tslice);
 // Adjust the start time entry values to 24 hour number:
 if (s_ampm == true && s_hour != 12) {
  s_hour = s_hour + 12.0;
 }
 if (s_ampm == false && s_hour == 12) {
  s_hour = 0.0;
 }
 //var s_time = new Number(s_hour + (s_min/60.0));

 //alert ("shour: " + s_hour + " s_min: " + s_min);

 var total_hours = new Number(eval("document." + myform + "." + whours + ".value"));
 var whole_hours = new Number(Math.floor(total_hours));
 var remainder = new Number(total_hours - whole_hours);
 if (remainder != 0.0) {
   for(i=1; i<tslice; i++) {
     realremainder = new Number(Math.max(inverse_interval * i,remainder));
     if(realremainder == inverse_interval * i)
       break;
     realremainder = inverse_interval * i;
   }
 } else {
   realremainder = new Number(0.0);
 }
 // We should be able to reset the hours field either here, or we could call get_hours
 // after setting the end_time fields.
 total_hours = whole_hours + realremainder;
 var thour_str = new String(total_hours.toString());
 if(thour_str.length <= 2) {
  // we most likely have a whole hour, pad it out with decimal point and zeros
  thour_str = thour_str + ".00";
 }
 if(thour_str.charAt(0) == ".") {
  // we need a leading zero in this case
  thour_str = "0" + thour_str;
 }
 eval('document.' + myform + '.' + whours + '.value = thour_str');

 var e_hour = new Number(s_hour + whole_hours);
 var e_min = new Number(s_min + (realremainder * 60.0));
 // need to carry?
 if (e_min > 59.0) {
   e_min = e_min - 60.0;
   e_hour = e_hour + 1;
 }
 if (e_hour > 23.0) {
   e_hour = e_hour - 24.0;
 } //else if (e_hour > 11.0) {
   //e_hour = e_hour - 12.0;
 //}

 //else {
 //}

 if (e_hour > 11) {
  var e_ampm = true;
  var ampm = "P.M.";
  e_hour = e_hour - 12;
 } else {
  var e_ampm = false;
  var ampm = "A.M.";
 }
 if (e_hour == 0) e_hour = 12;
 //alert("Total hours is " + total_hours + ", e_hour: " + e_hour + ",\n e_min: " + e_min + " e_ampm: " + ampm);
 //alert("End time: " + e_hour + ":" + e_min + " " + ampm);
 eval('document.' + myform + '.' + endtime + '_h.value = e_hour');
 eval('document.' + myform + '.' + endtime + '_m.value = e_min');
 if (e_ampm == true) {
   eval('document.' + myform + '.' + endtime + '_ampm[0].checked = 0');
   eval('document.' + myform + '.' + endtime + '_ampm[1].checked = 1');
 } else {
   eval('document.' + myform + '.' + endtime + '_ampm[1].checked = 0');
   eval('document.' + myform + '.' + endtime + '_ampm[0].checked = 1');
 }
}

// myform = name of the from
// whours = name of input box where to publish the hours value
// starttime = prefix name of the start-time components
// endtime = prefix name of the end-time components
function gethours(myform,whours,starttime,endtime) {
 var s_hours = new Number(eval("document." + myform + "." + starttime + "_h.value"));
 var s_min = new Number(eval("document." + myform + "." + starttime + "_m.value"));
 var e_hours = new Number(eval("document." + myform + "." + endtime + "_h.value"));
 var e_min = new Number(eval("document." + myform + "." + endtime + "_m.value"));
 var s_ampm = eval("document." + myform + "." + starttime + "_ampm[1].checked");
 // If these equal "true" then PM is set.
 var e_ampm = eval("document." + myform + "." + endtime + "_ampm[1].checked");
 //adjust the hours for ampm
 //alert("Start AM/PM = "+s_ampm+"\nEnd AM/PM = "+e_ampm);
 if (s_ampm == true && s_hours != 12) {
  s_hours = s_hours + 12.0;
 }
 if (e_ampm == true && e_hours != 12) {
  e_hours = e_hours + 12.0;
 }
 if (s_ampm == false && s_hours == 12) {
  s_hours = 0.0;
 }
 if (e_ampm == false && e_hours == 12) {
  e_hours = 0.0;
 }
 
 //alert("Start Hour = "+s_hours+"\nEnd Hours = "+e_hours);

 var s_time = new Number(s_hours + (s_min/60.0));
 var e_time = new Number(e_hours + (e_min/60.0));
 var total_hours = new Number(e_time - s_time);
 //alert("Start Time: "+s_time+"\nEnd Time: "+e_time);
 if(total_hours < 0.0) {
  total_hours = 24.0 + total_hours;
 }
 var thour_str = new String(total_hours.toString());
 if(thour_str.length <= 2) {
  // we most likely have a whole hour, pad it out with decimal point and zeros
  thour_str = thour_str + ".00";
 }
 if(thour_str.charAt(0) == ".") {
  // we need a leading zero in this case
  thour_str = "0" + thour_str;
 }
 eval('document.' + myform + '.' + whours + '.value = thour_str');
}

function inc_num(myform,myname,finc,radioname) {
 var mystr = new String();
 //var val2 = new Number();
 //var myinc = new Number();
 var val1 = eval("document." + myform + "." + myname + ".value");
 val2 = new Number(eval("document." + myform + "." + val1 + ".value"));
 myinc = new Number(eval("document." + myform + "." + finc + ".value"));
 // Setup loop limits. If the last char of "val1" is "m", then limit is 0-59
 // else if last char is "h", limit is 1-12
 switch (val1.charAt(val1.length-1))
  {
   case "h" :
     //alert("Hours");
     var mymin = new Number(1);
     var mymax = new Number(12);
     val2 = val2 + myinc;
     if (val2 > mymax) val2 = mymin;
     if (val2 == mymax)
      {
        if (eval('document.' + myform + '.' + radioname + '[0].checked'))
	  eval('document.' + myform + '.' + radioname + '[1].click()');
	else
	  eval('document.' + myform + '.' + radioname + '[0].checked = 1');
      }
     break;
   case "m" :
     //alert("Minutes");
     var mymin = new Number(0);
     var mymax = new Number(59);
     val2 = val2 + myinc;
     if (val2 > mymax) val2 = mymin;
     break;
   default :
     alert("Oops");
  }
 mystr = val2.toString();
 if(mystr.length == 1) mystr = "0" + mystr;
 eval('document.' + myform + '.' + val1 + '.value = mystr');
 eval('document.' + myform + '.' + val1 + '.focus()');
 return;
}

function dec_num(myform,myname,finc,radioname) {
 var mystr = new String();
 //var val2 = new Number();
 //var myinc = new Number();
 var val1 = eval("document." + myform + "." + myname + ".value");
 val2 = new Number(eval("document." + myform + "." + val1 + ".value"));
 myinc = new Number(eval("document." + myform + "." + finc + ".value"));
 // Setup loop limits. If the last char of "val1" is "m", then limit is 0-59
 // else if last char is "h", limit is 1-12
 switch (val1.charAt(val1.length-1))
  {
   case "h" :
     //alert("Hours");
     var mymin = new Number(1);
     var mymax = new Number(12);
     val2 = val2 - myinc;
     if (val2 < mymin) val2 = (mymax + 1) - myinc;
     if (val2 == mymax - 1)
      {
	if (eval('document.' + myform + '.' + radioname + '[0].checked'))
	  eval('document.' + myform + '.' + radioname + '[1].click()');
	else
	  eval('document.' + myform + '.' + radioname + '[0].checked = 1');
      }
     break;
   case "m" :
     //alert("Minutes");
     var mymin = new Number(0);
     var mymax = new Number(59);
     val2 = val2 - myinc;
     if (val2 < mymin) val2 = (mymax + 1) - myinc;
     break;
   default :
     alert("Oops");
  }
 //val2 = val2 - myinc;
 //if (val2 < mymin) val2 = (mymax + 1) - myinc;
 mystr = val2.toString();
 if(mystr.length == 1) mystr = "0" + mystr;
 eval('document.' + myform + '.' + val1 + '.value = mystr');
 eval('document.' + myform + '.' + val1 + '.focus()');
 return;
} 

function shake(n) {
if (self.moveBy) {
for (i = 10; i > 0; i--) {
for (j = n; j > 0; j--) {
self.moveBy(0,i);
self.moveBy(i,0);
self.moveBy(0,-i);
self.moveBy(-i,0);
         }
      }
   }
}
