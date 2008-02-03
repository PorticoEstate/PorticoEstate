<?php

	class guestbook_BO
	{
		var $so;

		function guestbook_BO()
		{
			$this->so = CreateObject('sitemgr_module_guestbook.guestbook_SO', true);
		}

		function create_book($name)
		{
			return $this->so->create_book($name);
		}

		function add_entry($name,$comment,$book_id)
		{
print_r($name);
			$this->so->add_entry($name,$comment,$book_id);
		}

		function get_entries($book_id)
		{
			return $this->so->get_entries($book_id);
		}

		function get_books()
		{
			return $this->so->get_books();
		}

		function delete_book($book_id)
		{
			return $this->so->delete_book($book_id);
		}

		function save_book($book_id,$name)
		{
			return $this->so->save_book($book_id,$name);
		}
	}