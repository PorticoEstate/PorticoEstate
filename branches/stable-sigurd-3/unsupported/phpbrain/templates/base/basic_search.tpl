<div class="basic_search">
	<h2>{lang_search_kb}</h2>
	<form name="search" method="POST" action="{form_search_action}">
		<label for="kb_query" style="width: auto;">{lang_enter_words}:</label><br />
		<input type="text" id="kb_query" name="query" value="{query_value}"/>
		<button type="submit" name="search" value="0" onclick="this.value=1;">
			{lang_search}
		</button>
		<a href="{link_adv_search}">{lang_advanced_search}</a><br />
	</form>
</div>