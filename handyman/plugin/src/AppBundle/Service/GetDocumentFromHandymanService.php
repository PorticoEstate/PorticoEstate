<?php
	/**
	 * Created by PhpStorm.
	 * User: eskil.saatvedt
	 * Date: 19.04.2018
	 * Time: 09:51
	 */

	namespace AppBundle\Service;

	use AppBundle\Entity\FmHandymanDocument;

	class GetDocumentFromHandymanService
	{
		protected $url;

		/**
		 * GetDocumentFromHandymanService constructor.
		 * @param $url
		 */
		public function __construct($url)
		{
			$this->url = $url;
		}

		/**
		 * @param FmHandymanDocument $doc
		 * @param string $path location of where to place the file
		 * @return string The full filepath
		 */
		public function retrieve_file_from_handyman(FmHandymanDocument $doc, string $path): string
		{
			$file_path = $path . '/' . $this->sanitize_file_name($doc->getFilePath());
			if (file_exists($file_path)) {
				return $file_path;
			}
			$fp = fopen($file_path, "w");
			$curl_url = $this->url . (string)$doc->getHsDocumentId();
			$curl_options = array(
				CURLOPT_FILE => $fp,
				CURLOPT_TIMEOUT => 10, // set this to 10 seconds
				CURLOPT_URL => $curl_url,
			);
			$ch = curl_init();
			curl_setopt_array($ch, $curl_options);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);
			return $file_path;
		}

		public static function sanitize_file_name(string $file): string
		{
			// Remove anything which isn't a word, whitespace, number
			// or any of the following caracters -_~,;[]().
			$result = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file);
			// Remove any runs of periods
			$result = mb_ereg_replace("([\.]{2,})", '', $result);
			return $result;
		}
	}