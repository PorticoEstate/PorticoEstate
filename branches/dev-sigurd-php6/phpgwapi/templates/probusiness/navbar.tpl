<!-- BEGIN navbar -->
<table class="workframe">
  <tr>
    <td class="APINavBar" align="center">
      <table class="APINavBar">
        <tr><td id="logo"><img src="{api_root}/images/logo.png" title="www.phpgroupware.org" alt="phpgroupware" /></td></tr>
        <tr><td>{switchlink}</td></tr>
		<!-- BEGIN app_row -->
        <tr>
			<td class="modBg">
				<a href="{url}"{class}><img src="{image}" alt="{text}"><br>{text}</a>
			</td>
		</tr>
		<!-- END app_row -->
      </table>
    </td>
    <td valign="top">
      <div align="center">
        <p class="current_app_header">{current_app_header}</p>
        {messages}<br />
        {sideboxcontent}
      </div>
<!-- END navbar -->

