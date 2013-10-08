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
		//$settings[] = $this->settings->get('Show map', 'accountModule', 'checkbox', array());
		
		
		return $this->createSettingsForm($settings);
	}
	
	public function renderDefault($idPage){
		$this->reloadContent();
		
		$this->template->config = $this->settings->getSection('accountModule');
		$this->template->idPage = $idPage;
	}
	
	
}