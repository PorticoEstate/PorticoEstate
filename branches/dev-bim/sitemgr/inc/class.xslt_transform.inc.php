<?php 

class xslt_transform
{
	var $arguments;

	function xslt_transform($xsltfile,$xsltparameters=NULL)
	{
		$this->xsltfile = $xsltfile;
		$this->xsltparameters = $xsltparameters;
	}

	function apply_transform($title,$content)
	{
		$xh = xslt_create();
		$xsltarguments = array('/_xml' => $content);
		$result = xslt_process($xh, 'arg:/_xml', $this->xsltfile, NULL, $xsltarguments,$this->xsltparameters);
		xslt_free($xh);
		return $result;
	}
}
