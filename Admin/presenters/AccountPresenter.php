<?php

namespace AdminModule\AccountModule;

/**
 * Description of AccountPresenter
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class AccountPresenter extends BasePresenter {
	
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
}