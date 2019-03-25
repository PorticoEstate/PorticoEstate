<?php

	class FormatInfo
	{

		/**
		 * @var ArrayOfFormatProperties $FormatPropertiesList
		 */
		protected $FormatPropertiesList = null;

		public function __construct()
		{

		}

		/**
		 * @return ArrayOfFormatProperties
		 */
		public function getFormatPropertiesList()
		{
			return $this->FormatPropertiesList;
		}

		/**
		 * @param ArrayOfFormatProperties $FormatPropertiesList
		 * @return FormatInfo
		 */
		public function setFormatPropertiesList( $FormatPropertiesList )
		{
			$this->FormatPropertiesList = $FormatPropertiesList;
			return $this;
		}
	}