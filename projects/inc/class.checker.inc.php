<?php

class checker
{
	var $errorMsg = array();

	function checker()
	{

	}

	function checkProjectNr( $pNr = '' )
	{
		if ( !$pNr )
		{
			$this->setErrorMsg( lang('Please enter the project id') );

			return false;
		}

		if ( strlen($pNr) > 250 )
		{
			$this->setErrorMsg( lang('id can not exceed 250 characters in length') );

			return false;
		}

		return true;
	}

	function setErrorMsg( $errorMsg )
	{
		$this->errorMsg[] = $errorMsg;
	}

	function getLastErrorMsg()
	{
		$i = count($this->errorMsg) - 1;

		if( $i >= 0 )
		{
			return $this->errorMsg[$i];
		}
		else
		{
			return '';
		}
	}

	function getErrorMsg()
	{
		return $this->errorMsg;
	}
}
?>