<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 23.03.2018
	 * Time: 14:51
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * FmTtsTicket
	 *
	 * @ORM\Table(name="phpgw_preferences")
	 * @ORM\Entity(repositoryClass="AppBundle\Repository\GwPreferenceRepository")
	 */
	class GwPreference
	{
		/**
		 * @var integer
		 *
		 * @ORM\Column(name="preference_owner", type="integer")
		 * @ORM\Id
		 */
		protected $preference_owner;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="preference_app", type="string", length=25)
		 * @ORM\Id
		 */
		protected $preference_app;

		/**
		 * @var array
		 *
		 * @ORM\Column(name="preference_value", type="object")
		 */
		protected $preference_value;


		/**
		 * @var string
		 */
		protected $resource_number;

		/**
		 * @ORM\ManyToOne(targetEntity = "GwAccount")
		 * @ORM\JoinColumn(name = "preference_owner", referencedColumnName = "account_id")
		 */
		protected $account;

		/**
		 * @return int
		 */
		public function getPreferenceOwner(): int
		{
			return $this->preference_owner;
		}

		/**
		 * @param int $preference_owner
		 */
		public function setPreferenceOwner(int $preference_owner)
		{
			$this->preference_owner = $preference_owner;
		}

		/**
		 * @return string
		 */
		public function getPreferenceApp(): string
		{
			return $this->preference_app;
		}

		/**
		 * @param string $preference_app
		 */
		public function setPreferenceApp(string $preference_app)
		{
			$this->preference_app = $preference_app;
		}

		/**
		 * @return array
		 */
		public function getPreferenceValue(): array
		{
			return $this->preference_value;
		}

		/**
		 * @param array $preference_value
		 */
		public function setPreferenceValue(array $preference_value)
		{
			$this->preference_value = $preference_value;
		}

		/**
		 * @return string
		 */
		public function getResourceNumber(): string
		{
			return $this->resource_number;
		}

		/**
		 * @param string $resource_number
		 */
		public function setResourceNumber(string $resource_number)
		{
			$this->resource_number = $resource_number;
		}

		/**
		 * @return GwAccount
		 */
		public function getAccount(): GwAccount
		{
			return $this->account;
		}
	}