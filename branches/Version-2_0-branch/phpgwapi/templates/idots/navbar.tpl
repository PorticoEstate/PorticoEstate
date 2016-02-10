<!-- BEGIN navbar_header -->
<div id="box">
	<div id="navpanel">
		<ul id="nav">
			<li class="phpgw_logo">
				<a href="http://www.phpgroupware.org" rel="external"><img src="{img_root}/logo.png" alt="phpGroupWare Logo" title="www.phpGroupWare.org"></a>
			</li>
			<!-- BEGIN navbar_item -->
			<li>
				<a href="{url_app}">
					<img src="{img_app}" alt="{alt_img_app}"><br>
					{app_name}
				</a>
			</li>
			<!-- END navbar_item -->
		</ul>
	</div>
	<div class="mainnote">
			<div id="user_info">{user_info}</div>
			<div>{current_users}</div>
	</div>
	<div id="sidecontent">
<!-- END navbar_header -->

<!-- BEGIN navbar_footer -->
	</div>
	<div id="{content_class}">
		<h1 class="articletitle">{current_app_title}</h1>
{menubar}
		<div id="articlecontent">
<!-- END navbar_footer -->

<!-- BEGIN extra_blocks_header -->
		<div class="sidebox">
			<h2 class="sideboxtitle">{lang_title}</h2>
			<ul class="sideboxcontent">
<!-- END extra_blocks_header -->

<!-- BEGIN extra_blocks_footer -->
			</ul>
		</div>
<!-- END extra_blocks_footer -->

<!-- BEGIN extra_block_row -->
				<li><a href="{item_link}">{lang_item}</a></li>
<!-- END extra_block_row -->
<!-- BEGIN extra_blocks_menu -->
		{app_menu}
<!-- END extra_blocks_menu -->

