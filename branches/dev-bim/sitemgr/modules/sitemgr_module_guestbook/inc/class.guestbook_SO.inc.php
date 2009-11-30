<?php

	class guestbook_SO
	{
		var $db;

		function guestbook_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
		}


		function create_book($title)
		{
			$this->db->query("INSERT INTO phpgw_sitemgr_module_guestbook_books (book_title) VALUES('$title')");
			return $this->db->get_last_insert_id('phpgw_sitemgr_module_guestbook_books','book_id');
		}


		function add_entry($name,$comment,$book_id)
		{
			$timestamp = time();
			$this->db->query("INSERT INTO phpgw_sitemgr_module_guestbook_entries (name,book_id,comment,timestamp) VALUES ('$name',$book_id,'$comment',$timestamp)");
		}

		function get_entries($book_id)
		{
			$this->db->query("SELECT name,comment,timestamp FROM phpgw_sitemgr_module_guestbook_entries WHERE book_id = $book_id ORDER BY timestamp DESC");
			while($this->db->next_record())
			{
				foreach(array('name','comment','timestamp') as $field)
				{
					$entry[$field] = $this->db->f($field);
				}
				$result[] = $entry;
			}
			return $result;
		}

		function get_books()
		{
			$this->db->query("SELECT * FROM phpgw_sitemgr_module_guestbook_books");
			while($this->db->next_record())
			{
				$result[$this->db->f('book_id')] = $this->db->f('book_title');
			}
			return $result;
		}

		function delete_book($book_id)
		{
			$this->db->query("DELETE FROM phpgw_sitemgr_module_guestbook_entries WHERE book_id = $book_id");
			$this->db->query("DELETE FROM phpgw_sitemgr_module_guestbook_books WHERE book_id = $book_id");
		}
		function save_book($book_id,$title)
		{
			$this->db->query("UPDATE phpgw_sitemgr_module_guestbook_books SET book_title='$title' WHERE book_id = $book_id");
		}
	}