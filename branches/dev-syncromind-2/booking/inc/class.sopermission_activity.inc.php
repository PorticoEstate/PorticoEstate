<?php
	phpgw::import_class('booking.sopermission');

	class booking_sopermission_activity extends booking_sopermission
	{

		/**
		 * Override to return nothing as were not interested in 
		 * any data from the object
		 *
		 * @see booking_sopermission
		 */
		protected function build_object_relations()
		{
			return null;
		}
	}