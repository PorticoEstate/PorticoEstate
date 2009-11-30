<?php

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

class module_webalizer extends Module 
{
	function module_webalizer()
	{
		$this->arguments = array(
			'webalizer_url' => array(
				'type' => 'textfield', 
				'label' => lang('The relative url to webalizer (both must start and end with a slash /)')
			),
			'base_dir'	=> array(
					'type'	=> 'textfield',
					'label'	=> 'the base directory on the local filesystem for webalizer'
			)
		);
		$this->title = lang('Site Statistics');
		$this->description = lang('This module allows inclusion of webalizer statistics on a site');
	}

	function get_content(&$arguments,$properties)
	{
		$base_dir = $arguments['base_dir'];
		$webalizer_url = $arguments['webalizer_url'];

		if(!$_GET[stats_month])
		{
			$target = 'index.html';
		}
		else
		{
			list($month) = explode('.', $_GET['stats_month']);
			//$month = $_GET['stats_month'];
			$target = 'usage_' . $month . '.html';
		}

		if($stats = @file_get_contents($base_dir . $target))
		{
			$stats = preg_replace('/\r/', '', $stats);
			$stats = preg_replace('/\n/', '', $stats);
			preg_match('/\<BODY[^>]*\>\s*(.*?)\s*\<\/BODY\>/i', $stats, $match);
			$stats = preg_replace('/\<\/?BODY[^>]*\>/i', '', $match[0]);
			$stats = str_replace('A HREF="usage_', 'A HREF="stats?stats_month=', $stats);
			$stats = str_replace('IMG SRC="', 'IMG SRC="' . $webalizer_url, $stats);
			return $stats;
		}
		else
		{
			return lang('Statistics not available');
		}
	}
}
