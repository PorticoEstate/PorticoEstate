<?php
	interface booking_authorized_object
	{
		public function auth_role_has_access($role, $mode, array $object);
		public function auth_role_permissions(array $forObject);
	}