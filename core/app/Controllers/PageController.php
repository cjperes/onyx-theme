<?php
/**
 * Page Controller
 *
 * @package Onyx Theme
 */

namespace Onyx\Controllers;

use Onyx\Controller;

class PageController extends Controller {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->context['post'] = $this->get_post();

		$this->set_page_templates( $this->context['post'], 'page' );
		$this->render_view();
	}

}