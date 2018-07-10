<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 19.04.2018
	 * Time: 09:15
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * GwVfs
	 *
	 * @ORM\Table(name="phpgw_config")
	 * @ORM\Entity
	 */
	class GwConfig
	{
		/**
		 * @var string
		 *
		 * @ORM\Column(name="config_app", type="string", length=50, nullable=false)
		 * @ORM\Id
		 */
		protected $config_app;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="config_name", type="string", length=255, nullable=false)
		 * @ORM\Id
		 */
		protected $config_name;
		/**
		 * @var string
		 *
		 * @ORM\Column(name="config_value", type="text",  nullable=true)
		 */
		protected $config_value;

		/**
		 * @return string
		 */
		public function getConfigApp(): string
		{
			return $this->config_app;
		}

		/**
		 * @param string $config_app
		 */
		public function setConfigApp(string $config_app)
		{
			$this->config_app = $config_app;
		}

		/**
		 * @return string
		 */
		public function getConfigName(): string
		{
			return $this->config_name;
		}

		/**
		 * @param string $config_name
		 */
		public function setConfigName(string $config_name)
		{
			$this->config_name = $config_name;
		}

		/**
		 * @return string
		 */
		public function getConfigValue(): string
		{
			return $this->config_value;
		}

		/**
		 * @param string $config_value
		 */
		public function setConfigValue(string $config_value)
		{
			$this->config_value = $config_value;
		}

	}