<?php
	phpgw::import_class('booking.sopermission');

	/**
	 * This class models the recursive case of permissions for permissions themselves.
	 */
	class booking_sopermission_permission extends booking_sopermission
	{

		/**
		 * Override to return nothing as a permission itself is not related
		 * to any object other than itself.
		 *
		 * @see booking_sopermission
		 */
		protected function build_object_relations()
		{
			return null;
		}
	}