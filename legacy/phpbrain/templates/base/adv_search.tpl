<h2>{lang_advanced_search}</h2>
<form name="adv_search_form" method="post" action="{form_action}">
	<fieldset>
		<label for="all_words">{lang_all_words}:</label>
		<input type="text" size=50 name="all_words" id="all_words"><br />
	
		<label for="phrase">{lang_phrase}:</label>
		<input type="text" size=50 name="phrase" id="phrase"><br />
	
		<label for="one_word">{lang_one_word}:</label>
		<input type="text" size=50 name="one_word" id="one_word"><br />
	
		<label for="without_words">{lang_without_word}</label>
		<input type="text" size=50 name="without_words" id="without_words"><br />
	</fieldset>

	<label for="cat">{lang_show_cats}:</label>
	<select name="cat" id="cat">
		<option value="0">{lang_all}</option>
		{select_categories}
	</select>
	<input type="checkbox" name="include_subs" id="include_subs" value="1" class="check" />
	<label for="include_subs" class="even">{lang_include_subs}</label><br />

	<label for="pub_date">{lang_pub_date}:</label>
	<select name="pub_date" id="pub_name">
		<option value="0" selected>{lang_anytime}</option>
		<option value="3">{lang_3_months}</option>
		<option value="6">{lang_6_months}</option>
		<option value="year">{lang_past_year}</option>
	</select>

	<label for="ocurrences">{lang_ocurrences}:</label>
	<select name="ocurrences" id="ocurrences">
		<option value="0" selected>{lang_anywhere}</option>
		<option value="title">{lang_in_title}</option>
		<option value="topic">{lang_in_topic}</option>
		<option value="text">{lang_in_text}</option>
	</select><br />

	<label for="num_res">{lang_num_res}:</label>
	<select name="num_res" id="num_res">
		<option value="0" selected>{lang_user_prefs}</option>
		<option value="10">10</option>
		<option value="20">20</option>
		<option value="30">30</option>
		<option value="50">50</option>
		<option value="100">100</option>
	</select><br />

	<label for="order">{lang_order}:</label>
	<select name="order">
		<option value="created" selected>{lang_created}</option>
		<option value="art_id">{lang_artid}</option>
		<option value="title">{lang_title}</option>
		<option value="user_id">{lang_user}</option>
		<option value="modified">{lang_modified}</option>
	</select>
	<select name="sort">
		<option value="DESC" selected>{lang_desc}</option>
		<option value="ASC">{lang_asc}</option>
	</select><br />

	<button type="submit" name="adv_search" value="1">
		{lang_search}
	</button>
</form>