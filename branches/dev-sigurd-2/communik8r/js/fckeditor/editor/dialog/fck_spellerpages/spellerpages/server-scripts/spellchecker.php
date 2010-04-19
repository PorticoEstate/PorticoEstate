<?php
	/**************************************************************************\
	* phpGroupWare - news_admin                                                *
	* http://www.phpgroupware.org                                              *
	* Written by Dave Hall skwashd at phpgroupware.org                         *
	* Portions Copyright 2005, Dave Hall                                       *
	* -----------------------------------------------                          *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: spellchecker.php,v 1.1.1.1 2005/08/23 05:03:06 skwashd Exp $ */

	/*
	* This is a rather heavily modified version of spellpages (spellpages.sf.net)
	* This version is based on the version which shipped with
	* FCKeditor (fckeditor.sf.net).  The interface will remains the same
	* but the internals now use php-pspell not the aspell cli.
	* Also this script is now tightly integrated with phpgw.
	*/
	
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'		=> 'communik8r',
		'noheader'		=> True,
		'nonavbar'		=> True,
		//'nocachecontrol'	=> True
	);
	include('../../../../../../../../header.inc.php');//yes boys and grrls that is how far down we are :P

	if ( !extension_loaded('pspell') )
	{
		$prefix = (PHP_SHLIB_SUFFIX == 'dll') ? 'php_' : '';
		if( !@dl( "{$prefix}pspell" . PHP_SHLIB_SUFFIX) )
		{
			error_handler( lang('error: php-pspell is not available, contact your system administrator') );
			exit;
		}
	}
	
	header('Content-type: text/html; charset=utf-8');

	//$spellercss = '/speller/spellerStyle.css';	// by FredCK
	$spellercss = '../spellerStyle.css';			// by FredCK
	//$word_win_src = '/speller/wordWindow.js';		// by FredCK
	$word_win_src = '../wordWindow.js';				// by FredCK
	
	# set the JavaScript variable to the submitted text.
	# textinputs is an array, each element corresponding to the (url-encoded)
	# value of the text control submitted for spell-checking
	function print_textinputs_var()
	{
		foreach( $_POST['textinputs'] as $key => $val )
		{
			echo "textinputs[$key] = decodeURIComponent(\"{$val}\");\n";
		}
	}

	# make declarations for the text input index
	function print_textindex_decl( $text_input_idx )
	{
		echo "words[$text_input_idx] = [];\n";
		echo "suggs[$text_input_idx] = [];\n";
	}

	# set an element of the JavaScript 'words' array to a misspelled word
	function print_words_elem( $word, $index )
	{
		echo "words[0][$index] = '" . escape_quote( $word ) . "';\n";
	}


	# set an element of the JavaScript 'suggs' array to a list of suggestions
	function print_suggs_elem( $suggs, $word_index )
	{
		if ( !is_array($suggs) )
		{
			$suggs = array();
		}
		
		$vals = array();
		foreach ( $suggs as $val )
		{
			if( $val )
			{
				$vals[] = "'" . escape_quote( $val ) . "'";
			}
		}
		echo "suggs[0][$word_index] = [" . implode(', ', $vals) . "];\n";
	}

	# escape single quote
	function escape_quote( $str )
	{
		return addslashes($str);
	}


	# handle a server-side error.
	function error_handler( $err )
	{
		echo "error = '" . escape_quote( $err ) . "';\n";
	}

	## get the list of misspelled words. Put the results in the javascript words array
	## for each misspelled word, get suggestions and put in the javascript suggs array
	function print_checker_results()
	{
		$lang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang']; //only for readability
		$dict = pspell_new($lang, '', '', 'utf-8');

		$text = urldecode( implode("\n", $_POST['textinputs']) );
		$words = array_flip( array_flip( preg_split('/[\W]+?/', $text) ) );
		$i = 0;
		print_textindex_decl(0);
		foreach ($words as $word)
		{
			if ( !pspell_check($dict, $word) )
			{
				print_words_elem($word, $i);
				print_suggs_elem( pspell_suggest($dict, $word), $i );
				++$i;
			}
		}
	}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="<?php echo $spellercss ?>" />
		<script language="javascript" src="<?php echo $word_win_src ?>"></script>
		<script language="javascript">
		<!--
			var suggs = new Array();
			var words = new Array();
			var textinputs = new Array();
			var error;
<?php
	print_textinputs_var();
	print_checker_results();
?>
			var wordWindowObj = new wordWindow();
			wordWindowObj.originalSpellings = words;
			wordWindowObj.suggestions = suggs;
			wordWindowObj.textInputs = textinputs;

			function init_spell()
			{
				// check if any error occured during server-side processing
				if( error )
				{
					alert( error );
				}
				else
				{
					// call the init_spell() function in the parent frameset
					if (parent.frames.length)
					{
						parent.init_spell( wordWindowObj );
					}
					else
					{
						alert('This page was loaded outside of a frameset. It might not display properly');
					}
				}
			}
		-->
		</script>

	</head>
	<!-- <body onLoad="init_spell();">		by FredCK -->
	<body onLoad="init_spell();" bgcolor="#ffffff">

		<script type="text/javascript">
		wordWindowObj.writeBody();
		</script>

	</body>
</html>
