<?php
	phpgw::import_class('booking.async_task');

	class booking_async_task_delete_expired_blocks extends booking_async_task
	{

		public function get_default_times()
		{
			return array( 'min' => '*/5');
		}

		public function run( $options = array() )
		{
			/**
			 * Transaction started in asyncservice
			 */
			$in_transaction = true;
			CreateObject('booking.soblock')->delete_expired($in_transaction);
		}
	}
