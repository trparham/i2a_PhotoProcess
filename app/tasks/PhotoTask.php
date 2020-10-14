<?php

class PhotoTask extends \Phalcon\Cli\Task {
	
	public function testAction(array $params=null) {
		echo $this->config->application->basePath . PHP_EOL;
		echo APPLICATION_PATH . PHP_EOL;
		$service = new PhotoService();
		$service->trpTest();
		
	}
    
    public function numberAction(array $params=null){
    	try{
    		// @todo remove metrics reporting
    		$startTime = new DateTime();
    		
    		// Get paramters
    		$originalImage = $params[0];
    		$image = new Phalcon\Image\Adapter\GD($originalImage);
    		$image->save($this->config->application->basePath . $this->config->application->originalPath . basename(substr($originalImage, 0, strrpos($originalImage, '.'))) . '.png');
    
     		// Image processing
     		//$image = new Imagick($this->config->application->basePath . $originalImage);
    		// @todo cleanup PNG conversion, so this hack can be removed
     		$image = new Imagick($this->config->application->basePath . $this->config->application->originalPath . basename(substr($originalImage, 0, strrpos($originalImage, '.'))) . '.png');
     		//$image->brightnessContrastImage(-15, 10, imagick::CHANNEL_ALL);
     		//$image = new Imagick($originalImage);
     		$service = new PhotoService($image);
     		$numberedImage = $service->traceAndNumberImage();
    
     		$endTime = new DateTime();
     		$elapsed = $endTime->diff($startTime)->format('%H:%I:%S');
    
     		// Return response
     		echo json_encode(array(
     				'status' => 200,
     				'message' => 'Success',
     				'originalImage' => $originalImage,
     				'numberedImage' => $numberedImage,
     				'referenceImage' => '',
     				'startTime' => $startTime->format('Y-m-d H:i:s'),
     				'endTime' => $endTime->format('Y-m-d H:i:s'),
     				'elapsed' => $elapsed
     		), JSON_NUMERIC_CHECK);
    
     	} catch (Exception $e) {
     		echo json_encode(array(
     				'status' => 500,
     				'message' => $e->getMessage(),
     				'originalImage' => $originalImage,
     				'numberedImage' => '',
     				'referenceImage' => '',
     				'startTime' => $startTime->format('Y-m-d H:i:s'),
     				'endTime' => '',
     				'elapsed' => ''
     		), JSON_NUMERIC_CHECK);
     	}
    }
    
    public function previewAction(array $params=null){
    	try{
    		// @todo remove metrics reporting
    		$startTime = new DateTime();

    		// Get paramters
    		$originalImage = $params[0];
    		$palette = $params[1];
    
    		// Image processing
    		//$image = new Imagick($this->config->application->basePath . $originalImage);
    		// @todo cleanup PNG conversion, so this hack can be removed
    		//$image = new Imagick($originalImage);
    		$image = new Imagick($this->config->application->basePath . $this->config->application->originalPath . basename(substr($originalImage, 0, strrpos($originalImage, '.'))) . '.png');
    		$service = new PhotoService($image);
    		$referenceImage = $service->createPreviewImage($palette);
    
    		$endTime = new DateTime();
    		$elapsed = $endTime->diff($startTime)->format('%H:%I:%S');
    
    		// Return response
    		echo json_encode(array(
    				'status' => 200,
    				'message' => 'Success',
    				'originalImage' => $originalImage,
    				'numberedImage' => $this->config->application->traceNumberPath . basename($image->getImageFilename()),
    				'referenceImage' => $referenceImage,
    				'startTime' => $startTime->format('Y-m-d H:i:s'),
    				'endTime' => $endTime->format('Y-m-d H:i:s'),
    				'elapsed' => $elapsed
    		), JSON_NUMERIC_CHECK);
    
    	} catch (Exception $e) {
    		echo json_encode(array(
    				'status' => 500,
    				'message' => $e->getMessage(),
    				'originalImage' => $originalImage,
    				'numberedImage' => '',
    				'referenceImage' => '',
    				'startTime' => $startTime->format('Y-m-d H:i:s'),
    				'endTime' => '',
    				'elapsed' => ''
    		), JSON_NUMERIC_CHECK);
    	}
    }
    
    public function finalizeAction(array $params=null) {
    	try {
    		// @todo remove metrics reporting
    		$startTime = new DateTime();

    		// Get paramters
    		$originalImage = $params[0];
    		$finalFileName = $params[1];
    		$microsite = $params[2];
    		$venue = $params[3];
    		if (isset($params[4])) {
	    		$eventTime = $params[4];
    		} else {
    			$eventTime = null;
    		}
    			
    		// Image processing
    		//$image = new Imagick($this->config->application->basePath . $originalImage);
    		// @todo cleanup PNG conversion, so this hack can be removed
    		//$image = new Imagick($originalImage);
    		//$image = new Imagick($this->config->application->basePath . $this->config->application->originalPath . basename(substr($originalImage, 0, strrpos($originalImage, '.'))) . '.png');
    		$image = new Imagick($this->config->application->basePath . $this->config->application->originalPath . basename(substr($originalImage, 0, strrpos($originalImage, '.'))) . '.png');
    		$service = new PhotoService($image);
    		$service->finalizeImage($finalFileName, $microsite, $venue, $eventTime);
    			
    		$endTime = new DateTime();
    		$elapsed = $endTime->diff($startTime)->format('%H:%I:%S');
    			
    		echo json_encode(array(
    				'status' => 200,
    				'message' => 'Success',
    				'originalImage' => $originalImage,
    				'startTime' => $startTime->format('Y-m-d H:i:s'),
    				'endTime' => $endTime->format('Y-m-d H:i:s'),
    				'elapsed' => $elapsed
    		), JSON_NUMERIC_CHECK);
    			
    	} catch (Exception $e) {
    		echo json_encode(array(
    				'status' => 500,
    				'message' => $e->getMessage(),
    				'originalImage' => $originalImage,
    				'startTime' => $startTime->format('Y-m-d H:i:s'),
    				'endTime' => '',
    				'elapsed' => ''
    		), JSON_NUMERIC_CHECK);
    	}
    }
}

