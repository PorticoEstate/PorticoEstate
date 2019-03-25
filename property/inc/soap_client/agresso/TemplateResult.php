<?php

	class TemplateResult
	{

		/**
		 * @var string $schema
		 */
		protected $schema = null;

		/**
		 * @var string $any
		 */
		protected $any = null;

		/**
		 * @param string $schema
		 * @param string $any
		 */
		public function __construct( $schema, $any )
		{
			$this->schema	 = $schema;
			$this->any		 = $any;
		}

		/**
		 * @return string
		 */
		public function getSchema()
		{
			return $this->schema;
		}

		/**
		 * @param string $schema
		 * @return TemplateResult
		 */
		public function setSchema( $schema )
		{
			$this->schema = $schema;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getAny()
		{
			return $this->any;
		}

		/**
		 * @param string $any
		 * @return TemplateResult
		 */
		public function setAny( $any )
		{
			$this->any = $any;
			return $this;
		}
	}