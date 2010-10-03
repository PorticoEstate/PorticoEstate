<?php
	/*******************************************************************************\
	* phpGroupWare - sitemgr - external html module					*
	* http://www.phpgroupware.org							*
	* Written by Dave Hall skwashd at phpgroupware.org				*
	* Copyright (C) 2004 Free Software Foundation Inc.				*
	* --------------------------------------------					*
	* Development of this module sponsored by					*
	*	General Pants Co. - http://generalpants.com.au				*
	* --------------------------------------------					*
	*  This program is free software; you can redistribute it and/or modify it	*
	*  under the terms of the GNU General Public License as published by the	*
	*  Free Software Foundation; either version 2 of the License, or (at your	*
	*  option) any later version.							*
	\*******************************************************************************/

if (!function_exists('file_get_contents'))
{
        function file_get_contents($filename, $use_include_path = 0)
	{
		$file = @fopen($filename, 'rb', $use_include_path);
		if ($file)
		{
			if ($fsize = @filesize($filename))
			{
				$data = fread($file, $fsize);
			}
			else
			{
				while (!feof($file))
				{
					$data .= fread($file, 1024);
				}
			}
			fclose($file);
		}
		return $data;
	}
} 

class module_external_html extends Module 
{
	function module_external_html()
	{
		$this->arguments = array(
			'filename' => array(
				'type' => 'textfield', 
				'label' => lang('The full path to the html document (must start with a slash /)')
			),
		);
		$this->title = lang('External HTML');
		$this->description = lang('This module allows inclusion of external static html files, the contents will be everything between the &gt;body&lt;');
	}

	function get_content(&$arguments,$properties)
	{
		if($page = @file_get_contents($arguments['filename']))
		{
			$page = preg_replace('/\r/', '', $page);
			$page = preg_replace('/\n/', '', $page);
			preg_match('/\<BODY[^>]*\>\s*(.*?)\s*\<\/BODY\>/i', $page, $match);
			$page = preg_replace('/\<\/?BODY[^>]*\>/i', '', $match[0]);
			return $page;
		}
		else
		{
			return lang('404 Page not Found!');
		}
	}
}
