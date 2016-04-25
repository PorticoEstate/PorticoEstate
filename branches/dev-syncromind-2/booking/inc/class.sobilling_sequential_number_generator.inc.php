<?php
	phpgw::import_class('booking.socommon');

	class booking_sobilling_sequential_number_generator extends booking_socommon
	{

		public static $generators = array();

		function __construct()
		{
			$this->season_so = CreateObject('booking.soseason');
			$this->resource_so = CreateObject('booking.soresource');

			parent::__construct('bb_billing_sequential_number_generator', array(
				'id' => array('type' => 'int'),
				'value' => array('type' => 'int', 'required' => true),
				)
			);
		}

		public function get_current_transaction_id()
		{
			$this->get_db()->query("SELECT txid_current() AS trans_id");
			if ($this->get_db()->next_record())
			{
				return intval($this->get_db()->f('trans_id', false));
			}
			else
			{
				throw new UnexpectedValueException("Unable to retrieve id of current transaction");
			}
		}

		public function get_generator_instance( $name )
		{
			if (!$name || empty($name))
			{
				return null;
			}

			$generator = null;

			if (isset(self::$generators[$name]))
			{
				$generator = self::$generators[$name];
				try
				{
					$generator->get_current();
				}
				catch (LogicException $e)
				{

				}

				if (!$generator->used())
				{
					if (!$generator->locked() || $generator->locked_by_transaction_id() == $this->get_current_transaction_id())
					{
						return $generator;
					}
				}

				$generator->set_used();
				unset(self::$generators[$name]);
				unset($generator);
			}

			$this->db->query(sprintf("SELECT id FROM %s WHERE name = '%s' LIMIT 1 OFFSET 0", $this->get_table_name(), $name), __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				$id = intval($this->db->f('id', false));
				self::$generators[$name] = $generator = new booking_sobilling_sequential_number_generator_instance($this, $name, $id);
			}

			return $generator;
		}
	}

	class booking_sobilling_sequential_number_generator_instance
	{

		protected
			$table,
			$name,
			$id,
			$value,
			$locked = false,
			$used = false,
			$locked_by_transaction_id;

		function __construct( $table, $name, $id )
		{
			$this->table = $table;
			$this->name = $name;
			$this->id = $id;
		}

		protected function get_db()
		{
			return $this->table->get_db();
		}

		protected function get_table_name()
		{
			return $this->table->get_table_name();
		}

		public function get_current_transaction_id()
		{
			$this->table->get_current_transaction_id();
		}

		function increment()
		{
			if ($this->used)
			{
				throw new LogicException("Cannot be reused over multiple transactions");
			}

			if ($this->get_db()->get_transaction() === false)
			{
				throw new LogicException("Must only be called within a transaction");
			}

			$this->get_db()->query(sprintf("UPDATE %s SET value=value+1 WHERE id=%s", $this->get_table_name(), $this->id));
			$was_locked = $this->locked;
			$this->locked = true;

			if (!$was_locked)
			{
				$this->locked_by_transaction_id = $this->get_current_transaction_id();
			}

			$old_value = $this->get_current();

			$this->get_db()->query(sprintf("SELECT value FROM %s WHERE id=%s", $this->get_table_name(), $this->id));

			if ($this->get_db()->next_record())
			{
				$new_value = $this->table->marshal_field_value('value', $this->get_db()->f('value', false));
				if ($was_locked == true && $old_value != null && $old_value + 1 != $new_value)
				{
					$this->set_used();
					throw new UnexpectedValueException("Unexpected value, increment was probably called after a transaction rolled back");
				}

				$this->value = $new_value;
				return $this;
			}
			else
			{
				throw new UnexpectedValueException();
			}

			return false;
		}

		function locked_by_transaction_id()
		{
			return $this->locked_by_transaction_id;
		}

		function locked()
		{
			return $this->locked;
		}

		function get_current()
		{
			if ($this->used || ($this->get_db()->get_transaction() === false && $this->locked))
			{
				$this->set_used();
				throw new LogicException("Cannot be reused over multiple transactions");
			}

			if (!$this->locked)
			{
				throw new LogicException("Access must be locked by a transaction first (call increment first)");
			}

			return $this->value;
		}

		function used()
		{
			return $this->used;
		}

		function set_used()
		{
			$this->value = null;
			$this->locked = false;
			$this->used = true;
		}
	}