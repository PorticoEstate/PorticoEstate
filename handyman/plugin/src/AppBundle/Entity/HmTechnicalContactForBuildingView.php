<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 27.03.2018
	 * Time: 09:54
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmTtsTicket
	 *
	 * @ORM\Table(name="hm_technical_contact_for_building")
	 * @ORM\Entity(repositoryClass="AppBundle\Repository\HmTechnicalContactForBuildingRepository")
	 */
	class HmTechnicalContactForBuildingView
	{
		/**
		 * @var string
		 *
		 * @ORM\Column(name="location_code", type="string", length=8)
		 * @ORM\Id
		 */
		protected $location_code;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="loc1_name", type="string", length=50)
		 */
		protected $loc1_name;
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="contact_id", type="integer")
		 */
		protected $contact_id;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="first_name", type="string", length=50)
		 */
		protected $first_name;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="last_name", type="string", length=50)
		 */
		protected $last_name;

		/**
		 * @ORM\ManyToOne(targetEntity = "GwAccount")
		 * @ORM\JoinColumn(name = "contact_id", referencedColumnName = "person_id")
		 */
		protected $account;

		/**
		 * @param $property string
		 * @return mixed
		 **/
		public function __get($property)
		{
			if (property_exists($this, $property)) {
				return $this->$property;
			}
		}

		/**
		 * @param $property string
		 * @param $value mixed
		 * @return HmTechnicalContactForBuildingView
		 **/
		public function __set($property, $value)
		{
			if (property_exists($this, $property)) {
				$this->$property = $value;
			}

			return $this;
		}

		/**
		 * @return int
		 */
		public function getContactId(): int
		{
			return $this->contact_id;
		}
	}