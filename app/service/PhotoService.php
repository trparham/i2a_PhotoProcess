<?php


class PhotoService extends ServiceBase {
	private $image;
	//const CONFIG_PATH = "/var/www/vhosts/gcaphoto/app/config/config.json";
	const CONFIG_PATH = "C:/wamp/www/vhosts/gca_photo/app/config/config.json";
	
	/**
	 * Constructor
	 *
	 */
	public function __construct(Imagick $image=null) {
		if (isset($image)) {
			$this->image = $image;
		}
	}
	
	public function trpTest() {
		echo APPLICATION_PATH . PHP_EOL;
		echo $this->getProperty('basePath') . PHP_EOL;
	}
	
	public function traceAndNumberImage() {
		// Contrast
		$this->image->brightnessContrastImage(0,14);
		$this->image->despeckleimage();
		$this->image->despeckleimage();
		
		// Conversion
		$this->resizeImage(1000, 800);
		$this->grayscaleImage();
		$this->posterizeImage();
		$this->traceImage();
		$this->numberImage();

		// Combine trace and number
		$img1 = new Imagick($this->getProperty('basePath') . $this->getProperty('numberPath') . basename(substr($this->image->getImageFilename(), 0, strrpos($this->image->getImageFilename(), '.'))) . '.jpg');
		$img2 = new Imagick($this->getProperty('basePath') . $this->getProperty('transparentPath') . basename($this->image->getImageFilename()));
		$img1->compositeimage($img2, Imagick::COMPOSITE_ATOP, 0, 0);		
		$this->image = $img1;
		$this->resizeImage(2000, 1600);

		if ($this->writeImage($this->getProperty('basePath') . $this->getProperty('traceNumberPath'))) {
			return $this->getProperty('traceNumberPath') . basename(substr($this->image->getImageFilename(), 0, strrpos($this->image->getImageFilename(), '.'))) . '.jpg';
		}
	}
	
	public function resizeImage($width, $height) {
		// Image processing
		$format = $this->image->getImageGeometry();
		$sizex = $format['width'];
		$sizey = $format['height'];

		if ($sizex > $sizey) { // landscape
			if ($sizex > $width) {
				$this->image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 0.5, true);
			} else {
				$this->image->resizeImage($width, $height, Imagick::FILTER_MITCHELL, 0.5, true);
			}
		} else { // portrait
			if ($sizex > $height) {
				$this->image->resizeImage($height, $width, Imagick::FILTER_LANCZOS, 0.5, true);
			} else {
				$this->image->resizeImage($height, $width, Imagick::FILTER_MITCHELL, 0.5, true);
			}
		}
		$this->image->despeckleimage();
	}
	
	public function grayscaleImage() {
		// Image processing
		$this->image->despeckleimage();
		$this->image->brightnessContrastImage(0, 4);
		$this->image->despeckleimage();
		$this->image->transformImageColorspace(Imagick::COLORSPACE_GRAY);
		$this->image->despeckleimage();
		$this->image->adaptiveblurimage(6,5);
	
		// Save file
		$this->writeImage($this->getProperty('basePath') . $this->getProperty('grayscalePath'));
	}
	
	public function posterizeImage() {
		// Posterize
		$this->image->posterizeImage(5, Imagick::DITHERMETHOD_FLOYDSTEINBERG);
	
		// Save file
		$this->writeImage($this->getProperty('basePath') . $this->getProperty('posterizePath'));
	}
	
	public function traceImage() {
		// Trace
		exec('convert ' . 
				$this->image->getImageFilename() . 
				' -canny 0x1+0.3%+30% -negate ' . 
				$this->getProperty('basePath') . $this->getProperty('tracePath') . 
				basename($this->image->getImageFilename()));

		// Transparent
		exec('convert ' . 
				$this->getProperty('basePath') . $this->getProperty('tracePath') . basename($this->image->getImageFilename()) . 
				' -transparent white ' .  
				$this->getProperty('basePath') . $this->getProperty('transparentPath') . 
				basename($this->image->getImageFilename()));
	}
	
	public function numberImage() {
		// Number
		exec('convert ' . 
				$this->image->getImageFilename() . ' ' . 
				$this->getProperty('basePath') . '/images/num5fill.gif' .
				' -virtual-pixel tile -fx "u[floor(4.999*u)+1]" ' .
				$this->getProperty('basePath') . $this->getProperty('numberPath') .
				basename(substr($this->image->getImageFilename(), 0, strrpos($this->image->getImageFilename(), '.'))) . '.jpg');
	}
	
	public function createPreviewImage($palette) {
		exec('convert ' . $this->getProperty('basePath') . $this->getProperty('posterizePath') . basename($this->image->getImageFilename()) . 
				' ' . $this->getProperty('basePath') . '/images/' . $palette . '.gif' . 
				' -virtual-pixel tile -fx "u[floor(4.999*u)+1]" ' . 
				$this->getProperty('basePath') . 
				$this->getProperty('previewPath') . 
				basename($this->image->getImageFilename())
				//substr(basename($this->image->getImageFilename()), 0, strrpos(basename($this->image->getImageFilename()), '.')) . '.png'
		);
		
		exec('convert ' . $this->getProperty('basePath') . 
				$this->getProperty('previewPath') . 
				basename($this->image->getImageFilename()) . ' ' .
				$this->getProperty('basePath') .
				$this->getProperty('previewPath') .
				substr(basename($this->image->getImageFilename()), 0, strrpos(basename($this->image->getImageFilename()), '.')) . '.jpg'
		);
		
		unlink($this->getProperty('basePath') . $this->getProperty('previewPath') . basename($this->image->getImageFilename()));
		
		return $this->getProperty('previewPath') . substr(basename($this->image->getImageFilename()), 0, strrpos(basename($this->image->getImageFilename()), '.')) . '.jpg';
	}
	
	public function finalizeImage($fileName, $microsite, $venue, $eventTime) {
		// Grab trace & number
		//$this->image = new Imagick($this->getProperty('basePath') . $this->getProperty('grayscalePath') . basename($this->image->getImageFilename()));
		$this->image = new Imagick($this->getProperty('basePath') . $this->getProperty('originalPath') . basename($this->image->getImageFilename()));
		
		// Contrast
		$this->image->brightnessContrastImage(0,14);
		$this->image->despeckleimage();
		$this->image->despeckleimage();
		
		// Generate final image at proper size
		$this->resizeImage(2000, 1600);
		$this->grayscaleImage();
		$this->posterizeImage();
		$this->traceImage();
		$this->numberImage();
		
		$img1 = new Imagick($this->getProperty('basePath') . $this->getProperty('numberPath') . basename(substr($this->image->getImageFilename(), 0, strrpos($this->image->getImageFilename(), '.'))) . '.jpg');
		$img2 = new Imagick($this->getProperty('basePath') . $this->getProperty('transparentPath') . basename($this->image->getImageFilename()));
		$img1->compositeimage($img2, Imagick::COMPOSITE_ATOP, 0, 0);
		
		$this->image = $img1;

		// Store Final Image
		$this->image->setImageFormat("jpg");
		$this->writeImage($this->getProperty('basePath') . $this->getProperty('finalPath'));
		
		// Check and create folders
		// echo $microsite;
		// echo $venue;
		// echo $eventTime;
		
		// Get LP Folder
		$lpFolderId = GdService::getFolderId($microsite, $this->getProperty('folder', 'drive'));
		
		// Get Venue Folder
		$venueFolderId = GdService::getFolderId($venue, $lpFolderId);
		
		// Get Event Folder
		if ($eventTime != null && !empty($eventTime)) {
			$eventFolderId = GdService::getFolderId($eventTime, $venueFolderId);
		} else {
			$eventFolderId = $venueFolderId;
		}
		//$fileMetadata = new Google_Service_Drive_DriveFile(array(
		//		'name' => $microsite,
		//		'parents' => array('0B2zsfZOvCeg-d2ZEaWlMcjlPam8'),
		//		'mimeType' => 'application/vnd.google-apps.folder'));
		//$file = $driveService->files->create($fileMetadata, array(
		//		'fields' => 'id'));
		//echo ($file->id);
		
		// Force jpg format
		$fileName = basename(substr($fileName, 0, strrpos($fileName, '.'))) . '.jpg';

		// Deliver image to Google Drive
		//print_r($this->image->identifyImage());
		$doc = $this->image->identifyImage();
		GdService::storeDocument($fileName, $doc['mimetype'], $doc['imageName'], $eventFolderId);
		
		// Store Reference Image
		$doc['imageName'] = $this->getProperty('basePath') . $this->getProperty('previewPath') . basename($this->image->getImageFilename());
		GdService::storeDocument($fileName, $doc['mimetype'], $doc['imageName'], $eventFolderId);
		
		// Store Original Image
		$fileName = basename(substr($fileName, 0, strrpos($fileName, '.'))) . '.png';
		//echo 'file - ' . $fileName . '<br />';
		$doc['imageName'] = $this->getProperty('basePath') . $this->getProperty('originalPath') . substr(basename($this->image->getImageFilename()), 0, strrpos(basename($this->image->getImageFilename()), '.')) . '.png';
		//echo 'imageName - ' . $doc['imageName'] . '<br />';
		GdService::storeDocument($fileName, $doc['mimetype'], $doc['imageName'], $eventFolderId);
		
		// @todo Remove final image from server - It is stored on Google Drive
	}
	
	// @todo Clean up this function
	public function uploadImage($request) {
		$config = new Phalcon\Config\Adapter\Json("../app/config/config.json");
		$basePath = $config->application->basePath;
		$uploadpath = $config->application->originalPath;
	
		// Check if the user has uploaded files
		if ($request->hasFiles() == true) {
			foreach ($request->getUploadedFiles() as $file) {
				//$uploadfilename = $uploadpath . $file->getName();
				$finalfilename = $uploadpath . RSCString::generateRandomString() . '.png';
	
				// Was the uploaded file an approved image type?
				switch (exif_imagetype($file->getTempName())) {
					case IMAGETYPE_GIF :
					case IMAGETYPE_JPEG :
					case IMAGETYPE_PNG :
						try {
							//$file->moveTo($basePath . $uploadfilename);
							//$image = new Phalcon\Image\Adapter\GD($basePath . $uploadfilename);
							$image = new Phalcon\Image\Adapter\GD($file->getTempName());
	
							if ($image->save($basePath . $finalfilename)) {
								$files [] = array(
										'status' => 200,
										'message' => 'File Saved Successfully',
										'original_file' => $finalfilename,
										'final_file' => $finalfilename
								);
							}
						} catch (Exception $e) {
							$files [] = array(
									'status' => 500,
									'message' => 'Unhandled Exception',
									'original_file' => $uploadfilename,
									'final_file' => ''
							);
						}
						break;
					default :
						$files [] = array(
						'status' => 500,
						'message' => 'Invalid File Type',
						'original_file' => $uploadfilename,
						'final_file' => ''
								);
				}
			}
		}
		if (!IsSet($files)) {
			$files [] = array(
					'status' => 404,
					'message' => 'Upload Failed',
					'original_file' => '',
					'final_file' => ''
			);
		}
		return $files;
		//echo json_encode($files);
	}
	
	private function writeImage($path) {
		unlink($path . basename($this->image->getImageFilename()));
		return $this->image->writeimage($path . basename($this->image->getImageFilename()));
	}
	
	// @todo move getProperty function to separate class
	private function getProperty($propName, $categoryName=null) {
		$config = new Phalcon\Config\Adapter\Json(APPLICATION_PATH . '/config/config.json');
		if ($categoryName == null) {
			return $config->application->$propName;
		} else {
			return $config->$categoryName->$propName;
		}
	}
}
