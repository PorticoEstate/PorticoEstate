{message}
{search_tpl}
<div id="kb_article_summaries">
	<div>
	<h2>{lang_latest}</h2>
	<ol id="kb_latest">
		<!-- BEGIN articles_latest_block -->
		<li>
			<a href="{art_href}">{art_title} </a><span class="kb_article_date">({art_date})</span><br />
			<span class="article_cat">{art_category}</span>
		</li>
		<!-- END articles_latest_block -->
	</ol>
	</div> 		
	
	<h2>{lang_most_viewed}</h2>
	<ol id="kb_most_viewed">
		<!-- BEGIN articles_mostviewed_block -->
		<li>
			<a href="{art_href}">{art_title} </a><span class="kb_article_date">({art_date})</span><br />
			<span class="article_cat">{art_category}</span>
		</li>
		<!-- END articles_mostviewed_block -->
	</ol>

	<h2>{lang_unanswered}</h2>
	<ol id="kb_unanswered">
		<!-- BEGIN unanswered_questions_block -->
		<li>
			<a href="{art_href}">{art_title} </a><span class="kb_article_date">({art_date})</span><br />
			<span class="article_cat">{art_category}</span>
		</li>
		<!-- END unanswered_questions_block -->
	</ol>
</div>
<div id="kb_main">
	<div id="kb_cats">
		<h2>{browse_cats}</h2>
		<h3>{path}</h3>
		{categories}
	</div>
	
	<div id="kb_articles">
		<h2>{lang_articles}</h2>
		<!-- BEGIN articles_navigation_block -->
		<ul id="kb_article_nav">
			{left}
			{num_regs}
			{right}
		</ul>
		<!-- END articles_navigation_block -->
	</div>

	<div class="article_list_item">
	<!-- BEGIN articles_block -->
		<em class="kbnum">({art_num}) </em><a href="{art_href}">{art_title}</a><br />
		<span>{lang_last_modified}: {art_date} - {img_stars} {attachment}</span>
		<span class="article_cat">{art_category}</span>
		<p>
		{art_topic}
	<!-- END articles_block -->
	</div>
</div>