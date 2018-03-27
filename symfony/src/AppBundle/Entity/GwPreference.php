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
	 * @return GwPreference
	 **/
	public function __set($property, $value)
	{
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}

		return $this;
	}
}