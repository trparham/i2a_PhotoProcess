<?php

/**
 * IndexController handles default requests
 *
 * @author Tim Parham
 *
 */
class IndexController extends ControllerBase {

	/**
	 * Initialize
	 */
	public function initialize() {
		parent::initialize();
	}

	/**
	 * Action for index page.
	 */
	public function indexAction() {
		return $this->response->redirect('photo');	
	}

}
