<?php

	class WSCredentials
	{

		/**
		 * @var string $Username
		 */
		protected $Username = null;

		/**
		 * @var string $Client
		 */
		protected $Client = null;

		/**
		 * @var string $Password
		 */
		protected $Password = null;

		public function __construct()
		{

		}

		/**
		 * @return string
		 */
		public function getUsername()
		{
			return $this->Username;
		}

		/**
		 * @param string $Username
		 * @return WSCredentials
		 */
		public function setUsername( $Username )
		{
			$this->Username = $Username;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getClient()
		{
			return $this->Client;
		}

		/**
		 * @param string $Client
		 * @return WSCredentials
		 */
		public function setClient( $Client )
		{
			$this->Client = $Client;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getPassword()
		{
			return $this->Password;
		}

		/**
		 * @param string $Password
		 * @return WSCredentials
		 */
		public function setPassword( $Password )
		{
			$this->Password = $Password;
			return $this;
		}
	}