<?php

namespace FrontendModule\AccountModule;

/**
 * Description of PagePresenter
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class AccountPresenter extends \FrontendModule\BasePresenter{
	
	private $repository;
	
	private $sessionData;
	
	private $user;
	
	private $account;
	
	protected function startup(){
		parent::startup();

		$this->getAccountState($this->session);
		
		$this->repository = $this->em->getRepository('WebCMS\AccountModule\Doctrine\Account');
	}
	
	private function getAccountState($session){
		$this->sessionData = $session->getSection('accountModule');
		
		if(!$this->sessionData->offsetExists('user')){
			$this->user = new \WebCMS\AccountModule\Doctrine\Account;
			$this->saveAccountState();
		}else{
			$this->user = $this->sessionData->user;
		}
	}
	
	private function saveAccountState(){
		$this->sessionData->user = $this->user;
	}
	
	protected function beforeRender() {
		parent::beforeRender();
		
	}
	
	public function actionDefault($id){
		
	}
	
	public function renderDefault($id){
		
		$this->template->user = $this->user;
		$this->template->id = $id;
	}
	
	/* LOGIN */
	
	public function createComponentLoginForm($name){
		$form = $this->createForm('loginForm-submit');
		
		$form->addText('email', 'Your email')->setRequired('Please fill in your email address.')->setAttribute('placeholder', 'Fill in email address');
		$form->addPassword('password', 'Password')->setRequired('Please fill in password.')->setAttribute('placeholder', 'Fill in password');
		
		$form->addSubmit('login', 'Login');
		$form->onSuccess[] = callback($this, 'loginFormSubmitted');
		
		return $form;
	}
	
	public function loginFormSubmitted($form){
		$values = $form->getValues();
		
		$user = $this->repository->findOneBy(array(
			'email' => $values->email, 
			'password' => $this->getContext()->authenticator->calculateHash($values->password)
				)
			);
		
		if(is_object($user)){

			$this->user = $user;
			$this->saveAccountState();
			
			$this->flashMessage('Logging in was successful.', 'success');
			$this->selfRedirect();
		}else{
			
			$this->flashMessage('Bad login data given.', 'danger');
			$this->selfRedirect();
		}
	}
	
	public function myAccountBox($context, $fromPage){
		
		$this->getAccountState($context->session);
		
		$template = $context->createTemplate();
		$template->setFile('../app/templates/account-module/boxes/myAccount.latte');
		$template->link = $link = $context->link('default', array(
																'path' => $fromPage->getPath(),
																'abbr' => $context->abbr
															));
		$template->linkLogout = $link = $context->link('logout', array(
																'path' => $fromPage->getPath(),
																'abbr' => $context->abbr
															));
		$template->user = $this->user;
		
		return $template;
	}
	
	public function actionLogout(){
		$this->user = new \WebCMS\AccountModule\Doctrine\Account;
		$this->saveAccountState();
		
		$this->flashMessage('User has been logged out.', 'success');
		$this->redirect('default', array(
				'path' => $this->actualPage->getPath(),
				'abbr' => $this->abbr
			));
	}
	
	/* REGISTER */
	
	public function actionRegister(){
				
		$this->addToBreadcrumbs($this->actualPage->getId(),
				'Account',
				'Account',
				$this->translation['Registrace'],
				$this->actualPage->getPath() . \Nette\Utils\Strings::webalize($this->translation['Registrace'])
			);
	}
	
	public function renderRegister($id){
		
		$this->template->id = $id;
	}
	
	public function createComponentRegisterForm($name){
		$form = $this->createForm('registerForm-submit');
		
		$form->addText('email', 'Email address (login)')
				->setRequired('Please fill in your email address.')
				->addRule(\Nette\Forms\Form::EMAIL, 'This is not correct email address.');
		
		$form->addText('firstname', 'Firstname')
				->setRequired('Please fill in your firstname.');
		
		$form->addText('lastname', 'Lastname')
				->setRequired('Please fill in your lastname.');
		
		$form->addPassword('password', 'Password')
				->setRequired('Please fill in password.');
		
		$form->addPassword('confirmPassword', 'Potvrzení hesla:', 30)
            ->addRule(\Nette\Forms\Form::FILLED, 'Please confirm your password.')
            ->addRule(\Nette\Forms\Form::EQUAL, 'Both passwords have to be equal.', $form['password']);
		
		$form->addProtection();
		$form->addSubmit('register', 'Register new account');
		$form->onSuccess[] = callback($this, 'registerFormSubmitted');
		 
		return $form;
	}
	
	public function registerFormSubmitted($form){
		$values = $form->getValues();
		
		$exists = $this->repository->findOneByEmail($values->email);

		if(!is_object($exists)){
			
			$account = new \WebCMS\AccountModule\Doctrine\Account;
			$account->setEmail($values->email);
			$account->setFirstname($values->firstname);
			$account->setLastname($values->lastname);
			$account->setPassword($this->getContext()->authenticator->calculateHash($values->password));
			
			$this->em->persist($account);
			$this->em->flush();
			
			$registered = TRUE;
		}else{
			
			$registered = FALSE;
		}

		if($registered){
			$this->sendRegisterEmail($values->email, $values->password);
			
			$this->flashMessage('User has been registered.', 'success');
			$this->redirect('default', array(
				'path' => $this->actualPage->getPath(),
				'abbr' => $this->abbr
			));
		}
		else{
			$this->flashMessage('User with this email already exists.', 'danger');
			$this->selfRedirect();
		}
	}

	public function sendRegisterEmail($email, $password){
		
		$text = \WebCMS\SystemHelper::replaceStatic($this->settings->get('Register email', 'accountModule', 'textarea')->getValue(),
				array(
					'[LOGIN_URL]',
					'[LOGIN]',
					'[PASSWORD]'
				),
				array(
					$this->link('//default', array(
						'path' => $this->actualPage->getPath(),
						'abbr' => $this->abbr
					)),
					$email,
					$password
				)
				);
		
		$mail = new \Nette\Mail\Message;
		$mail->addTo($email);
		$mail->setFrom($this->settings->get('Info email', \WebCMS\Settings::SECTION_BASIC)->getValue());
		$mail->setHtmlBody($text);
		$mail->setSubject(\WebCMS\SystemHelper::replaceStatic($this->settings->get('Register email subject', 'accountModule', 'text')->getValue()));
		$mail->send();
		
	}
	
	/* VIEW ORDERS */
	
	public function actionViewOrders(){
		$this->addToBreadcrumbs($this->actualPage->getId(),
				'Account',
				'Account',
				$this->translation['View orders'],
				$this->actualPage->getPath() . \Nette\Utils\Strings::webalize($this->translation['View orders'])
			);
	}
	
	public function renderViewOrders($id){
		
		$user = $this->repository->find($this->user->getId());
		
		$this->template->user = $user;
		$this->template->id = $id;
	}
	
	public function actionOrderDetail(){
		$this->addToBreadcrumbs($this->actualPage->getId(),
				'Account',
				'Account',
				$this->translation['View orders'],
				$this->actualPage->getPath() . '?action=viewOrders'
			);
		
		$this->addToBreadcrumbs($this->actualPage->getId(),
				'Account',
				'Account',
				$this->translation['Order detail'],
				$this->actualPage->getPath() . \Nette\Utils\Strings::webalize($this->translation['Order detail'])
			);
	}
	
	public function renderOrderDetail($orderId, $id){
		$order = $this->em->getRepository('\WebCMS\EshopModule\Doctrine\Order')->find($orderId);
		
		$this->template->order = $order;
		$this->template->id = $id;
	}
	
	/* SETTINGS */
	
	public function actionSettings(){
		$this->addToBreadcrumbs($this->actualPage->getId(),
				'Account',
				'Account',
				$this->translation['My account settings'],
				$this->actualPage->getPath() . \Nette\Utils\Strings::webalize($this->translation['My account settings'])
			);
		
		$this->account = $this->repository->find($this->user->getId());
	}
	
	public function renderSettings($id){
		
		$this->template->id = $id;
	}
	
	public function createComponentAccountForm($name){
		$form = $this->createForm('accountForm-submit', 'settings');
				
		$form->addText('firstname', 'Firstname');
		$form->addText('lastname', 'Lastname');
		$form->addText('email', 'Email')->setDisabled();
		$form->addText('phone', 'Phone');
		$form->addText('street', 'Street');
		$form->addText('city', 'City');
		$form->addText('postcode', 'Postcode');
		
		$form->addText('invoiceCompany', 'Company name');
		$form->addText('invoiceNo', 'No.');
		$form->addText('invoiceVatNo', 'Vat No.');
		$form->addText('invoiceStreet', 'Street');
		$form->addText('invoiceCity', 'City');
		$form->addText('invoicePostcode', 'Postcode');
		
		$form->addPassword('password', 'Password');
		
		$form->addPassword('confirmPassword', 'Potvrzení hesla:', 30)
            ->addRule(\Nette\Forms\Form::EQUAL, 'Both passwords have to be equal.', $form['password']);
		
		$form->addCheckbox('generatePassword', 'Generate new password?');
		
		$form->addSubmit('send', 'Save');
		$form->onSuccess[] = callback($this, 'accountFormSubmitted');
		
		if($this->account){
			$form->setDefaults($this->account->toArray());
		}
		
		return $form;
	}
	
	public function accountFormSubmitted($form){
		
		$values = $form->getValues();
		
		$this->account->setFirstname($values->firstname);
		$this->account->setLastname($values->lastname);
		//$this->account->setEmail($values->email);
		$this->account->setPhone($values->phone);
		$this->account->setStreet($values->street);
		$this->account->setCity($values->city);
		$this->account->setPostcode($values->postcode);
		
		$this->account->setInvoiceCompany($values->invoiceCompany);
		$this->account->setInvoiceNo($values->invoiceNo);
		$this->account->setInvoiceVatNo($values->invoiceVatNo);
		$this->account->setInvoiceStreet($values->invoiceStreet);
		$this->account->setInvoiceCity($values->invoiceCity);
		$this->account->setInvoicePostcode($values->invoicePostcode);
		
		if(!empty($values->password)){
			$this->flashMessage('Password has been changed.', 'success');
			
			$hash = $this->getContext()->authenticator->calculateHash($values->password);
			$this->account->setPassword($hash);
		}
		
		$this->em->flush();
		
		$this->user = $this->account;
		$this->saveAccountState();
		
		$this->flashMessage('Account data has been saved.', 'success');
		$this->selfRedirect();
	}
	
	private function sendNewPasswordEmail($email, $password){
		
		$text = \WebCMS\SystemHelper::replaceStatic($this->settings->get('New password', 'accountModule', 'textarea')->getValue(),
				array(
					'[LOGIN]',
					'[PASSWORD]'
				),
				array(
					$email,
					$password
				)
				);
		
		$mail = new \Nette\Mail\Message;
		$mail->addTo($email);
		$mail->setFrom($this->settings->get('Info email', \WebCMS\Settings::SECTION_BASIC)->getValue());
		$mail->setHtmlBody($text);
		$mail->setSubject(\WebCMS\SystemHelper::replaceStatic($this->settings->get('New password subject', 'accountModule', 'text')->getValue()));
		$mail->send();
	}
}