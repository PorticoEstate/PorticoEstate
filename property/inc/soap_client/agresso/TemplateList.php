<?php

	class TemplateList
	{

		/**
		 * @var ArrayOfTemplateHeader $TemplateHeaderList
		 */
		protected $TemplateHeaderList = null;

		public function __construct()
		{

		}

		/**
		 * @return ArrayOfTemplateHeader
		 */
		public function getTemplateHeaderList()
		{
			return $this->TemplateHeaderList;
		}

		/**
		 * @param ArrayOfTemplateHeader $TemplateHeaderList
		 * @return TemplateList
		 */
		public function setTemplateHeaderList( $TemplateHeaderList )
		{
			$this->TemplateHeaderList = $TemplateHeaderList;
			return $this;
		}
	}