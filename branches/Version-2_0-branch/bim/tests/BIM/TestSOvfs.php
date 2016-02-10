<?php
	/*
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Lesser General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

	include_once './TestBimCommon.php';

	class TestSOvfs extends TestBimCommon
	{
		/* private $modelName = "unitTestModel";
	private $vfsFileName = "dummyFile.txt";
	private $vfsFileNameWithFullPath;
	private $vfsFileContents = "This is a file made for unit testing, please ignore or delete";
	private $vfsSubModule = "ifc";
		  private $vfsFileId = 10101010; */
	
	/**
	 * @var boolean $backupGlobals disable backup of GLOBALS which breaks things
	 */
	protected $backupGlobals = false;

	/**
	 * Setup the environment for the tests
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$GLOBALS['phpgw_info']['user']['account_id'] = 7;
		$this->vfsFileName = "soVfsTestDummyFile.txt"; // so that these tests will not interfere with the sobimmodel tests
	}
	
		public function testAddFile()
		{
		$this->createDummyFile();
		$sovfs = new sovfs_impl();
		//$sovfs->debug = true;
		$sovfs->setFilename($this->vfsFileName);
		$sovfs->setFileNameWithFullPath($this->vfsFileNameWithFullPath);
		$sovfs->setSubModule($this->vfsSubModule);
		$sovfs->debug = true;
			try
			{
			$sovfs->addFileToVfs();
			echo "Success!";
			}
			catch(FileExistsException $e)
			{
			echo "File already exists\n";
		}
		$this->removeDummyFile();
	}
	/*
	public function testaddCheckDeleteFileToVfs() {
		$this->createDummyFile();
		
		$sovfs = new sovfs_impl();
		//$sovfs->debug = true;
		$sovfs->setFilename($this->vfsFileName);
		$sovfs->setFileNameWithFullPath($this->vfsFileNameWithFullPath);
		$sovfs->setSubModule($this->vfsSubModule);
		$sovfs->debug = true;
		try {
			$sovfs->addFileToVfs();
		} catch (FileExistsException $e) {
			echo "File already exists\n";
		}
		$this->assertTrue($sovfs->checkIfFileExists());
		$sovfs->removeFileFromVfs();
		$this->assertTrue(!$sovfs->checkIfFileExists());
		
		$this->removeDummyFile();
	}
	*/
	/*
	 * This test checks the OS for the file
	 */ /*
	public function testaddCheckOSDeleteFileToVfs() {
		$this->createDummyFile();
		
		$sovfs = new sovfs_impl();
		//$sovfs->debug = true;
		$sovfs->setFilename($this->vfsFileName);
		$sovfs->setFileNameWithFullPath($this->vfsFileNameWithFullPath);
		$sovfs->setSubModule($this->vfsSubModule);
		try {
			$sovfs->addFileToVfs();
		} catch (FileExistsException $e) {
			echo "File already exists\n";
		}
		$sovfs2 = new sovfs_impl();
		$sovfs2->setFilename($this->vfsFileName);
		$sovfs2->setSubModule($this->vfsSubModule);
		$OS_pathAndFilename = $sovfs2->getAbsolutePathOfVfsFile();
		$this->assertTrue(file_exists($OS_pathAndFilename), "File does not exist!");
		
		$sovfs->removeFileFromVfs();
		$this->assertTrue(!file_exists($OS_pathAndFilename), "File still exists!");
		$this->assertTrue(!$sovfs->checkIfFileExists());
		
		$this->removeDummyFile();
	}
	
	*/
	
		/* private function createDummyFile() {
		$currentDirectory = dirname(__FILE__);
		$this->vfsFileNameWithFullPath = $currentDirectory.DIRECTORY_SEPARATOR.$this->vfsFileName;
		
		$fileHandle = fopen($this->vfsFileNameWithFullPath, 'w') or die("Can't open file");
		$result = fwrite($fileHandle, $this->vfsFileContents);
		fclose($fileHandle);
	}
	private function removeDummyFile() {
		unlink($this->vfsFileNameWithFullPath);
		  } */
	
		private function removeDummyVfsFile()
		{
		$sovfs = new sovfs_impl();
		$sovfs->setFilename($this->vfsFileName);
		$sovfs->setSubModule($this->vfsSubModule);
		
		$sovfs->removeFileFromVfs();
	}
	}