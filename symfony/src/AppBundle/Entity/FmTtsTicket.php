<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 06.03.2018
 * Time: 11:35
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FmTtsTicket
 *
 * @ORM\Table(name="fm_tts_tickets")
 * @ORM\Entity
 */
class FmTtsTicket
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="SEQUENCE")
	 * @ORM\SequenceGenerator(sequenceName="seq_fm_tts_tickets", allocationSize=1, initialValue=1)
	 */
	protected $id;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="group_id", type="integer")
	 */
	protected $group_id;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="priority", type="integer")
	 */
	protected $priority;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="user_id", type="integer")
	 */
	protected $user_id;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="assignedto", type="integer")
	 */
	protected $assignedto;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="subject", type="string", length=255)
	 */
	protected $subject;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="cat_id", type="integer")
	 */
	protected $cat_id;
//protected $billable_hours;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="status", type="string", length=3)
	 */
	protected $status;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="details", type="text")
	 */
	protected $details = '';
	/**
	 * @var string
	 *
	 * @ORM\Column(name="location_code", type="string", length=28)
	 */
	protected $location_code;
//protected $p_num;
//protected $p_entry_id;
//protected $p_cat_id;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="loc1", type="string", length=8)
	 */
	protected $loc1;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="loc2", type="string", length=4)
	 */
	protected $loc2;
//protected $loc3;
//protected $loc4;
//protected $floor;
//	/**
//	 * @var string
//	 *
//	 * @ORM\Column(name="address", type="string", length=255)
//	 */
//	protected $address;
//protected $contact_phone;
//protected $tenanant_id;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="entry_date", type="integer")
	 */
	protected $entry_date;
//protected $finnish_date;
//protected $finnish_date2;
//protected $loc5;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="contact_id", type="integer")
	 */
	protected $contact_id;
//protected $order_id;
//protected $vendor_id;
//protected $order_descr;
//protected $b_account_id;
//protected $ecodimb;
//protected $budget;
//protected $actual_cost;
//protected $contact_email;
//protected $order_cat_id;
//protected $building_part;
//protected $order_dim1;
//protected $publish_note;
//protected $branch_id;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="modified_date", type="integer")
	 */
	protected $modified_date;
//protected $external_project_id;
//protected $actual_cost_year;
//protected $contract_id;
//protected $service_id;
//protected $tax_code;
//protected $unspsc_code;
//protected $order_sent;
//protected $order_recieved;
//protected $order_recived_amount;
//protected $order_by;
//protected $mail_recipients;
//protected $file_attachments;
//protected $delivery_address;
//protected $continuous;
//protected $order_deadeline;
//protected $invoice_remark;

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
	 * @return FmTtsTicket
	 **/
	public function __set($property, $value)
	{
		if (property_exists($this, $property)) {
			$this->$property = $value;
			$this->modified_date = time();
		}

		return $this;
	}

	public function set_default_values(){
		// 14 = Teknisk drift
		$this->group_id = 14;
		// 10 Bygg Teknisk
		$this->cat_id = 10;
		// status = 0: ny melding, 4: Hos teknisk person på bygget
		$this->status = 0;
		$this->priority = 2; // 2 = Medium
		$this->entry_date  = time();
	}
}
