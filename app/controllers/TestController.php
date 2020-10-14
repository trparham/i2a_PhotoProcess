<?php

/**
 * PhotoController handles photo processing requests
 *
 * @author Tim Parham
 *
 */
class TestController extends ControllerBase {

	/**
	 * Initialize
	 */
	public function initialize() {
		parent::initialize();
	}

  /**
   * Action for index page.
   */
	public function indexAction($image=null) {
		$this->view->disable();
		$image = "/uploads/original/" . $image;
		//echo $image;
		//exit;
  	
		echo exec('php ../app/cli.php photo number '. $image, $output, $return_var) . "<br />";
		//var_dump($output);
		//var_dump($return_var);
	}
	
	public function testAction() {
		echo "testAction<br />";
		//GdService::getFolder("RSC", '0B2zsfZOvCeg-d2ZEaWlMcjlPam8');
		
		// Get LP Folder
		//$lpFolderId = GdService::createFolder("RSC", '0B2zsfZOvCeg-d2ZEaWlMcjlPam8');
		$lpFolderId = GdService::getFolderId('RSC2', '0B2zsfZOvCeg-d2ZEaWlMcjlPam8');
		echo $lpFolderId . "<br />";
		
		// Get Venue Folder
		$venueFolderId = GdService::getFolderId('Office', $lpFolderId);
		echo $venueFolderId . "<br />";
		
		// Get Event Folder
		$eventFolderId = GdService::getFolderId('2016-10-09T17:00', $venueFolderId);
		echo $eventFolderId . "<br />";
	}
	
	/**
	 * Test harness for numbering process.
	 * 
	 * @param string $image - Image path
	 */
	public function numberAction($image=null) {
		$this->view->disable();
		$image = "/uploads/original/" . $image;
		 
		echo exec('php ../app/cli.php photo number '. $image, $output, $return_var);
	}
	
	/**
	 * Test harness for preview process.
	 * 
	 * @param unknown $image
	 */
	public function previewAction($image=null, $palette=null) {
		$this->view->disable();
		$image = "/uploads/original/" . $image;
			
		echo exec('php ../app/cli.php photo preview '. $image . " " . $palette, $output, $return_var);
	}
	
	/**
	 * Test harness for finalize process.
	 *
	 * @param unknown $image
	 */
	public function finalizeAction($image=null, $microsite=null, $venue=null, $eventTime=null) {
		$this->view->disable();
		$image = "/uploads/original/" . $image;
			
		echo exec('php ../app/cli.php photo finalize '. 
				$image . " " . 
				$microsite . " " . 
				$venue . " " . 
				$eventTime, $output, $return_var);
	}
}
