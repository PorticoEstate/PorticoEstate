<?php
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'		=> true,
		'nonavbar'		=> true,
		'currentapp'	=> 'property'
	);

	include_once('../header.inc.php');

	$html = '';
	$content = html_entity_decode(phpgw::get_var('content'));
	if($content)
	{
		$wiki_parser	= CreateObject('phpgwapi.wiki2html');
		$syntax 		= phpgw::get_var('syntax');
		$wiki_parser->set_syntax($syntax);
		$html			.=  $wiki_parser->process($content);

//		Alternative call - given default syntax textile
//		$html			=  execMethod('phpgwapi.wiki2html.process',$content);
	}

	$checked_textile = $syntax == 'textile' || !$syntax ? 'checked' : ''; //default
	$checked_markdown = $syntax == 'markdown' ? 'checked' : '';

	$content = $_POST ? $content : <<<content
h1. Title level 1

h2. Title level 2

h3. Title level 3

h4. Title level 4

h5. Title level 5

h6. Title level 6

h3. Table

|_. ID|_. Name|_. Location|
|24|Eric|Paris|
|28|Olivia|Paris|

h3. List

# Lorem ipsum
** Dolor sit amet
** Bloubu boulga
# Consectetur adipiscing

h3. Link

["link til syntax":http://demo.textilewiki.com/theme-default/] Textile Wiki is a simple wiki using Textile syntax.

h3. Phrase modifiers

Lorem *ipsum* dolor sit amet, consectetur adipiscing elit.

Lorem _ipsum_ dolor sit amet, consectetur adipiscing elit.

Lorem ^ipsum^ dolor sit amet, consectetur adipiscing elit.

Lorem ~ipsum~ dolor sit amet, consectetur adipiscing elit.

h3. Block quotations

bq. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ut diam non enim molestie gravida vitae eu diam. Ut viverra neque sit amet turpis tempor hendrerit. Aenean massa metus, congue a vehicula a, vehicula sit amet lorem. Vestibulum suscipit arcu at ipsum consequat vitae iaculis tellus iaculis.

h3. Paragraphs

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur at libero ut leo lobortis vehicula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus ut aliquet nisl. Etiam non nisi vel nisi sollicitudin ornare id et turpis. Integer ac mauris id mi suscipit sollicitudin a quis leo.

h3. Left alignment

p<. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur at libero ut leo lobortis vehicula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus ut aliquet nisl. Etiam non nisi vel nisi sollicitudin ornare id et turpis. Integer ac mauris id mi suscipit sollicitudin a quis leo.

h3. Right alignment

p>. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur at libero ut leo lobortis vehicula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus ut aliquet nisl. Etiam non nisi vel nisi sollicitudin ornare id et turpis. Integer ac mauris id mi suscipit sollicitudin a quis leo.

h3. Centered

p=. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur at libero ut leo lobortis vehicula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus ut aliquet nisl. Etiam non nisi vel nisi sollicitudin ornare id et turpis. Integer ac mauris id mi suscipit sollicitudin a quis leo.

h3. Justify

p<>. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur at libero ut leo lobortis vehicula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus ut aliquet nisl. Etiam non nisi vel nisi sollicitudin ornare id et turpis. Integer ac mauris id mi suscipit sollicitudin a quis leo.
content;

	$html .= <<<HTML
				<form action="{$action}" method="post" enctype="multipart/form-data">
					<fieldset>
						<p>
							<label for="content">Edit wiki:</label>
							<textarea cols="100" rows="20" id="content" name="content" wrap="virtual" title="wiki test">
{$content}
							</textarea>

						</p>
						<p>
							<label for="syntax">Textile</label>
						</p>
						<p>
							<input type="radio" name="syntax" value="textile" {$checked_textile}>
						</p>
						<p>
							<label for="syntax">markdown</label>
						</p>
						<p>
							<input type="radio" name="syntax" value="markdown" {$checked_markdown}>
						</p>
						<p>
							<input type="submit" name="importsubmit" value="send"  />
						</p>
		 			</fieldset>
				</form>
HTML;

	echo $html;
