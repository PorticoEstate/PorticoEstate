<?php

	class booking_errorstack extends ArrayObject
	{

		public function to_flash_error_msgs()
		{
			$flash_msgs = array();

			foreach ($this as $key => $value)
			{
				if (is_array($value) || $value instanceof ArrayAccess)
				{
					foreach ($value as $msg)
						$flash_msgs[$msg] = false;
				}
				else
				{
					$flash_msgs[$value] = false;
				}
			}

			return $flash_msgs;
		}

		public function offsetSet( $field, $error )
		{
			if (!isset($this[$field]))
			{
				parent::offsetSet($field, array($error));
			}
			else
			{
				$field_errors = $this[$field];
				$field_errors[] = $error;
				parent::offsetSet($field, $field_errors);
			}
		}
	}