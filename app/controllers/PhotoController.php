<?php

/**
 * PhotoController handles web based photo processing requests.
 *
 * @author Tim Parham
 *
 */
class PhotoController extends ControllerBase {

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
	}
  
	/**
	 * Upload images.
	 *
	 */
	public function uploadImageAction(){
		try {
			$this->view->disable();
			//echo "here<br>";
			
  		
			if ($this->request->isPost() == true) {
				//echo "post<br>";
				
				if ($this->request->hasFiles() == true) {
					//echo "has files";
					
					foreach ($this->request->getUploadedFiles() as $file) {
// 						var_dump($file);
// 						$pos = strrpos('/tmp/php4IrrBp', '.');
// 						if ($pos === false) {
// 							echo "no extension<br>";
// 							echo basename('/tmp/php4IrrBp');
// 						}
// 						echo basename(substr($file->getTempName(), 0, strrpos($file->getTempName(), '.'))) . '.png<br>';
// 						exit;
						// Imagick
						//$image = new Imagick($file->getTempName());
						//$image->readImage($file->getTempName());
						//$image->brightnessContrastImage(-15, 10, imagick::CHANNEL_ALL);
						//$image->setImageUnits(1);
						//$image->setImageResolution(72, 72);
						//$image->setImageFormat("png");
						//$image->resampleImage(72,72,imagick::FILTER_UNDEFINED,1);
						//$image = new Imagick($file->getTempName());
						//$image->writeImage($this->config->application->basePath . $this->config->application->originalPath . basename(substr($file->getTempName(), 0, strrpos($file->getTempName(), '.'))) . '.png');
						
						// Phalcon
						$image = new Phalcon\Image\Adapter\GD($file->getTempName());
						if (strrpos($file->getTempName(), '.') === false) {
							$originalFile = $this->config->application->originalPath . basename($file->getTempName() . '.png');
						} else {
							$originalFile = $this->config->application->originalPath . basename(substr($file->getTempName(), 0, strrpos($file->getTempName(), '.'))) . '.png';
						}
						$image->save($this->config->application->basePath . $originalFile);

						$files [] = array(
								'status' => 200,
								'message' => 'File Saved Successfully',
								'original_file' => $originalFile,
								'final_file' => $originalFile
						);
					}
				}
				//$photoService = new PhotoService();
				//$files = $photoService->uploadImage($this->request);
				// @todo move resizing to here
			}
  		
			echo json_encode($files);
		} catch (Exception $e) {
			// @todo capture original file name for error reporting
			echo json_encode(array(
				'status' => 500,
				'message' => $e->getMessage(),
				'original_file' => '',
				'final_file' => ''
			), JSON_NUMERIC_CHECK);
		}
	}
  
	/**
	 * Create traced and numbered image.
	 * 
	 */
	public function numberAction(){
		try{
			// @todo remove metrics reporting
			$startTime = new DateTime();
  
			$this->view->disable();
  
			// Get request data
			$reqData = (object) $this->request->getPost();
			$originalImage = $reqData->image;
  
			// Simulate request data
			//$originalImage = '/uploads/original/test.png';
  
			// Image processing
			$image = new Imagick($this->config->application->basePath . $originalImage);
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
				'endTime' => $endTime->format('Y-m-d H:i:s'),
				'elapsed' => $elapsed
			), JSON_NUMERIC_CHECK);
		}
	}

	/**
	 * Generate reference image.
	 * 
	 */
	public function previewAction(){
		try{
			// @todo remove metrics reporting
			$startTime = new DateTime();
  
			$this->view->disable();
  
			// Get request data
			$reqData = (object) $this->request->getPost();
			$originalImage = $reqData->image;
			$palette = $reqData->palette;
  
			// Simulate request data
			//$originalImage = '/uploads/original/test.png';
			//$palette = 'rose';
  
			// Image processing
			$image = new Imagick($this->config->application->basePath . $originalImage);
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
				'endTime' => $endTime->format('Y-m-d H:i:s'),
				'elapsed' => $elapsed
			), JSON_NUMERIC_CHECK);
		}
	}
	
	public function finalizeAction() {
		try {
			// @todo remove metrics reporting
			$startTime = new DateTime();
			
			$this->view->disable();
			
			// Get request data
			$reqData = (object) $this->request->getPost();
			$finalFileName = $reqData->finalFileName;
			$originalImage = $reqData->image;
			$microsite = $reqData->microsite;
			$venue = $reqData->venue;
			$eventTime = $reqData->eventTime;
			
			// Simulate request data
			//$originalImage = '/uploads/original/test.png';
			
			// Image processing
			$image = new Imagick($this->config->application->basePath . $originalImage);
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