<?php
	phpgw::import_class('phpgwapi.uicommon_jquery');

	/**
	 * Cherry pick selected values into a new array
	 * 
	 * @param array $array    input array
	 * @param array $keys     array of keys to pick
	 *
	 * @return array containg values from $array for the keys in $keys.
	 */
	function extract_values( $array, $keys, $options = array() )
	{
		static $default_options = array(
			'prefix' => '',
			'suffix' => '',
			'preserve_prefix' => false,
			'preserve_suffix' => false
		);

		$options = array_merge($default_options, $options);
		$result = array();

		$isIndexed = array_values($keys) === $keys;
		if ($isIndexed)
		{
			foreach ($keys as $write_key)
			{
				$array_key = $options['prefix'] . $write_key . $options['suffix'];
				if (isset($array[$array_key]))
				{
					$result[($options['preserve_prefix'] ? $options['prefix'] : '') . $write_key . ($options['preserve_suffix'] ? $options['suffix'] : '')] = phpgw::clean_value($array[$array_key]);
				}
			}
		}
		else
		{
			foreach ($keys as $write_key => $type)
			{
				switch ($type)
				{
					case 'string':
					default:
						$_type = 'string';
						break;
					case 'bool':
					case 'boolean':
						$_type = 'bool';
						break;
					case 'int':
					case 'integer':
					case 'number':
						$_type = 'int';
						break;
					case 'float':
					case 'double':
						$_type = 'float';
						break;
					/* Specific string types */
					case 'color':
						$_type = 'color';
						break;
					case 'email':
						$_type = 'email';
						break;
					case 'filename':
						$_type = 'filename';
						break;
					case 'ip':
						$_type = 'ip';
						break;
					case 'location':
						$_type = 'location';
						break;
					case 'url':
						$_type = 'url';
						break;
					/* only use this if you really know what you are doing */
					case 'raw':
						$_type = 'raw';
						break;
					case 'html':
						$_type = 'html';
						break;
				}
				$array_key = $options['prefix'] . $write_key . $options['suffix'];
				if (isset($array[$array_key]))
				{
					$result[($options['preserve_prefix'] ? $options['prefix'] : '') . $write_key . ($options['preserve_suffix'] ? $options['suffix'] : '')] = phpgw::clean_value($array[$array_key], $_type);
				}
			}
		}

		return $result;
	}

	function array_set_default( &$array, $key, $value )
	{
		if (!isset($array[$key]))
			$array[$key] = $value;
	}

	/**
	 * Reformat an ISO timestamp into norwegian format
	 * 
	 * @param string $date    date
	 *
	 * @return string containg timestamp in norwegian format
	 */
	function pretty_timestamp( $date )
	{
		if (empty($date))
			return "";

		if (is_array($date) && is_object($date[0]) && $date[0] instanceof DOMNode)
		{
			$date = $date[0]->nodeValue;
		}
		preg_match('/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})( ([0-9]{2}):([0-9]{2}))?/', $date, $match);

		$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		if ($match[4])
		{
			$dateformat .= ' H:i';
			$timestamp = mktime($match[5], $match[6], 0, $match[2], $match[3], $match[1]);
		}
		else
		{
			$timestamp = mktime(0, 0, 0, $match[2], $match[3], $match[1]);
		}
		$text = date($dateformat, $timestamp);

		return $text;
	}

	abstract class booking_uicommon extends phpgwapi_uicommon_jquery
	{

		protected static
			$old_exception_handler;
		private
			$config;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			self::add_javascript('booking', 'base', 'common');

			$this->ui_session_key = $this->current_app() . '_uicommon';
			//$this->restore_flash_msgs();
			$this->config = CreateObject('phpgwapi.config', 'bookingfrontend');
			$this->config->read();

			if ($this->current_app() == 'bookingfrontend')
			{
				$GLOBALS['phpgw']->translation->add_app('booking');
			}

			phpgwapi_jquery::load_widget('autocomplete');

		}

		/**
		 * @see booking_account_helper
		 */
		public static function current_account_id()
		{
			return booking_account_helper::current_account_id();
		}

		/**
		 * @see booking_account_helper
		 */
		public static function current_account_lid()
		{
			return booking_account_helper::current_account_lid();
		}

		/**
		 * @see booking_account_helper
		 */
		public static function current_account_fullname()
		{
			return booking_account_helper::current_account_fullname();
		}

		public static function encoding()
		{
			return 'UTF-8';
		}

		public static function process_booking_unauthorized_exceptions()
		{
			if (!self::$old_exception_handler)
			{
				self::$old_exception_handler = set_exception_handler(array(__CLASS__, 'handle_booking_unauthorized_exception'));
				if (!self::$old_exception_handler)
				{
					//The exception handler of phpgw has probably not been activated, 
					//so taking that as a hint to not enable any of our own either.
					restore_exception_handler();
				}
			}
		}

		public static function handle_booking_unauthorized_exception( Exception $e )
		{
			if ($e instanceof booking_unauthorized_exception)
			{
				$message = htmlentities('HTTP/1.0 401 Unauthorized to ' . $e->get_operation(), null, self::encoding());
				$exception_message = $e->getMessage();

				if (!empty($exception_message))
					$message .= ' - ' . htmlentities($exception_message, null, self::encoding());

				header($message);
				echo "<html><head><title>$message</title></head><body><strong>$message</strong></body></html>";
			} else
			{
				if (self::$old_exception_handler)
				{
					call_user_func(self::$old_exception_handler, $e);
				}
			}
		}

		protected function current_app()
		{
			return $GLOBALS['phpgw_info']['flags']['currentapp'];
		}

		public function in_frontend()
		{
			return $this->current_app() == 'bookingfrontend';
		}

		public function link( $data )
		{
			if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
			{
				return $GLOBALS['phpgw']->link('/bookingfrontend/', $data);
			}
			else
			{
				return $GLOBALS['phpgw']->link('/index.php', $data);
			}
		}

		public function redirect( $link_data )
		{
			$this->store_flash_msgs();

			if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
			{
				$GLOBALS['phpgw']->redirect_link('/bookingfrontend/', $link_data);
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}
		}

		public function create_error_stack( $errors = array() )
		{
			return CreateObject('booking.errorstack', $errors);
		}

		public function flash_form_errors( $errors )
		{
			$error_stack = $this->create_error_stack($errors);
			$this->flash_msgs = $error_stack->to_flash_error_msgs();
		}

		public function render_template( $files, $data )
		{
			parent::render_template_xsl($files, $data);
		}

		public function send_file( $file_path, $options = array() )
		{
			if (!is_readable($file_path))
			{
				throw new InvalidArgumentException('File is not readable');
			}

			$base_name = basename($file_path);
			$file_type = self::get_file_type_from_extension($base_name);

			$options = array_merge(
				array('filename' => $base_name), $options
			);

			$options['latin1_filename'] = utf8_decode($options['filename']);
			$options['utf8_filename'] = rawurlencode($options['filename']);

			#Below only seems to work for firefox. RE: http://www.ietf.org/rfc/rfc2047.txt
			#header("Content-Disposition: attachment; filename*=utf-8'en-us'{$options['filename']}");
			//The behaviour of sending both the filename both in traditional format and in utf-8 RFC2231 encoded is undefined. 
			//However, in reality (where most of us live), UAs pick one of the two values that it understands. 
			header("Content-Disposition: attachment; filename={$options['latin1_filename']}");
			#header("Content-Description: {$options['filename']}");
			header("Content-Type: $file_type");
			# IE6 needs this one
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: cache");
			readfile($file_path, false);
			exit;
		}

		public function check_active( $url )
		{
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$activate = extract_values($_POST, array("status", "activate_id"));
				$this->bo->set_active(intval($activate['activate_id']), intval($activate['status']));
				$this->redirect(array('menuaction' => $url, 'id' => $activate['activate_id']));
			}
		}

		protected static function get_file_type_from_extension( $file, $defaultType = 'application/octet-stream' )
		{
			if (false === ($extension = (false === $pos = strrpos($file, '.')) ? false : substr($file, $pos + 1)))
			{
				return $defaultType;
			}

			if (strlen($extension) == 0)
			{
				return $defaultType;
			}

			switch ($extension)
			{
				case 'ez': return 'application/andrew-inset';
				case 'base64': return 'application/x-word';
				case 'dp': return 'application/commonground';
				case 'pqi': return 'application/cprplayer';
				case 'tsp': return 'application/dsptype';
				case 'xls': return 'application/x-msexcel';
				case 'xlsx': return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
				case 'pfr': return 'application/font-tdpfr';
				case 'spl': return 'application/x-futuresplash';
				case 'stk': return 'application/hyperstudio';
				case 'js': return 'application/x-javascript';
				case 'hqx': return 'application/mac-binhex40';
				case 'cpt': return 'application/x-mac-compactpro';
				case 'mbd': return 'application/mbed';
				case 'mfp': return 'application/mirage';
				case 'doc': return 'application/x-msword';
				case 'docx': return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
				case 'orq': return 'application/ocsp-request';
				case 'ors': return 'application/ocsp-response';
				case 'bin': return 'application/octet-stream';
				case 'oda': return 'application/oda';
				case 'ogg': return 'application/ogg';
				case 'pdf': return 'application/x-pdf';
				case '7bit': return 'application/pgp-keys';
				case 'sig': return 'application/pgp-signature';
				case 'p10': return 'application/pkcs10';
				case 'p7m': return 'application/pkcs7-mime';
				case 'p7s': return 'application/pkcs7-signature';
				case 'cer': return 'application/pkix-cert';
				case 'crl': return 'application/pkix-crl';
				case 'pkipath': return 'application/pkix-pkipath';
				case 'pki': return 'application/pkixcmp';
				case 'ps': return 'application/postscript';
				case 'shw': return 'application/presentations';
				case 'cw': return 'application/prs.cww';
				case 'rnd': return 'application/prs.nprend';
				case 'qrt': return 'application/quest';
				case 'rtf': return 'text/rtf';
				case 'soc': return 'application/sgml-open-catalog';
				case 'siv': return 'application/sieve';
				case 'smi': return 'application/smil';
				case 'tbk': return 'application/toolbook';
				case 'plb': return 'application/vnd.3gpp.pic-bw-large';
				case 'psb': return 'application/vnd.3gpp.pic-bw-small';
				case 'pvb': return 'application/vnd.3gpp.pic-bw-var';
				case 'sms': return 'application/vnd.3gpp.sms';
				case 'atc': return 'application/vnd.acucorp';
				case 'xfdf': return 'application/vnd.adobe.xfdf';
				case 'ami': return 'application/vnd.amiga.amu';
				case 'mpm': return 'application/vnd.blueice.multipass';
				case 'cdy': return 'application/vnd.cinderella';
				case 'cmc': return 'application/vnd.cosmocaller';
				case 'wbs': return 'application/vnd.criticaltools.wbs+xml';
				case 'curl': return 'application/vnd.curl';
				case 'rdz': return 'application/vnd.data-vision.rdz';
				case 'dfac': return 'application/vnd.dreamfactory';
				case 'fsc': return 'application/vnd.fsc.weblauch';
				case 'txd': return 'application/vnd.genomatix.tuxedo';
				case 'hbci': return 'application/vnd.hbci';
				case 'les': return 'application/vnd.hhe.lesson-player';
				case 'plt': return 'application/vnd.hp-hpgl';
				case 'emm': return 'application/vnd.ibm.electronic-media';
				case 'irm': return 'application/vnd.ibm.rights-management';
				case 'sc': return 'application/vnd.ibm.secure-container';
				case 'rcprofile': return 'application/vnd.ipunplugged.rcprofile';
				case 'irp': return 'application/vnd.irepository.package+xml';
				case 'jisp': return 'application/vnd.jisp';
				case 'karbon': return 'application/vnd.kde.karbon';
				case 'chrt': return 'application/vnd.kde.kchart';
				case 'kfo': return 'application/vnd.kde.kformula';
				case 'flw': return 'application/vnd.kde.kivio';
				case 'kon': return 'application/vnd.kde.kontour';
				case 'kpr': return 'application/vnd.kde.kpresenter';
				case 'ksp': return 'application/vnd.kde.kspread';
				case 'kwd': return 'application/vnd.kde.kword';
				case 'htke': return 'application/vnd.kenameapp';
				case 'kia': return 'application/vnd.kidspiration';
				case 'kne': return 'application/vnd.kinar';
				case 'lbd': return 'application/vnd.llamagraphics.life-balance.desktop';
				case 'lbe': return 'application/vnd.llamagraphics.life-balance.exchange+xml';
				case 'wks': return 'application/vnd.lotus-1-2-3';
				case 'mcd': return 'application/x-mathcad';
				case 'mfm': return 'application/vnd.mfmp';
				case 'flo': return 'application/vnd.micrografx.flo';
				case 'igx': return 'application/vnd.micrografx.igx';
				case 'mif': return 'application/x-mif';
				case 'mpn': return 'application/vnd.mophun.application';
				case 'mpc': return 'application/vnd.mophun.certificate';
				case 'xul': return 'application/vnd.mozilla.xul+xml';
				case 'cil': return 'application/vnd.ms-artgalry';
				case 'asf': return 'video/x-ms-asf';
				case 'lrm': return 'application/vnd.ms-lrm';
				case 'ppt': return 'application/vnd.ms-powerpoint';
				case 'mpp': return 'application/vnd.ms-project';
				case 'wpl': return 'application/vnd.ms-wpl';
				case 'mseq': return 'application/vnd.mseq';
				case 'ent': return 'application/vnd.nervana';
				case 'rpst': return 'application/vnd.nokia.radio-preset';
				case 'rpss': return 'application/vnd.nokia.radio-presets';
				case 'odt': return 'application/vnd.oasis.opendocument.text';
				case 'ott': return 'application/vnd.oasis.opendocument.text-template';
				case 'oth': return 'application/vnd.oasis.opendocument.text-web';
				case 'odm': return 'application/vnd.oasis.opendocument.text-master';
				case 'odg': return 'application/vnd.oasis.opendocument.graphics';
				case 'otg': return 'application/vnd.oasis.opendocument.graphics-template';
				case 'odp': return 'application/vnd.oasis.opendocument.presentation';
				case 'otp': return 'application/vnd.oasis.opendocument.presentation-template';
				case 'ods': return 'application/vnd.oasis.opendocument.spreadsheet';
				case 'ots': return 'application/vnd.oasis.opendocument.spreadsheet-template';
				case 'odc': return 'application/vnd.oasis.opendocument.chart';
				case 'odf': return 'application/vnd.oasis.opendocument.formula';
				case 'odb': return 'application/vnd.oasis.opendocument.database';
				case 'odi': return 'application/vnd.oasis.opendocument.image';
				case 'prc': return 'application/vnd.palm';
				case 'efif': return 'application/vnd.picsel';
				case 'pti': return 'application/vnd.pvi.ptid1';
				case 'qxd': return 'application/vnd.quark.quarkxpress';
				case 'sdoc': return 'application/vnd.sealed.doc';
				case 'seml': return 'application/vnd.sealed.eml';
				case 'smht': return 'application/vnd.sealed.mht';
				case 'sppt': return 'application/vnd.sealed.ppt';
				case 'sxls': return 'application/vnd.sealed.xls';
				case 'stml': return 'application/vnd.sealedmedia.softseal.html';
				case 'spdf': return 'application/vnd.sealedmedia.softseal.pdf';
				case 'see': return 'application/vnd.seemail';
				case 'mmf': return 'application/vnd.smaf';
				case 'sxc': return 'application/vnd.sun.xml.calc';
				case 'stc': return 'application/vnd.sun.xml.calc.template';
				case 'sxd': return 'application/vnd.sun.xml.draw';
				case 'std': return 'application/vnd.sun.xml.draw.template';
				case 'sxi': return 'application/vnd.sun.xml.impress';
				case 'sti': return 'application/vnd.sun.xml.impress.template';
				case 'sxm': return 'application/vnd.sun.xml.math';
				case 'sxw': return 'application/vnd.sun.xml.writer';
				case 'sxg': return 'application/vnd.sun.xml.writer.global';
				case 'stw': return 'application/vnd.sun.xml.writer.template';
				case 'sus': return 'application/vnd.sus-calendar';
				case 'vsc': return 'application/vnd.vidsoft.vidconference';
				case 'vsd': return 'application/vnd.visio';
				case 'vis': return 'application/vnd.visionary';
				case 'sic': return 'application/vnd.wap.sic';
				case 'slc': return 'application/vnd.wap.slc';
				case 'wbxml': return 'application/vnd.wap.wbxml';
				case 'wmlc': return 'application/vnd.wap.wmlc';
				case 'wmlsc': return 'application/vnd.wap.wmlscriptc';
				case 'wtb': return 'application/vnd.webturbo';
				case 'wpd': return 'application/vnd.wordperfect';
				case 'wqd': return 'application/vnd.wqd';
				case 'wv': return 'application/vnd.wv.csp+wbxml';
				case '8bit': return 'multipart/parallel';
				case 'hvd': return 'application/vnd.yamaha.hv-dic';
				case 'hvs': return 'application/vnd.yamaha.hv-script';
				case 'hvp': return 'application/vnd.yamaha.hv-voice';
				case 'saf': return 'application/vnd.yamaha.smaf-audio';
				case 'spf': return 'application/vnd.yamaha.smaf-phrase';
				case 'vmd': return 'application/vocaltec-media-desc';
				case 'vmf': return 'application/vocaltec-media-file';
				case 'vtk': return 'application/vocaltec-talker';
				case 'wif': return 'image/cewavelet';
				case 'wp5': return 'application/wordperfect5.1';
				case 'wk': return 'application/x-123';
				case '7ls': return 'application/x-7th_level_event';
				case 'aab': return 'application/x-authorware-bin';
				case 'aam': return 'application/x-authorware-map';
				case 'aas': return 'application/x-authorware-seg';
				case 'bcpio': return 'application/x-bcpio';
				case 'bleep': return 'application/x-bleeper';
				case 'bz2': return 'application/x-bzip2';
				case 'vcd': return 'application/x-cdlink';
				case 'chat': return 'application/x-chat';
				case 'pgn': return 'application/x-chess-pgn';
				case 'z': return 'application/x-compress';
				case 'cpio': return 'application/x-cpio';
				case 'pqf': return 'application/x-cprplayer';
				case 'csh': return 'application/x-csh';
				case 'csm': return 'chemical/x-csml';
				case 'co': return 'application/x-cult3d-object';
				case 'deb': return 'application/x-debian-package';
				case 'dcr': return 'application/x-director';
				case 'dvi': return 'application/x-dvi';
				case 'evy': return 'application/x-envoy';
				case 'gtar': return 'application/x-gtar';
				case 'gz': return 'application/x-gzip';
				case 'hdf': return 'application/x-hdf';
				case 'hep': return 'application/x-hep';
				case 'rhtml': return 'application/x-html+ruby';
				case 'mv': return 'application/x-httpd-miva';
				case 'phtml': return 'application/x-httpd-php';
				case 'ica': return 'application/x-ica';
				case 'imagemap': return 'application/x-imagemap';
				case 'ipx': return 'application/x-ipix';
				case 'ips': return 'application/x-ipscript';
				case 'jar': return 'application/x-java-archive';
				case 'jnlp': return 'application/x-java-jnlp-file';
				case 'ser': return 'application/x-java-serialized-object';
				case 'class': return 'application/x-java-vm';
				case 'skp': return 'application/x-koan';
				case 'latex': return 'application/x-latex';
				case 'frm': return 'application/x-maker';
				case 'mid': return 'audio/x-midi';
				case 'mda': return 'application/x-msaccess';
				case 'com': return 'application/x-msdos-program';
				case 'nc': return 'application/x-netcdf';
				case 'pac': return 'application/x-ns-proxy-autoconfig';
				case 'pm5': return 'application/x-pagemaker';
				case 'pl': return 'application/x-perl';
				case 'rp': return 'application/x-pn-realmedia';
				case 'py': return 'application/x-python';
				case 'qtl': return 'application/x-quicktimeplayer';
				case 'rar': return 'application/x-rar-compressed';
				case 'rb': return 'application/x-ruby';
				case 'sh': return 'application/x-sh';
				case 'shar': return 'application/x-shar';
				case 'swf': return 'application/x-shockwave-flash';
				case 'spr': return 'application/x-sprite';
				case 'sav': return 'application/x-spss';
				case 'spt': return 'application/x-spt';
				case 'sit': return 'application/x-stuffit';
				case 'sv4cpio': return 'application/x-sv4cpio';
				case 'sv4crc': return 'application/x-sv4crc';
				case 'tar': return 'application/x-tar';
				case 'tcl': return 'application/x-tcl';
				case 'tex': return 'application/x-tex';
				case 'texinfo': return 'application/x-texinfo';
				case 't': return 'application/x-troff';
				case 'man': return 'application/x-troff-man';
				case 'me': return 'application/x-troff-me';
				case 'ms': return 'application/x-troff-ms';
				case 'vqf': return 'application/x-twinvq';
				case 'vqe': return 'application/x-twinvq-plugin';
				case 'ustar': return 'application/x-ustar';
				case 'bck': return 'application/x-vmsbackup';
				case 'src': return 'application/x-wais-source';
				case 'wz': return 'application/x-wingz';
				case 'wp6': return 'application/x-wordperfect6.1';
				case 'crt': return 'application/x-x509-ca-cert';
				case 'zip': return 'application/zip';
				case 'xhtml': return 'application/xhtml+xml';
				case '3gpp': return 'audio/3gpp';
				case 'amr': return 'audio/amr';
				case 'awb': return 'audio/amr-wb';
				case 'au': return 'audio/basic';
				case 'evc': return 'audio/evrc';
				case 'l16': return 'audio/l16';
				case 'mp3': return 'audio/mpeg';
				case 'sid': return 'audio/prs.sid';
				case 'qcp': return 'audio/qcelp';
				case 'smv': return 'audio/smv';
				case 'koz': return 'audio/vnd.audiokoz';
				case 'eol': return 'audio/vnd.digital-winds';
				case 'plj': return 'audio/vnd.everad.plj';
				case 'lvp': return 'audio/vnd.lucent.voice';
				case 'mxmf': return 'audio/vnd.nokia.mobile-xmf';
				case 'vbk': return 'audio/vnd.nortel.vbk';
				case 'ecelp4800': return 'audio/vnd.nuera.ecelp4800';
				case 'ecelp7470': return 'audio/vnd.nuera.ecelp7470';
				case 'ecelp9600': return 'audio/vnd.nuera.ecelp9600';
				case 'smp3': return 'audio/vnd.sealedmedia.softseal.mpeg';
				case 'vox': return 'audio/voxware';
				case 'aif': return 'audio/x-aiff';
				case 'mp2': return 'audio/x-mpeg';
				case 'mpu': return 'audio/x-mpegurl';
				case 'rm': return 'audio/x-pn-realaudio';
				case 'rpm': return 'audio/x-pn-realaudio-plugin';
				case 'ra': return 'audio/x-realaudio';
				case 'wav': return 'audio/x-wav';
				case 'emb': return 'chemical/x-embl-dl-nucleotide';
				case 'cube': return 'chemical/x-gaussian-cube';
				case 'gau': return 'chemical/x-gaussian-input';
				case 'jdx': return 'chemical/x-jcamp-dx';
				case 'mol': return 'chemical/x-mdl-molfile';
				case 'rxn': return 'chemical/x-mdl-rxnfile';
				case 'tgf': return 'chemical/x-mdl-tgf';
				case 'mop': return 'chemical/x-mopac-input';
				case 'pdb': return 'x-chemical/x-pdb';
				case 'scr': return 'chemical/x-rasmol';
				case 'xyz': return 'x-chemical/x-xyz';
				case 'dwf': return 'x-drawing/dwf';
				case 'ivr': return 'i-world/i-vrml';
				case 'bmp': return 'image/x-bmp';
				case 'cod': return 'image/cis-cod';
				case 'fif': return 'image/fif';
				case 'gif': return 'image/gif';
				case 'ief': return 'image/ief';
				case 'jp2': return 'image/jp2';
				case 'jpg': return 'image/pjpeg';
				case 'jpm': return 'image/jpm';
				case 'jpf': return 'image/jpx';
				case 'pic': return 'image/pict';
				case 'png': return 'image/x-png';
				case 'tga': return 'image/targa';
				case 'tif': return 'image/tiff';
				case 'tiff': return 'image/tiff';
				case 'svf': return 'image/vn-svf';
				case 'dgn': return 'image/vnd.dgn';
				case 'djvu': return 'image/vnd.djvu';
				case 'dwg': return 'image/vnd.dwg';
				case 'pgb': return 'image/vnd.glocalgraphics.pgb';
				case 'ico': return 'image/vnd.microsoft.icon';
				case 'mdi': return 'image/vnd.ms-modi';
				case 'spng': return 'image/vnd.sealed.png';
				case 'sgif': return 'image/vnd.sealedmedia.softseal.gif';
				case 'sjpg': return 'image/vnd.sealedmedia.softseal.jpg';
				case 'wbmp': return 'image/vnd.wap.wbmp';
				case 'ras': return 'image/x-cmu-raster';
				case 'fh4': return 'image/x-freehand';
				case 'pnm': return 'image/x-portable-anymap';
				case 'pbm': return 'image/x-portable-bitmap';
				case 'pgm': return 'image/x-portable-graymap';
				case 'ppm': return 'image/x-portable-pixmap';
				case 'rgb': return 'image/x-rgb';
				case 'xbm': return 'image/x-xbitmap';
				case 'xpm': return 'image/x-xpixmap';
				case 'xwd': return 'image/x-xwindowdump';
				case 'igs': return 'model/iges';
				case 'msh': return 'model/mesh';
				case 'x_b': return 'model/vnd.parasolid.transmit.binary';
				case 'x_t': return 'model/vnd.parasolid.transmit.text';
				case 'wrl': return 'x-world/x-vrml';
				case 'csv': return 'text/comma-separated-values';
				case 'css': return 'text/css';
				case 'html': return 'text/html';
				case 'txt': return 'text/plain';
				case 'rst': return 'text/prs.fallenstein.rst';
				case 'rtx': return 'text/richtext';
				case 'sgml': return 'text/x-sgml';
				case 'tsv': return 'text/tab-separated-values';
				case 'ccc': return 'text/vnd.net2phone.commcenter.command';
				case 'jad': return 'text/vnd.sun.j2me.app-descriptor';
				case 'si': return 'text/vnd.wap.si';
				case 'sl': return 'text/vnd.wap.sl';
				case 'wml': return 'text/vnd.wap.wml';
				case 'wmls': return 'text/vnd.wap.wmlscript';
				case 'hdml': return 'text/x-hdml';
				case 'etx': return 'text/x-setext';
				case 'talk': return 'text/x-speech';
				case 'vcs': return 'text/x-vcalendar';
				case 'vcf': return 'text/x-vcard';
				case 'xml': return 'text/xml';
				case 'uvr': return 'ulead/vrml';
				case '3gp': return 'video/3gpp';
				case 'dl': return 'video/dl';
				case 'gl': return 'video/gl';
				case 'mj2': return 'video/mj2';
				case 'mpeg': return 'video/mpeg';
				case 'mov': return 'video/quicktime';
				case 'vdo': return 'video/vdo';
				case 'viv': return 'video/vivo';
				case 'fvt': return 'video/vnd.fvt';
				case 'mxu': return 'video/vnd.mpegurl';
				case 'nim': return 'video/vnd.nokia.interleaved-multimedia';
				case 'mp4': return 'video/vnd.objectvideo';
				case 's11': return 'video/vnd.sealed.mpeg1';
				case 'smpg': return 'video/vnd.sealed.mpeg4';
				case 'sswf': return 'video/vnd.sealed.swf';
				case 'smov': return 'video/vnd.sealedmedia.softseal.mov';
				case 'vivo': return 'video/vnd.vivo';
				case 'fli': return 'video/x-fli';
				case 'wmv': return 'video/x-ms-wmv';
				case 'avi': return 'video/x-msvideo';
				case 'movie': return 'video/x-sgi-movie';
				case 'ice': return 'x-conference/x-cooltalk';
				case 'd': return 'x-world/x-d96';
				case 'svr': return 'x-world/x-svr';
				case 'vrw': return 'x-world/x-vream';
				default:
					return $defaultType;
			}
		}

		public function adddatetimepicker_old( $type = 'datetime' )
		{
			phpgwapi_jquery::load_widget('datepicker');
			if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
			{
				$theme = 'humanity';
			}
			else
			{
				$theme = 'ui-lightness';
			}
			$theme = 'redmond';
			$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/{$theme}/jquery-ui.min.css");

			switch ($type)
			{
				case 'datetime':
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/jquery-ui-timepicker-addon.css");
					$GLOBALS['phpgw']->js->validate_file('jquery', 'js/jquery-ui-timepicker-addon.min');
					$_type = 'datetime';
					break;
				case 'time':
					$GLOBALS['phpgw']->css->add_external_file("phpgwapi/js/jquery/css/jquery-ui-timepicker-addon.css");
					$GLOBALS['phpgw']->js->validate_file('jquery', 'js/jquery-ui-timepicker-addon.min');
					$_type = 'time';
					break;
				default:
					$_type = 'date';
			}

			$img_cal = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');
			$dateformat = str_ireplace(array('d', 'm', 'y'), array('dd', 'mm', 'yy'), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$lang_select_date = lang('select date');
			$lang_from = lang('from');
			$lang_to = lang('to');
			$lang_remove = lang('remove date');

			if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
			{
				$html = 'var html = "<div class=\'date-container\'>"+
                            "<a class=\'close-btn btnclose\' href=\'javascript:void(0);\'>' . $lang_remove . '</a>"+
                            "<dt><label for=\'new_start_date_"+this.counter+"\'>' . $lang_from . '</label></dt>"+
                            "<dd><input class=\'new_datepicker time\' readonly=\'readonly\' name=\'from_[]\' id=\'new_start_date_"+this.counter+"\' type=\'text\'>"+
                            "</input></dd>"+
                            "<dt><label for=\'new_end_date_"+this.counter+"\' >' . $lang_to . '</label></dt>"+
                            "<dd><input class=\'new_datepicker time\' readonly=\'readonly\' name=\'to_[]\' id=\'new_end_date_"+this.counter+"\' type=\'text\'>"+
                            "</input></dd>"+
                        "</div>"';
			}
			else
			{
				$html = 'var html = "<div class=\'date-container\'>"+
						"<a class=\'close-btn btnclose\' href=\'javascript:void(0);\'>-</a>"+
						"<div class=\'pure-control-group\'>"+
							"<label for=\'new_start_date_"+this.counter+"\'><h4>' . $lang_from . '</h4></label>"+
							"<input class=\'new_datepicker time pure-input-2-3\' readonly=\'readonly\' name=\'from_[]\' id=\'new_start_date_"+this.counter+"\' type=\'text\'>"+
							"</input>"+
						"</div>"+
						"<div class=\'pure-control-group\'>"+
							"<label for=\'new_end_date_"+this.counter+"\' ><h4>' . $lang_to . '</h4></label>"+
							"<input class=\'new_datepicker time pure-input-2-3\' readonly=\'readonly\' name=\'to_[]\' id=\'new_end_date_"+this.counter+"\' type=\'text\'>"+
							"</input>"+
						"</div>"+
				 	"</div>"';
			}

//            echo $html;
//            exit();


			$js = <<<JS

			$(function() {

					$.each($('.newaddedpicker'), function(i, v){
					var id = v.id;
					v.classList.remove('newaddedpicker');
					$( "#"+id ).{$type}picker({
						dateFormat: '{$dateformat}',
						showWeek: true,
						changeMonth: true,
						changeYear: true,
						showOn: "button",
						showButtonPanel:true,
						buttonImage: "{$img_cal}",
						buttonText: "{$lang_select_date}",
						buttonImageOnly: true
					});
				});

				$('#add-date-link').click(function(){
					var add = $(this);
					var html = '';

					this.counter = $('.date-container').length - 1;

					if (!this.counter) { this.counter = 0; }

                    {$html}

					add.parent().parent().children('#dates-container').append(html);

					$( ".new_datepicker" ).{$_type}picker({
						dateFormat: '{$dateformat}',
						showWeek: true,
						changeMonth: true,
						changeYear: true,
						showOn: "button",
						showButtonPanel:true,
						buttonImage: "{$img_cal}",
						buttonText: "{$lang_select_date}",
						buttonImageOnly: true
					});
					this.counter++;
				});
			});

			$(document).on("click",".btnclose",function(){
				var the = $(this);
				RemoveDate(the);
			});

			RemoveDate = function(the){
				the.parent().remove();
			}

JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
		}

		public function adddatetimepicker( $type = 'datetime' )
		{
			phpgwapi_jquery::load_widget('datetimepicker');

			$dateformat = str_ireplace(array('y'), array('Y'), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

			switch($type)
			{
				case 'datetime':
					$_type = 'datetime';
					$dateformat .= " H:i";
					break;
				case 'time':
					$_type	 = 'time';
					$dateformat = "H:i";
					break;
				default:
					$_type = 'date';
			}

			$img_cal = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');

			$userlang = 'en';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']) )
			{
				$userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}

			$lang_select_date = lang('select date');
			$lang_from = lang('from');
			$lang_to = lang('to');
			$lang_remove = lang('remove date');

			$placeholder = str_ireplace(array('Y','m', 'd', 'H', 'i'), array('YYYY', 'MM', 'DD', 'HH', 'mm' ),$dateformat);

			if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'bookingfrontend')
			{
				$html = 'var html = "<div class=\'date-container\'>"+
                            "<a class=\'btnclose\' href=\'javascript:void(0);\'>' . $lang_remove . '</a>"+
                            "<div class=\'form-group\'><label for=\'new_start_date_"+this.counter+"\'>' . $lang_from . '</label>"+
                            "<input class=\'new_datepicker time form-control\'  name=\'from_[]\' id=\'new_start_date_"+this.counter+"\' type=\'text\' placeholder =\'' . $placeholder . '\'>"+
                            "</input></div>"+
                            "<div class=\'form-group\'><label for=\'new_end_date_"+this.counter+"\' >' . $lang_to . '</label>"+
                            "<input class=\'new_datepicker time form-control\'  name=\'to_[]\' id=\'new_end_date_"+this.counter+"\' type=\'text\' placeholder =\'' . $placeholder . '\'>"+
                            "</input></div>"+
                        "</div>"';
			}
			else
			{
				$html = 'var html = "<div class=\'date-container\'>"+
						"<a class=\'close-btn btnclose\' href=\'javascript:void(0);\'></a>"+
						"<div class=\'pure-control-group\'>"+
							"<label for=\'new_start_date_"+this.counter+"\'><h4>' . $lang_from . '</h4></label>"+
							"<input class=\'new_datepicker time pure-input-2-3\'  name=\'from_[]\' id=\'new_start_date_"+this.counter+"\' type=\'text\' placeholder =\'' . $placeholder . '\'>"+
							"</input>"+
						"</div>"+
						"<div class=\'pure-control-group\'>"+
							"<label for=\'new_end_date_"+this.counter+"\' ><h4>' . $lang_to . '</h4></label>"+
							"<input class=\'new_datepicker time pure-input-2-3\'  name=\'to_[]\' id=\'new_end_date_"+this.counter+"\' type=\'text\' placeholder =\'' . $placeholder . '\'>"+
							"</input>"+
						"</div>"+
				 	"</div>"';
			}

//            echo $html;
//            exit();

			$datepicker = $_type == 'time' ? false : true;
			$timepicker = $_type == 'date' ? false : true;

			$js = <<<JS

			$(function() {

				var logic = function( currentDateTime ){
						console.log(currentDateTime);

				};

				$.each($('.newaddedpicker'), function(i, v)
				{
					var id = v.id;
					v.classList.remove('newaddedpicker');
	
					$( "#"+id ).datetimepicker({
						format: '{$dateformat}',
						datepicker:{$datepicker},
						timepicker: {$timepicker},
						step: 15,
						weeks: true,
						dayOfWeekStart:1,
						minDate:0,
//						onChangeDateTime:logic
					});
				});

				$('#add-date-link').click(function(){
					var add = $(this);
					var html = '';

					this.counter = $('.date-container').length - 1;

					if (!this.counter) { this.counter = 0; }

                    {$html}

					add.parent().parent().children('#dates-container').append(html);

					$( ".new_datepicker" ).datetimepicker(
					{
						format: '{$dateformat}',
						datepicker:{$datepicker},
						timepicker: {$timepicker},
						step: 15,
						weeks: true,
						dayOfWeekStart:1,
						minDate:0,
						onChangeDateTime:logic
					});
					this.counter++;
				});
			});

			$(document).on("click",".btnclose",function(){
				var the = $(this);
				RemoveDate(the);
			});

			RemoveDate = function(the){
				the.parent().remove();
			}

JS;
			$GLOBALS['phpgw']->js->add_code('', $js);
		}
	}