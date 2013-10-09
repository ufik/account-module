<?php

namespace AdminModule\AccountModule;

/**
 * Description of AccountPresenter
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class SettingsPresenter extends BasePresenter {
	
	protected function startup() {
		parent::startup();
	}

	protected function beforeRender() {
		parent::beforeRender();
			
	}
	
	public function actionDefault($idPage){
	}
	
	public function createComponentSettingsForm(){
		
		$settings = array();
		$settings[] = $this->settings->get('Register email subject', 'accountModule', 'text', array());
		$settings[] = $this->settings->get('Register email', 'accountModule', 'textarea', array());
		
		$settings[] = $this->settings->get('New password subject', 'accountModule', 'text', array());
		$settings[] = $this->settings->get('New password', 'accountModule', 'textarea', array());
		
		return $this->createSettingsForm($settings);
	}
	
	public function renderDefault($idPage){
		$this->reloadContent();
		
		$this->template->config = $this->settings->getSection('accountModule');
		$this->template->idPage = $idPage;
	}
}