<?php

	class SearchCriteriaProperties
	{

		/**
		 * @var string $ColumnName
		 */
		protected $ColumnName = null;

		/**
		 * @var string $Description
		 */
		protected $Description = null;

		/**
		 * @var string $RestrictionType
		 */
		protected $RestrictionType = null;

		/**
		 * @var string $FromValue
		 */
		protected $FromValue = null;

		/**
		 * @var string $ToValue
		 */
		protected $ToValue = null;

		/**
		 * @var int $DataType
		 */
		protected $DataType = null;

		/**
		 * @var int $DataLength
		 */
		protected $DataLength = null;

		/**
		 * @var int $DataCase
		 */
		protected $DataCase = null;

		/**
		 * @var boolean $IsParameter
		 */
		protected $IsParameter = null;

		/**
		 * @var boolean $IsVisible
		 */
		protected $IsVisible = null;

		/**
		 * @var boolean $IsPrompt
		 */
		protected $IsPrompt = null;

		/**
		 * @var boolean $IsMandatory
		 */
		protected $IsMandatory = null;

		/**
		 * @var boolean $CanBeOverridden
		 */
		protected $CanBeOverridden = null;

		/**
		 * @var string $RelDateCrit
		 */
		protected $RelDateCrit = null;

		/**
		 * @param int $DataType
		 * @param int $DataLength
		 * @param int $DataCase
		 * @param boolean $IsParameter
		 * @param boolean $IsVisible
		 * @param boolean $IsPrompt
		 * @param boolean $IsMandatory
		 * @param boolean $CanBeOverridden
		 */
		public function __construct( $DataType, $DataLength, $DataCase, $IsParameter, $IsVisible, $IsPrompt, $IsMandatory, $CanBeOverridden )
		{
			$this->DataType			 = $DataType;
			$this->DataLength		 = $DataLength;
			$this->DataCase			 = $DataCase;
			$this->IsParameter		 = $IsParameter;
			$this->IsVisible		 = $IsVisible;
			$this->IsPrompt			 = $IsPrompt;
			$this->IsMandatory		 = $IsMandatory;
			$this->CanBeOverridden	 = $CanBeOverridden;
		}

		/**
		 * @return string
		 */
		public function getColumnName()
		{
			return $this->ColumnName;
		}

		/**
		 * @param string $ColumnName
		 * @return SearchCriteriaProperties
		 */
		public function setColumnName( $ColumnName )
		{
			$this->ColumnName = $ColumnName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getDescription()
		{
			return $this->Description;
		}

		/**
		 * @param string $Description
		 * @return SearchCriteriaProperties
		 */
		public function setDescription( $Description )
		{
			$this->Description = $Description;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getRestrictionType()
		{
			return $this->RestrictionType;
		}

		/**
		 * @param string $RestrictionType
		 * @return SearchCriteriaProperties
		 */
		public function setRestrictionType( $RestrictionType )
		{
			$this->RestrictionType = $RestrictionType;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getFromValue()
		{
			return $this->FromValue;
		}

		/**
		 * @param string $FromValue
		 * @return SearchCriteriaProperties
		 */
		public function setFromValue( $FromValue )
		{
			$this->FromValue = $FromValue;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getToValue()
		{
			return $this->ToValue;
		}

		/**
		 * @param string $ToValue
		 * @return SearchCriteriaProperties
		 */
		public function setToValue( $ToValue )
		{
			$this->ToValue = $ToValue;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getDataType()
		{
			return $this->DataType;
		}

		/**
		 * @param int $DataType
		 * @return SearchCriteriaProperties
		 */
		public function setDataType( $DataType )
		{
			$this->DataType = $DataType;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getDataLength()
		{
			return $this->DataLength;
		}

		/**
		 * @param int $DataLength
		 * @return SearchCriteriaProperties
		 */
		public function setDataLength( $DataLength )
		{
			$this->DataLength = $DataLength;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getDataCase()
		{
			return $this->DataCase;
		}

		/**
		 * @param int $DataCase
		 * @return SearchCriteriaProperties
		 */
		public function setDataCase( $DataCase )
		{
			$this->DataCase = $DataCase;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getIsParameter()
		{
			return $this->IsParameter;
		}

		/**
		 * @param boolean $IsParameter
		 * @return SearchCriteriaProperties
		 */
		public function setIsParameter( $IsParameter )
		{
			$this->IsParameter = $IsParameter;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getIsVisible()
		{
			return $this->IsVisible;
		}

		/**
		 * @param boolean $IsVisible
		 * @return SearchCriteriaProperties
		 */
		public function setIsVisible( $IsVisible )
		{
			$this->IsVisible = $IsVisible;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getIsPrompt()
		{
			return $this->IsPrompt;
		}

		/**
		 * @param boolean $IsPrompt
		 * @return SearchCriteriaProperties
		 */
		public function setIsPrompt( $IsPrompt )
		{
			$this->IsPrompt = $IsPrompt;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getIsMandatory()
		{
			return $this->IsMandatory;
		}

		/**
		 * @param boolean $IsMandatory
		 * @return SearchCriteriaProperties
		 */
		public function setIsMandatory( $IsMandatory )
		{
			$this->IsMandatory = $IsMandatory;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getCanBeOverridden()
		{
			return $this->CanBeOverridden;
		}

		/**
		 * @param boolean $CanBeOverridden
		 * @return SearchCriteriaProperties
		 */
		public function setCanBeOverridden( $CanBeOverridden )
		{
			$this->CanBeOverridden = $CanBeOverridden;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getRelDateCrit()
		{
			return $this->RelDateCrit;
		}

		/**
		 * @param string $RelDateCrit
		 * @return SearchCriteriaProperties
		 */
		public function setRelDateCrit( $RelDateCrit )
		{
			$this->RelDateCrit = $RelDateCrit;
			return $this;
		}
	}