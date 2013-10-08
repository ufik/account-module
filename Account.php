<?php

namespace WebCMS\AccountModule;

/**
 * Description of Page
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class Account extends \WebCMS\Module {
	
	protected $name = 'Account';
	
	protected $author = 'Tomáš Voslař';
	
	protected $presenters = array(
		array(
			'name' => 'Account',
			'frontend' => TRUE,
			'parameters' => TRUE
			),
		array(
			'name' => 'Settings',
			'frontend' => FALSE
			)
	);
	
	protected $params = array(
		
	);
	
	public function __construct(){
		$this->addBox('Account', 'Account', 'myAccountBox');
	}
	
}