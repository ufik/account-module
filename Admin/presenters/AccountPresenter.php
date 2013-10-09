<?php

namespace AdminModule\AccountModule;

/**
 * Description of AccountPresenter
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class AccountPresenter extends BasePresenter {
	
	private $account;
	
	protected function startup() {
		parent::startup();
	
	}

	protected function beforeRender() {
		parent::beforeRender();
		
	}
	
	public function actionDefault($idPage){
	}
	
	public function renderDefault($idPage){
		$this->reloadContent();
		
		$this->template->idPage = $idPage;
	}
	
	protected function createComponentAccountsGrid($name){
				
		$grid = $this->createGrid($this, $name, '\WebCMS\AccountModule\Doctrine\Account');
						
		$grid->addColumn('firstname', 'Firstname')->setSortable()->setFilter();
		$grid->addColumn('lastname', 'Lastname')->setSortable()->setFilter();
		$grid->addColumn('email', 'Email')->setSortable()->setFilter();
		$grid->addColumn('street', 'Street')->setSortable()->setFilter();
		$grid->addColumn('city', 'City')->setSortable()->setFilter();
		$grid->addColumn('postcode', 'Postcode')->setSortable()->setFilterNumber();
						
		$grid->addAction("editAccount", 'Edit', \Grido\Components\Actions\Action::TYPE_HREF, 'editAccount', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => 'btn btn-primary ajax'));
		$grid->addAction("deleteAccount", 'Delete', \Grido\Components\Actions\Action::TYPE_HREF, 'deleteAccount', array('idPage' => $this->actualPage->getId()))->getElementPrototype()->addAttributes(array('class' => 'btn btn-primary btn-danger'));
		
		return $grid;
	}
	
	public function actionDeleteAccount($id, $idPage){
		$order = $this->repository->find($id);
		$this->em->remove($order);
		$this->em->flush();
		
		$this->flashMessage($this->translation['Account has been deleted.'], 'success');
		if(!$this->isAjax())
			$this->redirect('Account:default', array(
				'idPage' => $idPage
			));
	}
	
	public function actionEditAccount($id, $idPage){
		$this->reloadContent();
		
		$this->account = $this->repository->find($id);
	}
	
	public function renderEditAccount($idPage){
		$this->template->account = $this->account;
		$this->template->idPage = $idPage;
	}
	
	public function createComponentAccountForm($name){
		$form = $this->createForm();
				
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
		
		if($values->generatePassword){
			
			$password = \Nette\Utils\Strings::random(10);
			
			$hash = $this->getContext()->authenticator->calculateHash($password);
			$this->account->setPassword($hash);
			
			$this->sendNewPasswordEmail($this->account->getEmail(), $password);
			$this->flashMessage($this->translation['New password has been generated. Info email has been sent.'], 'success');
		}
		
		$this->em->flush();
		
		$this->flashMessage($this->translation['Account has been saved.'], 'success');
		$this->redirect('Account:editAccount', array(
				'idPage' => $this->actualPage->getId(),
				'id' => $this->getParam('id')
			));
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