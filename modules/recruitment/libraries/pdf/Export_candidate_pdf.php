<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once APPPATH . 'libraries/pdf/App_pdf.php';

/**
 *  Export_candidate pdf
 */
class Export_candidate_pdf extends App_pdf {
	protected $export_candidate;

	/**
	 * construct
	 * @param object
	 */
	public function __construct($export_candidate) {

		$export_candidate = hooks()->apply_filters('request_html_pdf_data', $export_candidate);
		$GLOBALS['export_candidate_pdf'] = $export_candidate;

		parent::__construct();

		$this->export_candidate = $export_candidate;

		$this->SetTitle('export_candidate');

		# Don't remove these lines - important for the PDF layout
		$this->export_candidate = $this->fix_editor_html($this->export_candidate);
	}

	/**
	 * prepare
	 * @return
	 */
	public function prepare() {
		$this->set_view_vars('export_candidate', $this->export_candidate);

		return $this->build();
	}

	/**
	 * type
	 * @return
	 */
	protected function type() {
		return 'export_candidate';
	}

	/**
	 * file path
	 * @return
	 */
	protected function file_path() {
		$customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
		$actualPath = APP_MODULES_PATH . '/recruitment/views/export_candidate_pdf.php';

		if (file_exists($customPath)) {
			$actualPath = $customPath;
		}

		return $actualPath;
	}
}