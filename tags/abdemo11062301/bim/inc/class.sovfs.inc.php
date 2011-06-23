<?php
/*
 * Class that interacts with the VFS system in portico
 */
phpgw::import_class('bim.bimExceptions');
	/*
	 * Interface for communicating with Portico estate's virtual file system
	 */
	interface sovfs {
		public function addFileToVfs();
		public function getFileInformation();
		public function getFileFromVfs();
		public function removeFileFromVfs();
		public function checkIfFileExists();
		public function retrieveVfsFileId() ;
		public function setFilename($filename);
		public function setFileNameWithFullPath($fileNameWithFullPath);
		/*
		 * Arguments that needed to be set: Filename and Submodule
		 * @return the absolute file name and path of the VFS file in the current filesystem
		 */
		public function getAbsolutePathOfVfsFile();
		/*
		 * Submodule is in effect the subdirectory under /property that the file is put into
		 */
		public function setSubModule($subModule);
		public function getSubModule();
	}
	
	class sovfs_impl implements sovfs {
		/* @var $bofiles property_bofiles */
		private $bofiles;
		private $filename;
		private $fileNameWithFullPath;
		private $subModule;
		public $debug = false;
		
		public function __construct($filename = null, $fileNameWithFullPath = null, $subModule = null){
			$this->bofiles = CreateObject('property.bofiles');
			$this->filename = $filename;
			$this->fileNameWithFullPath = $fileNameWithFullPath;
			$this->subModule = $subModule;
		}
		/*
		 * Required fields: filename and submodule
		 */
		public function getAbsolutePathOfVfsFile() {
			if(empty($this->filename)) {
				throw new Exception("Missing filename");
			}
			$vfsFilenameWithPath = $this->createResultingFileName($this->subModule, $this->filename);
			$pathInfo = ($this->bofiles->vfs->path_parts(array(
										'string'	=> $vfsFilenameWithPath,
										'mask'	=> array (RELATIVE_NONE),
										'fake'	=> false )));
			return $pathInfo->real_full_path;
		}
		/*
		 * code derived from class.uitts.inc.php, from the part where it says '//------------ files'
		 * This method will save files under the 'property' folder in the vfs structure
		 * @return The filename that was added
		 * @throws Exception
		 * @throws FileExistsException
		 * @throws CopyFailureException
		 */
		public function addFileToVfs() {
			if(!$this->filename || !$this->fileNameWithFullPath) {
				$errorString = "\nIllegal arguments\n";
				$errorString = $errorString."filename:$this->filename \n";
				$errorString = $errorString."fileNameWithFullPath:$this->fileNameWithFullPath \n";
				throw new Exception($errorString);
			}
			if($this->debug) {
				echo "Starting add file to VFS with following arguments:\n";
				echo "filename: \t $this->filename \n";
				echo "filename with full path:\t $this->fileNameWithFullPath \n";
				echo "subModule: $this->subModule \n";
			}
			
			$filename = $this->convertSpacesToUnderscores($this->filename);
			$to_file = $this->createResultingFileName($this->subModule, $this->filename);
			
			if($this->debug) {
				echo "Altered filename:\t $filename \n".
					"The TO-FILE:\t $to_file \n";
			}
			
			try {
				if($this->checkIfFileExists()) {
					throw new FileExistsException($filename);
				}
				if($this->subModule) {
					$this->bofiles->create_document_dir($this->subModule);
				}
				$this->bofiles->vfs->override_acl = 1;
				
				$this->copyFileToVfs($this->fileNameWithFullPath, $to_file);
			} catch (FileExistsException $e) {
     			throw $e;	
			} catch ( Exception $e) {
				throw new CopyFailureException("Copy failed, error:".$e);
			}
			$this->bofiles->vfs->override_acl = 0;
			
				
			
			return $filename;
		}
		private function copyFileToVfs($sourceFile, $destinationFile) {
			
			$inputArray = array (
				'from'	=> $sourceFile,
				'to'	=> $destinationFile,
				'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL));
			if($this->debug) {
				echo "InputArray\n";
				print_r($inputArray);
			}
			try {
				$this->bofiles->vfs->cp2($inputArray);
			} catch ( Exception $e) {
				throw $e;
			}
		}
		/*
		 * Needs submodule and filename to work
		 */
		public function checkIfFileExists() {
			$to_file = $this->createResultingFileName($this->subModule, $this->filename);
			if($this->bofiles->vfs->file_exists(array(
				'string' => $to_file,
				'relatives' => array(RELATIVE_NONE)
			)))
			{
				return true;
			}
			return false;
		}
		
		private function createResultingFileName($subModule, $filename) {
			if($subModule) {
				return $this->bofiles->fakebase . '/'.$subModule.'/'.$filename;
			} else {
				return $this->bofiles->fakebase .'/'.$filename;
			}
		}
		
		private function convertSpacesToUnderscores($string) {
			return @str_replace(' ','_',$string);
		}
		
		public function getFileFromVfs() {
			
			
		}
		/*
		 * Needs the filename set, and the submodule ( if applicable)
		 */
		public function removeFileFromVfs() {
			if(!$this->filename) {
				$errorString = "Illegal arguments";
				$errorString = $errorString."filename:$this->filename \n";
				throw new InvalidArgumentException($errorString);
			}
			$file = array();
			$file['file_action'][0] = $this->filename;
			if($this->subModule) {
				$path = "/$this->subModule/";
			} else {
				$path = "/";
			}
			$recieve = $this->bofiles->delete_file($path, $file);
			if($recieve['message']) {
				$result = $recieve['message'][0]["msg"];
			} else {
				$result = $recieve['error'][0]["msg"];
				throw new Exception("Error removing file from vfs:".$result);
			}
			return $result;
		}
		
		public function getFileInformation() {
			if(!$this->filename) {
				$errorString = "Illegal arguments";
				$errorString = $errorString."filename:$this->filename \n";
				throw new InvalidArgumentException($errorString);
			}
			$to_file = $this->createResultingFileName($this->subModule, $this->filename);
			$data = array(
					'string'		=>  $to_file,
					'relatives' 	=> array(RELATIVE_NONE),
					'checksubdirs'	=> false,
					'nofiles'		=> true
				);
			return $this->bofiles->vfs->ls($data);
		}
		
		public function retrieveVfsFileId() {
			$fileInfo = $this->getFileInformation();
			return $fileInfo[0]["file_id"];
		}
		
		public function setFilename($filename) {
			$this->filename = $filename;
		}
		public function setFileNameWithFullPath($fileNameWithFullPath) {
			$this->fileNameWithFullPath = $fileNameWithFullPath;
		}
		public function setSubModule($subModule) {
			$this->subModule = $subModule;
		}
		public function getSubModule(){
			return $this->subModule;
		}
		
	}
