<?php

/**
  * Keeps track of the people in our contact list.
  *
  * Starts with a standard contact list and can add
  * new people to our list or change existing contacts.
  * This class is for example purposes only, just to
  * show how to create a webservice
  */
class contactManager{

	protected $contacts = array();

	public function __construct() {
		$contact = new contact();
		$contact->address = new address();
		$contact->address->city ="sesamcity";
		$contact->address->street ="sesamstreet";
		$contact->email = "me@you.com";
		$contact->id = 1;
		$contact->name ="me";
		$this->contacts[] = $contact;
		$contact = new contact();
		$contact->address = new address();
		$contact->address->city ="toyscity";
		$contact->address->street ="toysstreet";
		$contact->email = "zorg@example.com";
		$contact->id = 2;
		$contact->name ="zorg";
		$this->contacts[] = $contact;
	}

	/**
	 * Gets the current contact list.
	 * @return contact[]
	 */
	public function	getContacts() {
		return $this->contacts;
	}
	
	/**
	  * Gets the contact with the given id.
	  * @param int The id
	  * @return contact
	  */
	public function	getContact($id) {
		//get contact from db
		foreach($this->contacts as $contact) {
			if ($contact->id == $id)
				return $contact;
		}
		//might wanna throw an exception when it does not exists
		throw new Exception("Contact '$id' not found");
	}

	/**
	 * add a list of contact
	 * @param contact[] list of contact
	 * @return boolean
	 */
	function addContacts($contacts) {
		$this->contacts = array_merge($this->contacts, $contacts);
		return true;
	}

	/**
	  * Generates an new, empty contact template
	  * @return contact
	  */
	public function newContact() {
		return new contact();
	}
	
	/**
	  * Saves a given contact
	  * @param contact
	  * @return void
	  */
	public function saveContact(contact $contact) {
		$contact->save();
		foreach($this->contacts as $k=>$contactdb) {
			if ($contactdb->id == $contact->id) {
				$this->contacts[$k] = $contact;
				return;
			}
		}
		$this->contacts[] = $contact;
	}
}

