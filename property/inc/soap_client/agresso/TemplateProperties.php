<?php

	class TemplateProperties
	{

		/**
		 * @var string $AggregateId
		 */
		protected $AggregateId = null;

		/**
		 * @var string $ExtraTables
		 */
		protected $ExtraTables = null;

		/**
		 * @var string $ExtraWhere
		 */
		protected $ExtraWhere = null;

		/**
		 * @var string $Frame
		 */
		protected $Frame = null;

		/**
		 * @var string $Name
		 */
		protected $Name = null;

		/**
		 * @var string $Table1
		 */
		protected $Table1 = null;

		/**
		 * @var string $Table2
		 */
		protected $Table2 = null;

		/**
		 * @var string $Table3
		 */
		protected $Table3 = null;

		/**
		 * @var string $Version
		 */
		protected $Version = null;

		/**
		 * @var string $VersionMinor
		 */
		protected $VersionMinor = null;

		/**
		 * @var boolean $Aggregated
		 */
		protected $Aggregated = null;

		/**
		 * @var boolean $AutoFind
		 */
		protected $AutoFind = null;

		/**
		 * @var boolean $IsBrowseTable
		 */
		protected $IsBrowseTable = null;

		/**
		 * @var boolean $UseTable1
		 */
		protected $UseTable1 = null;

		/**
		 * @var boolean $UseTable2
		 */
		protected $UseTable2 = null;

		/**
		 * @var boolean $UseTable3
		 */
		protected $UseTable3 = null;

		/**
		 * @var int $DWPeriod
		 */
		protected $DWPeriod = null;

		/**
		 * @var int $StartLevel
		 */
		protected $StartLevel = null;

		/**
		 * @var int $MaxRows
		 */
		protected $MaxRows = null;

		/**
		 * @var int $TemplateId
		 */
		protected $TemplateId = null;

		/**
		 * @param boolean $Aggregated
		 * @param boolean $AutoFind
		 * @param boolean $IsBrowseTable
		 * @param boolean $UseTable1
		 * @param boolean $UseTable2
		 * @param boolean $UseTable3
		 * @param int $DWPeriod
		 * @param int $StartLevel
		 * @param int $MaxRows
		 * @param int $TemplateId
		 */
		public function __construct( $Aggregated, $AutoFind, $IsBrowseTable, $UseTable1, $UseTable2, $UseTable3, $DWPeriod, $StartLevel, $MaxRows, $TemplateId )
		{
			$this->Aggregated	 = $Aggregated;
			$this->AutoFind		 = $AutoFind;
			$this->IsBrowseTable = $IsBrowseTable;
			$this->UseTable1	 = $UseTable1;
			$this->UseTable2	 = $UseTable2;
			$this->UseTable3	 = $UseTable3;
			$this->DWPeriod		 = $DWPeriod;
			$this->StartLevel	 = $StartLevel;
			$this->MaxRows		 = $MaxRows;
			$this->TemplateId	 = $TemplateId;
		}

		/**
		 * @return string
		 */
		public function getAggregateId()
		{
			return $this->AggregateId;
		}

		/**
		 * @param string $AggregateId
		 * @return TemplateProperties
		 */
		public function setAggregateId( $AggregateId )
		{
			$this->AggregateId = $AggregateId;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getExtraTables()
		{
			return $this->ExtraTables;
		}

		/**
		 * @param string $ExtraTables
		 * @return TemplateProperties
		 */
		public function setExtraTables( $ExtraTables )
		{
			$this->ExtraTables = $ExtraTables;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getExtraWhere()
		{
			return $this->ExtraWhere;
		}

		/**
		 * @param string $ExtraWhere
		 * @return TemplateProperties
		 */
		public function setExtraWhere( $ExtraWhere )
		{
			$this->ExtraWhere = $ExtraWhere;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getFrame()
		{
			return $this->Frame;
		}

		/**
		 * @param string $Frame
		 * @return TemplateProperties
		 */
		public function setFrame( $Frame )
		{
			$this->Frame = $Frame;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName()
		{
			return $this->Name;
		}

		/**
		 * @param string $Name
		 * @return TemplateProperties
		 */
		public function setName( $Name )
		{
			$this->Name = $Name;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getTable1()
		{
			return $this->Table1;
		}

		/**
		 * @param string $Table1
		 * @return TemplateProperties
		 */
		public function setTable1( $Table1 )
		{
			$this->Table1 = $Table1;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getTable2()
		{
			return $this->Table2;
		}

		/**
		 * @param string $Table2
		 * @return TemplateProperties
		 */
		public function setTable2( $Table2 )
		{
			$this->Table2 = $Table2;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getTable3()
		{
			return $this->Table3;
		}

		/**
		 * @param string $Table3
		 * @return TemplateProperties
		 */
		public function setTable3( $Table3 )
		{
			$this->Table3 = $Table3;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getVersion()
		{
			return $this->Version;
		}

		/**
		 * @param string $Version
		 * @return TemplateProperties
		 */
		public function setVersion( $Version )
		{
			$this->Version = $Version;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getVersionMinor()
		{
			return $this->VersionMinor;
		}

		/**
		 * @param string $VersionMinor
		 * @return TemplateProperties
		 */
		public function setVersionMinor( $VersionMinor )
		{
			$this->VersionMinor = $VersionMinor;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getAggregated()
		{
			return $this->Aggregated;
		}

		/**
		 * @param boolean $Aggregated
		 * @return TemplateProperties
		 */
		public function setAggregated( $Aggregated )
		{
			$this->Aggregated = $Aggregated;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getAutoFind()
		{
			return $this->AutoFind;
		}

		/**
		 * @param boolean $AutoFind
		 * @return TemplateProperties
		 */
		public function setAutoFind( $AutoFind )
		{
			$this->AutoFind = $AutoFind;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getIsBrowseTable()
		{
			return $this->IsBrowseTable;
		}

		/**
		 * @param boolean $IsBrowseTable
		 * @return TemplateProperties
		 */
		public function setIsBrowseTable( $IsBrowseTable )
		{
			$this->IsBrowseTable = $IsBrowseTable;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getUseTable1()
		{
			return $this->UseTable1;
		}

		/**
		 * @param boolean $UseTable1
		 * @return TemplateProperties
		 */
		public function setUseTable1( $UseTable1 )
		{
			$this->UseTable1 = $UseTable1;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getUseTable2()
		{
			return $this->UseTable2;
		}

		/**
		 * @param boolean $UseTable2
		 * @return TemplateProperties
		 */
		public function setUseTable2( $UseTable2 )
		{
			$this->UseTable2 = $UseTable2;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getUseTable3()
		{
			return $this->UseTable3;
		}

		/**
		 * @param boolean $UseTable3
		 * @return TemplateProperties
		 */
		public function setUseTable3( $UseTable3 )
		{
			$this->UseTable3 = $UseTable3;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getDWPeriod()
		{
			return $this->DWPeriod;
		}

		/**
		 * @param int $DWPeriod
		 * @return TemplateProperties
		 */
		public function setDWPeriod( $DWPeriod )
		{
			$this->DWPeriod = $DWPeriod;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getStartLevel()
		{
			return $this->StartLevel;
		}

		/**
		 * @param int $StartLevel
		 * @return TemplateProperties
		 */
		public function setStartLevel( $StartLevel )
		{
			$this->StartLevel = $StartLevel;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getMaxRows()
		{
			return $this->MaxRows;
		}

		/**
		 * @param int $MaxRows
		 * @return TemplateProperties
		 */
		public function setMaxRows( $MaxRows )
		{
			$this->MaxRows = $MaxRows;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getTemplateId()
		{
			return $this->TemplateId;
		}

		/**
		 * @param int $TemplateId
		 * @return TemplateProperties
		 */
		public function setTemplateId( $TemplateId )
		{
			$this->TemplateId = $TemplateId;
			return $this;
		}
	}