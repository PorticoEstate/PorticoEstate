<?php

class booking_errorstack extends ArrayObject
{
	public function to_flash_error_msgs()
	{
		$flash_msgs = array();
		
		foreach($this as $key => $value) {
			if (is_array($value) || $value instanceof ArrayAccess) {
				foreach($value as $msg) $flash_msgs[$msg] = false;
			} else {
				$flash_msgs[$value] = false;
			}
		}
		
		return $flash_msgs;
	}
	
	public function offsetSet($index, $newval)
    {
		if (!isset($this[$index])) {
			parent::offsetSet($index, array($newval));
		} else {
			$data = $this[$index][] = $newval;
		}
	}
}

// $es = new booking_boerrorstack();
// $es['field'] = 'f once';
// $es['field'] = 'f twice';
// 
// $es['other'] = 'o once';
// 
// var_export($es->to_flash_error_msgs());