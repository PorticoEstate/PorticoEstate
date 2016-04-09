<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- BEGIN head -->
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" >
		<meta name="AUTHOR" content="phpGroupWare http://www.phpgroupware.org" />
		<meta name="description" content="phpGroupWare" />
		<meta name="keywords" content="phpGroupWare" />
		<meta name="robots" content="none" />
		<link rel="ICON" href="{img_icon}" type="image/x-ico" />
		<link rel="SHORTCUT ICON" href="{img_shortcut}" />
		{css}
		<link href="{theme_css}" type="text/css" rel="StyleSheet" />
		<title>{website_title}</title>
		{java_script}
		<script>
			//<![CDATA[
			//Parts based public doman code from ALA - http://www.alistapart.com/articles/dropdowns/
			function fixIE()
			{
				if( document.all && document.getElementById )
				{
					navRoot = document.getElementById('navbar_ul');
					for( i=0; i < navRoot.childNodes.length; i++ )
					{
						node = navRoot.childNodes[i];
						if( node.nodeName == 'LI' )
						{
							node.onmouseover=function()
							{
								this.className += ' over';
							}
							
							node.onmouseout=function()
							{
								this.className = this.className.replace(' over', '');
							}
							if( node.childNodes.length )
							{
								for( j=0; j < node.childNodes.length; j++)
								{
									subNode = node.childNodes[j];
									if( subNode.nodeName == 'UL' )
									{
										if( subNode.childNodes.length )
										{
											for( k = 0; k < subNode.childNodes.length; k++)
											{
												if( subNode.childNodes[k].nodeName == 'LI' )
												{
													subNode.childNodes[k].onmouseover=function()
													{
														this.className += ' over';
													}
													
													subNode.childNodes[k].onmouseout=function()
													{
														this.className = this.className.replace(' over', '');
													}

												}
											}
										}
									}

								}
							}
						}
					}
				}
			}
			
			function updateClock()
			{
				var oDate = new Date();
				
				strHr = oDate.getHours();
				strMin = ( oDate.getMinutes() < 10 ? "0" + oDate.getMinutes() : oDate.getMinutes() );
			
				document.getElementById('clock').innerHTML = strHr + ':' + strMin;

				setTimeout('updateClock()', 1000);
			}

			function initPage()
			{
				fixIE();
				updateClock();
			}
			//]]>
		</script>
</head>
<body {body_tags}>
<!-- END Head -->
