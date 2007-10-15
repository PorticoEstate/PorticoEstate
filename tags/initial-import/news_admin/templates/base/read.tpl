<div id="app_menu_bar">
	<ul>
		<li style="text-align:left;">
			<!-- BEGIN less_li -->
			<a href="{lesslink}">&lt;&lt;&lt;</a>
			<!-- END less_li -->
		</li>
		<!-- BEGIN cat_form -->
		<li>
			<form method="POST">
				<select name="cat_id">
					<!-- BEGIN cat_option -->
					<option value="{cat_id}" {selected}>{cat_name}</option>
					<!-- END cat_option -->
				</select><input type="submit" value="{lang_go}" />
			 </form>
		 </li>
		<!-- END cat_form -->
		<li>
			<!-- BEGIN maintain_li -->
			<a href="{href_maintain}">{lang_maintain}</a>
			<!-- END maintain_li -->
		</li>
		<li>
			<!-- BEGIN newsletter_li -->
			<a href="{href_newsletter}">{lang_newsletter}</a>
			<!-- END newsletter_li -->
		</li>
		<li style="text-align:right;">
			<!-- BEGIN more_li -->
			<a href="{morelink}">&gt;&gt;&gt;</a>
			<!-- END more_li -->
		</li>
	</ul>
</div>

<!-- BEGIN news_summary -->
<h1>{cat_name}</h1>
<!-- BEGIN summary_item -->
<div class="news_item">
	<h2>{subject}</h2>
	<p class="summary">
		{summary}
		<a href="{href_read}">{lang_read}</a>
	</p>
	<p class="info">{submission}</p>
</div>
<!-- END summary_item -->
<!-- END news_summary -->

<!-- BEGIN news_item -->
<h1>{subject}</h1>
<div class="news_item">
	<p class="content">
		{content}
	</p>
	<p class="info">{submission}</p>
</div>
<!-- END news_item -->
