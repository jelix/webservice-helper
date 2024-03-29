Webservice helper
=================

This is a fork of Webservice helper from David Kingma.
Original sources come from www.jool.nl/webservicehelper/.
Original author : David Kingma david<AT>jool<DOT>nl

Maintainer of this fork: Laurent Jouanneau
web site: https://github.com/jelix/webservice-helper

What does the webservice helper?
---------------------------------

The webservice helper does what the name says: helping you making a php class 
available as webservice. It generates the documentation, the webservice
description file (WSDL) and handles errorhandling. It consists of three parts: 
* extension of the PHP reflection classes to also parse the comments for
  information on parameter info and return values. The documentation and WSDL
  are generated from these classes.(see also documentation.php as an example)
* extension to the PHP SOAP implementation. It catches all normal exceptions
  and allows typehints in the webservice methods. (ie. `saveContact(contact $contact)`)


Installation
------------

Use Composer to install the library. Package name: `jelix/webservice-helper`.

If you don't want to use Composer, you can copy the content of the source code
(all needed classes are into `lib/`), and you must create an autoloader (like in
`example/common.php` or you must include all classes.

Manual
------

So how do you create your own webservice. As an example we create a webservice to
add and show contacts. First you create a class called contactManager in the 
`/example/data_objects` with the public functions `getContacts()`, 
`saveContact(contact $contact)` and `newContact()`. To let the Webservice helper
know what the parameters and return values of each method are we put a comment 
in front of each method specifying the parameters and return types. For example:

```php
/**
 * This method saves the given contact
 * @return contact[] Array with all the contacts
 */
 public function getContacts(){}

/**
 * This method saves the given contact
 * @param contact The contact to save
 * @return void
 */
 public function saveContact(contact $contact){}

/**
 * This method saves the given contact
 * @return contact A new contact template
 */
 public function newContact(){}

/**
 * Gets the current contact list as associative array
 * @return contact[=>]  keys are contact name
 */
public function	getContactsAsAssoc() {}
```

We used the contact type as a return value for `newContact()` and `getContacts()` so we 
need to define what a contact looks like. For that we create a class called contact:

```php
class contact{
	/** @var string */
	public $name;
	/** @var string */
	public $address;
}
```

Since string is (just as boolean and int) a known datatype we don't need to specify it
any further.

The last thing we need to do to finish our webservice is to tell the webservice that de 
`contactManager` class is an allowed webservice and that `contact` is an allowed data-
structure (for documentation purpose and classmap). In the `config.php` you add
`"contactmanager"` to the `WSClasses` array and add `"contact"` to the `WSStructures` array. 

You can now view the service documentation at `/doc/documentation.php` and the wsdl at 
`/example/service.php?class=contactManager&wsdl`

Note about associative array containing objects: if the soap server defines a classmap
(`'classmap'` option to `SoapServer` in PHP), objects are wrapped into `SoapVal` objects, else
they are simple `stdClass` objects.


FAQ
----

* My function doesn't showup in the documentation nor the WSDL file?
Please check if it's a public function and it doesn't start with `__`

* It doesn't work!
    - Do you see any warnings in the generated documentation? Fix them
    - Check case sensitivity of class names
    - Did you check the javascript console to see if anything goes wrong?
    - Tried cleaning the wsdl cache in the WSDL cache directory?
    - Did you check the WSDL url in the client?

* Can I use the webservice helper in my own project?
Yes you can use it under the terms of LGPL 2.1

* Can I contribute?

Yes, open an issue and/or a PR on https://github.com/jelix/webservice-helper


Example and unit tests
----------------------

See tests/README.md