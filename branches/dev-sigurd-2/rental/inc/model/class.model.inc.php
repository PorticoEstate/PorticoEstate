<?php

	abstract class rental_model
	{
		/**
		* Store the object in the database.  If the object has no ID it is assumed to be new and
		* inserted for the first time.  The object is then updated with the new insert id.
		*/
		public function store()
		{
			$so = $this->get_so();
			
			if ($this->id) {
				// We can assume this composite came from the database since it has an ID. Update the existing row
				$so->update($this);
			} 
			else
			{
				// This object does not have an ID, so will be saved as a new DB row
				$so->add($this);
			}
		}
		
		public abstract function serialize();
	}
?>