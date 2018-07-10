<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 01.03.2018
	 * Time: 14:20
	 */

	namespace AppBundle\XmlModels;

	class HmCustomerXMLModel
	{
		/**
		 * -1 = Bergen Kommune
		 * @var string
		 */
		protected $CustomerNo = -1;
		/**
		 * @var string
		 */
		protected $CustomerName;
		/**
		 * @var string
		 */
		protected $Address;
		/**
		 * @var string
		 */
		protected $Telephone1;
		/**
		 * @var string
		 */
		protected $Telephone2;
		/**
		 * @var string
		 */
		protected $Comment;
		/**
		 * @var string
		 */
		protected $Email;
		/**
		 * @var integer
		 */
		protected $SendToAll;
		/**
		 * @var string
		 */
		protected $VATNumber;
		/**
		 *  0= no, 1 = yes
		 * @var integer
		 */
		protected $Company = 1;
		/**
		 * @var string
		 */
		protected $ContactName;

		/**
		 * HmCustomerXMLModel constructor.
		 */
		public function __construct()
		{
			//
		}

		/**
		 * @return string
		 */
		public function getCustomerNo(): string
		{
			return $this->CustomerNo;
		}
	}