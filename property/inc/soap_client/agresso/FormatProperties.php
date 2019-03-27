<?php

	class FormatProperties
	{

		/**
		 * @var string $AmountDisplayFormat
		 */
		protected $AmountDisplayFormat = null;

		/**
		 * @var string $AttributeId
		 */
		protected $AttributeId = null;

		/**
		 * @var string $BreakColName
		 */
		protected $BreakColName = null;

		/**
		 * @var string $BreakText
		 */
		protected $BreakText = null;

		/**
		 * @var string $Color
		 */
		protected $Color = null;

		/**
		 * @var string $ColumnHeaderText
		 */
		protected $ColumnHeaderText = null;

		/**
		 * @var string $ColumnName
		 */
		protected $ColumnName = null;

		/**
		 * @var string $ConditionType
		 */
		protected $ConditionType = null;

		/**
		 * @var string $FontFamily
		 */
		protected $FontFamily = null;

		/**
		 * @var string $Formula
		 */
		protected $Formula = null;

		/**
		 * @var string $SectionType
		 */
		protected $SectionType = null;

		/**
		 * @var string $SectionFormatID
		 */
		protected $SectionFormatID = null;

		/**
		 * @var string $Type
		 */
		protected $Type = null;

		/**
		 * @var boolean $Break
		 */
		protected $Break = null;

		/**
		 * @var boolean $ConditionInUse
		 */
		protected $ConditionInUse = null;

		/**
		 * @var boolean $ConditionOnlyBreaks
		 */
		protected $ConditionOnlyBreaks = null;

		/**
		 * @var boolean $FontBold
		 */
		protected $FontBold = null;

		/**
		 * @var boolean $FontItalic
		 */
		protected $FontItalic = null;

		/**
		 * @var boolean $FontStrikeOut
		 */
		protected $FontStrikeOut = null;

		/**
		 * @var boolean $FontUnderline
		 */
		protected $FontUnderline = null;

		/**
		 * @var boolean $Show
		 */
		protected $Show = null;

		/**
		 * @var boolean $ShowText
		 */
		protected $ShowText = null;

		/**
		 * @var int $ConditionLevel
		 */
		protected $ConditionLevel = null;

		/**
		 * @var int $DataCase
		 */
		protected $DataCase = null;

		/**
		 * @var int $DataLength
		 */
		protected $DataLength = null;

		/**
		 * @var int $DataType
		 */
		protected $DataType = null;

		/**
		 * @var int $DisplayLength
		 */
		protected $DisplayLength = null;

		/**
		 * @var int $DisplayOrder
		 */
		protected $DisplayOrder = null;

		/**
		 * @var int $SortOrder
		 */
		protected $SortOrder = null;

		/**
		 * @var int $TextDisplayLength
		 */
		protected $TextDisplayLength = null;

		/**
		 * @var int $SequenceNo
		 */
		protected $SequenceNo = null;

		/**
		 * @var float $ConditionFrom
		 */
		protected $ConditionFrom = null;

		/**
		 * @var float $ConditionTo
		 */
		protected $ConditionTo = null;

		/**
		 * @param boolean $Break
		 * @param boolean $ConditionInUse
		 * @param boolean $ConditionOnlyBreaks
		 * @param boolean $FontBold
		 * @param boolean $FontItalic
		 * @param boolean $FontStrikeOut
		 * @param boolean $FontUnderline
		 * @param boolean $Show
		 * @param boolean $ShowText
		 * @param int $ConditionLevel
		 * @param int $DataCase
		 * @param int $DataLength
		 * @param int $DataType
		 * @param int $DisplayLength
		 * @param int $DisplayOrder
		 * @param int $SortOrder
		 * @param int $TextDisplayLength
		 * @param int $SequenceNo
		 * @param float $ConditionFrom
		 * @param float $ConditionTo
		 */
		public function __construct( $Break, $ConditionInUse, $ConditionOnlyBreaks, $FontBold, $FontItalic, $FontStrikeOut, $FontUnderline, $Show, $ShowText, $ConditionLevel, $DataCase, $DataLength, $DataType, $DisplayLength, $DisplayOrder, $SortOrder, $TextDisplayLength, $SequenceNo, $ConditionFrom, $ConditionTo )
		{
			$this->Break				 = $Break;
			$this->ConditionInUse		 = $ConditionInUse;
			$this->ConditionOnlyBreaks	 = $ConditionOnlyBreaks;
			$this->FontBold				 = $FontBold;
			$this->FontItalic			 = $FontItalic;
			$this->FontStrikeOut		 = $FontStrikeOut;
			$this->FontUnderline		 = $FontUnderline;
			$this->Show					 = $Show;
			$this->ShowText				 = $ShowText;
			$this->ConditionLevel		 = $ConditionLevel;
			$this->DataCase				 = $DataCase;
			$this->DataLength			 = $DataLength;
			$this->DataType				 = $DataType;
			$this->DisplayLength		 = $DisplayLength;
			$this->DisplayOrder			 = $DisplayOrder;
			$this->SortOrder			 = $SortOrder;
			$this->TextDisplayLength	 = $TextDisplayLength;
			$this->SequenceNo			 = $SequenceNo;
			$this->ConditionFrom		 = $ConditionFrom;
			$this->ConditionTo			 = $ConditionTo;
		}

		/**
		 * @return string
		 */
		public function getAmountDisplayFormat()
		{
			return $this->AmountDisplayFormat;
		}

		/**
		 * @param string $AmountDisplayFormat
		 * @return FormatProperties
		 */
		public function setAmountDisplayFormat( $AmountDisplayFormat )
		{
			$this->AmountDisplayFormat = $AmountDisplayFormat;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getAttributeId()
		{
			return $this->AttributeId;
		}

		/**
		 * @param string $AttributeId
		 * @return FormatProperties
		 */
		public function setAttributeId( $AttributeId )
		{
			$this->AttributeId = $AttributeId;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getBreakColName()
		{
			return $this->BreakColName;
		}

		/**
		 * @param string $BreakColName
		 * @return FormatProperties
		 */
		public function setBreakColName( $BreakColName )
		{
			$this->BreakColName = $BreakColName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getBreakText()
		{
			return $this->BreakText;
		}

		/**
		 * @param string $BreakText
		 * @return FormatProperties
		 */
		public function setBreakText( $BreakText )
		{
			$this->BreakText = $BreakText;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getColor()
		{
			return $this->Color;
		}

		/**
		 * @param string $Color
		 * @return FormatProperties
		 */
		public function setColor( $Color )
		{
			$this->Color = $Color;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getColumnHeaderText()
		{
			return $this->ColumnHeaderText;
		}

		/**
		 * @param string $ColumnHeaderText
		 * @return FormatProperties
		 */
		public function setColumnHeaderText( $ColumnHeaderText )
		{
			$this->ColumnHeaderText = $ColumnHeaderText;
			return $this;
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
		 * @return FormatProperties
		 */
		public function setColumnName( $ColumnName )
		{
			$this->ColumnName = $ColumnName;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getConditionType()
		{
			return $this->ConditionType;
		}

		/**
		 * @param string $ConditionType
		 * @return FormatProperties
		 */
		public function setConditionType( $ConditionType )
		{
			$this->ConditionType = $ConditionType;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getFontFamily()
		{
			return $this->FontFamily;
		}

		/**
		 * @param string $FontFamily
		 * @return FormatProperties
		 */
		public function setFontFamily( $FontFamily )
		{
			$this->FontFamily = $FontFamily;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getFormula()
		{
			return $this->Formula;
		}

		/**
		 * @param string $Formula
		 * @return FormatProperties
		 */
		public function setFormula( $Formula )
		{
			$this->Formula = $Formula;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getSectionType()
		{
			return $this->SectionType;
		}

		/**
		 * @param string $SectionType
		 * @return FormatProperties
		 */
		public function setSectionType( $SectionType )
		{
			$this->SectionType = $SectionType;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getSectionFormatID()
		{
			return $this->SectionFormatID;
		}

		/**
		 * @param string $SectionFormatID
		 * @return FormatProperties
		 */
		public function setSectionFormatID( $SectionFormatID )
		{
			$this->SectionFormatID = $SectionFormatID;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getType()
		{
			return $this->Type;
		}

		/**
		 * @param string $Type
		 * @return FormatProperties
		 */
		public function setType( $Type )
		{
			$this->Type = $Type;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getBreak()
		{
			return $this->Break;
		}

		/**
		 * @param boolean $Break
		 * @return FormatProperties
		 */
		public function setBreak( $Break )
		{
			$this->Break = $Break;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getConditionInUse()
		{
			return $this->ConditionInUse;
		}

		/**
		 * @param boolean $ConditionInUse
		 * @return FormatProperties
		 */
		public function setConditionInUse( $ConditionInUse )
		{
			$this->ConditionInUse = $ConditionInUse;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getConditionOnlyBreaks()
		{
			return $this->ConditionOnlyBreaks;
		}

		/**
		 * @param boolean $ConditionOnlyBreaks
		 * @return FormatProperties
		 */
		public function setConditionOnlyBreaks( $ConditionOnlyBreaks )
		{
			$this->ConditionOnlyBreaks = $ConditionOnlyBreaks;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getFontBold()
		{
			return $this->FontBold;
		}

		/**
		 * @param boolean $FontBold
		 * @return FormatProperties
		 */
		public function setFontBold( $FontBold )
		{
			$this->FontBold = $FontBold;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getFontItalic()
		{
			return $this->FontItalic;
		}

		/**
		 * @param boolean $FontItalic
		 * @return FormatProperties
		 */
		public function setFontItalic( $FontItalic )
		{
			$this->FontItalic = $FontItalic;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getFontStrikeOut()
		{
			return $this->FontStrikeOut;
		}

		/**
		 * @param boolean $FontStrikeOut
		 * @return FormatProperties
		 */
		public function setFontStrikeOut( $FontStrikeOut )
		{
			$this->FontStrikeOut = $FontStrikeOut;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getFontUnderline()
		{
			return $this->FontUnderline;
		}

		/**
		 * @param boolean $FontUnderline
		 * @return FormatProperties
		 */
		public function setFontUnderline( $FontUnderline )
		{
			$this->FontUnderline = $FontUnderline;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getShow()
		{
			return $this->Show;
		}

		/**
		 * @param boolean $Show
		 * @return FormatProperties
		 */
		public function setShow( $Show )
		{
			$this->Show = $Show;
			return $this;
		}

		/**
		 * @return boolean
		 */
		public function getShowText()
		{
			return $this->ShowText;
		}

		/**
		 * @param boolean $ShowText
		 * @return FormatProperties
		 */
		public function setShowText( $ShowText )
		{
			$this->ShowText = $ShowText;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getConditionLevel()
		{
			return $this->ConditionLevel;
		}

		/**
		 * @param int $ConditionLevel
		 * @return FormatProperties
		 */
		public function setConditionLevel( $ConditionLevel )
		{
			$this->ConditionLevel = $ConditionLevel;
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
		 * @return FormatProperties
		 */
		public function setDataCase( $DataCase )
		{
			$this->DataCase = $DataCase;
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
		 * @return FormatProperties
		 */
		public function setDataLength( $DataLength )
		{
			$this->DataLength = $DataLength;
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
		 * @return FormatProperties
		 */
		public function setDataType( $DataType )
		{
			$this->DataType = $DataType;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getDisplayLength()
		{
			return $this->DisplayLength;
		}

		/**
		 * @param int $DisplayLength
		 * @return FormatProperties
		 */
		public function setDisplayLength( $DisplayLength )
		{
			$this->DisplayLength = $DisplayLength;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getDisplayOrder()
		{
			return $this->DisplayOrder;
		}

		/**
		 * @param int $DisplayOrder
		 * @return FormatProperties
		 */
		public function setDisplayOrder( $DisplayOrder )
		{
			$this->DisplayOrder = $DisplayOrder;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getSortOrder()
		{
			return $this->SortOrder;
		}

		/**
		 * @param int $SortOrder
		 * @return FormatProperties
		 */
		public function setSortOrder( $SortOrder )
		{
			$this->SortOrder = $SortOrder;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getTextDisplayLength()
		{
			return $this->TextDisplayLength;
		}

		/**
		 * @param int $TextDisplayLength
		 * @return FormatProperties
		 */
		public function setTextDisplayLength( $TextDisplayLength )
		{
			$this->TextDisplayLength = $TextDisplayLength;
			return $this;
		}

		/**
		 * @return int
		 */
		public function getSequenceNo()
		{
			return $this->SequenceNo;
		}

		/**
		 * @param int $SequenceNo
		 * @return FormatProperties
		 */
		public function setSequenceNo( $SequenceNo )
		{
			$this->SequenceNo = $SequenceNo;
			return $this;
		}

		/**
		 * @return float
		 */
		public function getConditionFrom()
		{
			return $this->ConditionFrom;
		}

		/**
		 * @param float $ConditionFrom
		 * @return FormatProperties
		 */
		public function setConditionFrom( $ConditionFrom )
		{
			$this->ConditionFrom = $ConditionFrom;
			return $this;
		}

		/**
		 * @return float
		 */
		public function getConditionTo()
		{
			return $this->ConditionTo;
		}

		/**
		 * @param float $ConditionTo
		 * @return FormatProperties
		 */
		public function setConditionTo( $ConditionTo )
		{
			$this->ConditionTo = $ConditionTo;
			return $this;
		}
	}