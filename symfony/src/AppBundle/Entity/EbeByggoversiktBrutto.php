<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 23.02.2018
 * Time: 14:59
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EbeByggoversiktBrutto
 *
 * @ORM\Table(name="ebe_byggoversikt_brutto")
 * @ORM\Entity(readOnly=true)
 */
class EbeByggoversiktBrutto
{
	/**
	 * @var string
	 *
	 * @ORM\Column(name="loc1", type="string", length=8)
	 * @ORM\Id
	 */
	protected $loc1;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="loc2", type="string", length=4)
	 * @ORM\Id
	 */
	protected $loc2;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="location_code", type="string", length=13)
	 * @ORM\Id
	 */
	protected $location_code;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="eiendomsnavn_adresse", type="string", length=50)
	 * @ORM\Id
	 */
	protected $eiendomsnavn_adresse;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="gatenavn", type="text")
	 */
	protected $gatenavn;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="street_number", type="string", length=5)
	 */
	protected $street_number;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="gab_id", type="string", length=20)
	 */
	protected $gab_id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="bygning", type="string", length=200)
	 */
	protected $bygning;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="byggeaar", type="integer", nullable=true)
	 */
	protected $byggeaar;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="bydel", type="string", length=20)
	 */
	protected $bydel;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="area_gross", type="decimal", precision=2)
	 */
	protected $area_gross;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="ebe_byggkategori_id", type="integer")
	 */
	protected $ebe_byggkategori_id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ebe_byggkategori", type="string", length=100)
	 */
	protected $ebe_byggkategori;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="holtekategori", type="integer", nullable=true)
	 */
	protected $holtekategori;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="merknader", type="text")
	 */
	protected $merknader;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="merknader_1", type="text")
	 */
	protected $merknader_1;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="holtekategori_navn", type="text")
	 */
	protected $holtekategori_navn;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="eierform", type="text")
	 */
	protected $eierform;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="areal_sist_endret", type="integer", nullable=true)
	 */
	protected $areal_sist_endret;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="areal_netto_endring", type="integer", nullable=true)
	 */
	protected $areal_netto_endring;

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
	 * @return php
	 **/
	public function __set($property, $value)
	{
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}

		return $this;
	}

}