<?php

require_once __DIR__ . '/../../lib/Google/vendor/autoload.php';
define('CHUNK', 1 * 1024 * 1024);

class GdService {

    public static function storeDocument($name, $type, $filePath, $folder) {
        $handle = null;
        $result = false;
        
        try {
            // Create drive file
            $file = new Google_Service_Drive_DriveFile(array(
                'name' => $name,
                'parents' => array($folder)
            ));

            // Initialize service
            $client = self::getClient();
            $client->setDefer(true);
            $service = new Google_Service_Drive($client);        
            $request = $service->files->create($file);

            // Create a media file upload to represent our upload process.
            $media = new Google_Http_MediaFileUpload(
                $client, 
                $request, 
                $type, 
                null, 
                true, 
                CHUNK
            );
            $media->setFileSize(filesize($filePath));

            // Upload the various chunks. $status will be false until the process is complete.
            $handle = fopen($filePath, 'rb');
            $status = false;
            while (!$status && !feof($handle)) {
                $chunk = self::readChunk($handle, CHUNK);
                $status = $media->nextChunk($chunk);
            }
            fclose($handle);

            // The final value of $status will be the data from the API for the object that has been uploaded.
            if ($status != false) {
                $result = $status;
            }
        }
        catch (Exception $e) {
            if ($handle !== null) {
                fclose($handle);
            }            
            
            throw $e;
        }
        
        return $result;        
    }
    
    public static function getFolderId($folderName, $parentFolder) {
    	try {
    		$client = self::getClient();
    		$driveService = new Google_Service_Drive($client);
    		$pageToken = null;
    		
    		do {
    			$response = $driveService->files->listFiles(array(
					'q' => "'$parentFolder' in parents 
    					and mimeType='application/vnd.google-apps.folder' 
    					and name = '$folderName'",
					'spaces' => 'drive',
					'pageToken' => $pageToken,
					'fields' => 'nextPageToken, files(id, name)',
    			));
    			if (count($response->files) == 1) {
    				return $response->files[0]->id;
    			} else if (count($response->files) > 1) {
    				throw new Exception("Duplicate folders exist");
    			} else {
    				return self::createFolder($folderName, $parentFolder);
    			}
//     			foreach ($response->files as $file) {
//     				printf("Found file: %s (%s)\n<br />", $file->name, $file->id);
//     			}
    		} while ($pageToken != null);

    		return null;
    		
    	} catch (Exception $e) {
    		throw $e;
    	}
    }
    
    public static function createFolder($folderName, $parentFolder) {
    	try {
    		//$folder = self::getFolder($folderName, $parentFolder);
    		//if ($folder == null) {
	    		$client = self::getClient();
	    		$driveService = new Google_Service_Drive($client);
	    		 
	    		$fileMetadata = new Google_Service_Drive_DriveFile(array(
	    				'name' => $folderName,
	    				'parents' => array($parentFolder),
	    				'mimeType' => 'application/vnd.google-apps.folder'));
	    		$folder = $driveService->files->create($fileMetadata, array('fields' => 'id'));
    		//}
    		return $folder->id;;
    	} catch (Exception $e) {
    		throw $e;
    	}
    }

//     private static function getFolderId() {
//         $di = Phalcon\DI::getDefault();
//         $config = $di->get('config');
        
//         return $config->drive->folder;
//     }

    private static function getClient() {
        $di = Phalcon\DI::getDefault();
        $config = $di->get('config');
        $dir = __DIR__; // debug issue requires variable assignment before use

        $client = new Google_Client();
        $credentials = Google\Auth\Credentials\ServiceAccountCredentials::makeCredentials(array(
            Google_Service_Drive::DRIVE
        ), json_decode(file_get_contents($dir . $config->drive->credentialsPath), true));
        $credentials->setSub($config->drive->user);
        $token = $credentials->fetchAuthToken();
        $client->setAccessToken($token);

        return $client;
    }
  
    private static function readChunk ($handle, $chunkSize)
    {
        $byteCount = 0;
        $giantChunk = "";
        while (!feof($handle)) {
            // fread will never return more than 8192 bytes if the stream is read buffered and it does not represent a plain file
            $chunk = fread($handle, 8192);
            $byteCount += strlen($chunk);
            $giantChunk .= $chunk;
            if ($byteCount >= $chunkSize)
            {
                return $giantChunk;
            }
        }
        return $giantChunk;
    }

    
    
    
/**
 * The following dead code was used in identifying the 
 * folder ID of the upload folder and is left here for 
 * convenience.
 */    
    
//    public static function test() {
//        $files = [];
//        try {
//            // Get the API client and construct the service object.
//            $client = self::getClient();
//            $service = new Google_Service_Drive($client);
//
//            $response = $service->files->listFiles([
//                'q' => "'0BxzpvgYPhjbHRlVvRURPb3ZhQ1E' in parents"
//            ]);
//            foreach ($response->files as $child) {
//                $files[$child->getName() . ' ' . $child->getId()] = null;
//            }
//
//
////            $pageToken = null;
////            do {
////              $response = $service->files->listFiles(array(
////                'q' => "mimeType='application/vnd.google-apps.folder'",
////                'spaces' => 'drive',
////                'pageToken' => $pageToken,
////                'fields' => 'nextPageToken, files(id, name, parents)',
////              ));
////              foreach ($response->files as $file) {
////                  $files[sprintf("%s (%s)\n", $file->name, $file->id)] = $file->parents;
////              }
////            } while ($pageToken != null);
//        } catch (Exception $e) {
//            RSCError::log_error($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
//        }
//
//        return $files;
//    }
}
