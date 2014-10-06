<?php
/**
 * HTML Sanitizer, attemtpts to make variables safe for users.
 * $Id$
 *
 * Taken from the horde project by Dave Hall for use in phpGroupWare
 *
 * Copyright 1999-2005 Anil Madhavapeddy <anil@recoil.org>
 * Copyright 1999-2005 Jon Parise <jon@horde.org>
 * Copyright 2002-2005 Michael Slusarz <slusarz@horde.org>
 * Portions Copyright 2005 Free Software Foundation Inc http://fsf.org
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Anil Madhavapeddy <anil@recoil.org>
 * @author  Jon Parise <jon@horde.org>
 * @author  Michael Slusarz <slusarz@horde.org>
 * @author  Dave Hall skwashd at phpgroupware.org
 * @since   phpGroupWare 0.9.16.007
 * @package phpgwapi
 * @subpackage utilities
 */
class data_cleaner
{
	/**
	* @var string $data the data
	*/
	var $data;
	
	/**
	* Constructor
	*
	* @param string $data the data to be cleaned
	*/
	function data_cleaner($data = '')
	{
		$this->data = $data;
	}

	/**
	 * Render out the currently set contents.
	 *
	 * @param String $data the raw data.
	 * @param bool $safe_redirect wrap uris in save redirect? should normally be true
	 *
	 * @return string  The cleaned data.
	 */
	function clean($data = null, $safe_redirect = True )
	{
		if ( !is_null($data) )
		{
			return $this->_clean_data($data, $safe_redirect);
		}
		return $this->_clean_data($this->data, $safe_redirect);
	}

	/**
	 * These regular expressions attempt to make HTML safe for
	 * viewing. THEY ARE NOT PERFECT. 
	 *
	 * @access private
	 *
	 * @param string $data  The HTML data.
	 *
	 * @return string  The cleaned HTML data.
	 */
	function _clean_data(&$data, $safe_redirect)
	{
		/* Deal with <base> tags in the HTML, since they will screw up
		 * our own relative paths. */
		if (($i = stristr($data, '<base ')) && ($i = stristr($i, 'http')) &&
				($j = strchr($i, '>'))) 
		{
			$base = substr($i, 0, strlen($i) - strlen($j));
			$base = preg_replace('|(http.*://[^/]*/?).*|i', '\1', $base);

			if ($base[strlen($base) - 1] != '/')
			{
				$base .= '/';
			}

			/* Recursively call this->_clean_data() to prevent clever fiends
			 * from sneaking nasty things into the page via $base. */
			$base = $this->_clean_data($base, $safe_redirect);
		}

		/* Removes HTML comments (including some scripts & styles). */
		$data = preg_replace('/<!--.*?-->/s', '', $data);

		/* Change space entities to space characters. */
		$data = preg_replace('/&#(x0*20|0*32);?/i', ' ', $data);

		/* Nuke non-printable characters (a play in three acts). */

		/* Rule 1). If we have a semicolon, it is deterministically
		 * detectable and fixable, without introducing collateral
		 * damage. */
		$data = preg_replace('/&#x?0*([9A-D]|1[0-3]);/i', '&nbsp;', $data);

		/* Rule 2). Hex numbers (usually having an x prefix) are also
		 * deterministic, even if we don't have the semi. Note that
		 * some browsers will treat &#a or &#0a as a hex number even
		 * without the x prefix; hence /x?/ which will cover those
		 * cases in this rule. */
		$data = preg_replace('/&#x?0*[9A-D]([^0-9A-F]|$)/i', '&nbsp\\1', $data);

		/* Rule 3). Decimal numbers without trailing semicolons. The
		 * problem is that some browsers will interpret &#10a as
		 * "\na", some as "&#x10a" so we have to clean the &#10 to be
		 * safe for the "\na" case at the expense of mangling a valid
		 * entity in other cases. (Solution for valid HTML authors:
		 * always use the semicolon.) */
		$data = preg_replace('/&#0*(9|1[0-3])([^0-9]|$)/i', '&nbsp\\2', $data);

		/* Remove overly long numeric entities. */
		$data = preg_replace('/&#x?0*[0-9A-F]{6,};?/i', '&nbsp;', $data);

		/* Remove everything outside of and including the <body> tag
		 * if displaying inline. */
		if (!isset($attachment) || !$attachment) {
			$data = preg_replace('/.*<body[^>]*>/si', '', $data);
			$data = preg_replace('/<\/body>.*/si', '', $data);
		}

		/* Get all attribute="javascript:foo()" tags. This is
		 * essentially the regex /(=|url\()("?)[^>]*script:/ but
		 * expanded to catch camouflage with spaces and entities. */
		$preg = '/((&#0*61;?|&#x0*3D;?|=)|' .
				'((u|&#0*85;?|&#x0*55;?|&#0*117;?|&#x0*75;?)\s*' .
					'(r|&#0*82;?|&#x0*52;?|&#0*114;?|&#x0*72;?)\s*' .
					'(l|&#0*76;?|&#x0*4c;?|&#0*108;?|&#x0*6c;?)\s*' .
					'(\()))\s*' .
			'(&#0*34;?|&#x0*22;?|"|&#0*39;?|&#x0*27;?|\')?' .
			'[^>]*\s*' .
			'(s|&#0*83;?|&#x0*53;?|&#0*115;?|&#x0*73;?)\s*' .
			'(c|&#0*67;?|&#x0*43;?|&#0*99;?|&#x0*63;?)\s*' .
			'(r|&#0*82;?|&#x0*52;?|&#0*114;?|&#x0*72;?)\s*' .
			'(i|&#0*73;?|&#x0*49;?|&#0*105;?|&#x0*69;?)\s*' .
			'(p|&#0*80;?|&#x0*50;?|&#0*112;?|&#x0*70;?)\s*' .
			'(t|&#0*84;?|&#x0*54;?|&#0*116;?|&#x0*74;?)\s*' .
			'(:|&#0*58;?|&#x0*3a;?)/i';
		$data = preg_replace($preg, '\1\8VarCleaned', $data);

		/* Get all on<foo>="bar()". NEVER allow these. */
		$data = preg_replace('/([\s"\']+' .
					'(o|&#0*79;?|&#0*4f;?|&#0*111;?|&#0*6f;?)' .
					'(n|&#0*78;?|&#0*4e;?|&#0*110;?|&#0*6e;?)' .
					'\w+)\s*=/i', '\1VarCleaned=', $data);

		/* Remove all scripts since they might introduce garbage if
		 * they are not quoted properly. */
		$data = preg_replace('|<script[^>]*>.*?</script>|is', '<VarCleaned_script />', $data);

		/* Get all tags that might cause trouble - <object>, <embed>,
		 * <base>, etc. Meta refreshes and iframes, too. */
		$malicious = array(
				'/<([^>a-z]*)' .
				'(s|&#0*83;?|&#x0*53;?|&#0*115;?|&#x0*73;?)\s*' .
				'(c|&#0*67;?|&#x0*43;?|&#0*99;?|&#x0*63;?)\s*' .
				'(r|&#0*82;?|&#x0*52;?|&#0*114;?|&#x0*72;?)\s*' .
				'(i|&#0*73;?|&#x0*49;?|&#0*105;?|&#x0*69;?)\s*' .
				'(p|&#0*80;?|&#x0*50;?|&#0*112;?|&#x0*70;?)\s*' .
				'(t|&#0*84;?|&#x0*54;?|&#0*116;?|&#x0*74;?)\s*/i',

				'/<([^>a-z]*)' .
				'(e|&#0*69;?|&#0*45;?|&#0*101;?|&#0*65;?)\s*' .
				'(m|&#0*77;?|&#0*4d;?|&#0*109;?|&#0*6d;?)\s*' .
				'(b|&#0*66;?|&#0*42;?|&#0*98;?|&#0*62;?)\s*' .
				'(e|&#0*69;?|&#0*45;?|&#0*101;?|&#0*65;?)\s*' .
				'(d|&#0*68;?|&#0*44;?|&#0*100;?|&#0*64;?)\s*/i',

				'/<([^>a-z]*)' .
				'(x|&#0*88;?|&#0*58;?|&#0*120;?|&#0*78;?)\s*' .
				'(m|&#0*77;?|&#0*4d;?|&#0*109;?|&#0*6d;?)\s*' .
				'(l|&#0*76;?|&#x0*4c;?|&#0*108;?|&#x0*6c;?)\s*/i',

				'/<([^>a-z]*)' .
					'(b|&#0*66;?|&#0*42;?|&#0*98;?|&#0*62;?)\s*' .
					'(a|&#0*65;?|&#0*41;?|&#0*97;?|&#0*61;?)\s*' .
					'(s|&#0*83;?|&#x0*53;?|&#0*115;?|&#x0*73;?)\s*' .
					'(e|&#0*69;?|&#0*45;?|&#0*101;?|&#0*65;?)\s*' .
					'[^line]/i',

				'/<([^>a-z]*)' .
					'(m|&#0*77;?|&#0*4d;?|&#0*109;?|&#0*6d;?)\s*' .
					'(e|&#0*69;?|&#0*45;?|&#0*101;?|&#0*65;?)\s*' .
					'(t|&#0*84;?|&#x0*54;?|&#0*116;?|&#x0*74;?)\s*' .
					'(a|&#0*65;?|&#0*41;?|&#0*97;?|&#0*61;?)\s*/i',

				'/<([^>a-z]*)' .
					'(j|&#0*74;?|&#0*4a;?|&#0*106;?|&#0*6a;?)\s*' .
					'(a|&#0*65;?|&#0*41;?|&#0*97;?|&#0*61;?)\s*' .
					'(v|&#0*86;?|&#0*56;?|&#0*118;?|&#0*76;?)\s*' .
					'(a|&#0*65;?|&#0*41;?|&#0*97;?|&#0*61;?)\s*/i',

				'/<([^>a-z]*)' .
					'(o|&#0*79;?|&#0*4f;?|&#0*111;?|&#0*6f;?)\s*' .
					'(b|&#0*66;?|&#0*42;?|&#0*98;?|&#0*62;?)\s*' .
					'(j|&#0*74;?|&#0*4a;?|&#0*106;?|&#0*6a;?)\s*' .
					'(e|&#0*69;?|&#0*45;?|&#0*101;?|&#0*65;?)\s*' .
					'(c|&#0*67;?|&#x0*43;?|&#0*99;?|&#x0*63;?)\s*' .
					'(t|&#0*84;?|&#x0*54;?|&#0*116;?|&#x0*74;?)\s*/i',

				'/<([^>a-z]*)' .
					'(i|&#0*73;?|&#x0*49;?|&#0*105;?|&#x0*69;?)\s*' .
					'(f|&#0*70;?|&#0*46;?|&#0*102;?|&#0*66;?)\s*' .
					'(r|&#0*82;?|&#x0*52;?|&#0*114;?|&#x0*72;?)\s*' .
					'(a|&#0*65;?|&#0*41;?|&#0*97;?|&#0*61;?)\s*' .
					'(m|&#0*77;?|&#0*4d;?|&#0*109;?|&#0*6d;?)\s*' .
					'(e|&#0*69;?|&#0*45;?|&#0*101;?|&#0*65;?)\s*/i');

		$data = preg_replace($malicious, '<VarCleaned_tag', $data);

		/* Comment out style/link tags. */
		$pattern = array('/\s+style\s*=/i',
				'|<style[^>]*>(?:\s*<\!--)*|i',
				'|(?:-->\s*)*</style>|i',
				'|(<link[^>]*>)|i');
		$replace = array(' VarCleaned=',
				'<!--',
				'-->',
				'<!-- $1 -->');
		$data = preg_replace($pattern, $replace, $data);

		/* A few other matches. */
		$pattern = array('|<([^>]*)&{.*}([^>]*)>|',
				'|<([^>]*)mocha:([^>]*)>|i',
				'|<([^>]*)binding:([^>]*)>|i');
		$replace = array('<&{;}\3>',
				'<\1VarCleaned:\2>',
				'<\1VarCleaned:\2>');
		$data = preg_replace($pattern, $replace, $data);

		/* Attempt to fix paths that were relying on a <base> tag. */
		if (!empty($base)) {
			$pattern = array('|src=(["\'])/|i',
					'|src=[^\'"]/|i',
					'|href= *(["\'])/|i',
					'|href= *[^\'"]/|i');
			$replace = array('src=\1' . $base,
					'src=' . $base,
					'href=\1' . $base,
					'href=' . $base);
			$data = preg_replace($pattern, $replace, $data);
		}

		if ( $safe_redirect )
		{
			/* Try to derefer all external references. */
			//XXX external references begin with http(s) isnt'it ? what should I do if it's not external ?? like href="/tata"
			// Just try to save a <a href="titi.org"> my site </a>
			// you get a <a href="/phpgw/redirect.php?go=titi.org"> my site </a>
			// Save a second time and you will get :
			// <a href="/phpgw/redirect.php?go=/phpgw/redirect.php?go=titi.org"> my site </a>
			// ....
			$data = preg_replace_callback('/href\s*=\s*([\\\]?["\']?)((?(1)[^\1]*?|[^\s]+))(?(1)\1|)/i',
				create_function('$m', 'return \'href="\' . (strlen($m[2]) && $m[2]{0} == \'#\' ? $m[2] : $GLOBALS[\'phpgw\']->safe_redirect(urldecode($m[2]))) . \'"\';'),
				$data);
		}
		return $data;
	}
}
?>
