<?php
namespace GatherContent\Importer;

class General extends Base {

	protected static $single_instance = null;

	/**
	 * GatherContent\Importer\API instance
	 *
	 * @var GatherContent\Importer\API
	 */
	protected $api;

	/**
	 * GatherContent\Importer\Admin instance
	 *
	 * @var GatherContent\Importer\Admin
	 */
	protected $admin;

	/**
	 * GatherContent\Importer\importer Sync\Pull instance
	 *
	 * @var GatherContent\Importer\importer Sync\Pull
	 */
	protected $pull;

	/**
	 * GatherContent\Importer\importer Sync\Push instance
	 *
	 * @var GatherContent\Importer\importer Sync\Push
	 */
	protected $push;

	/**
	 * GatherContent\Importer\Select2_Ajax_Handler instance
	 *
	 * @var GatherContent\Importer\Select2_Ajax_Handler
	 */
	protected $ajax_handler;

	/**
	 * GatherContent\Importer\Admin\Bulk instance
	 *
	 * @var GatherContent\Importer\Admin\Bulk
	 */
	protected $bulk_ui;

	/**
	 * GatherContent\Importer\Admin\Single instance
	 *
	 * @var GatherContent\Importer\Admin\Single
	 */
	protected $single_ui;

	const OPTION_NAME = 'gathercontent_importer';

	/**
	 * Creates or returns an instance of this class.
	 * @since  3.0.0
	 * @return General A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	protected function __construct() {
		parent::__construct( $_GET, $_POST );
		new Utils;

		$this->api   = new API( _wp_http_get_object() );
		$this->admin = new Admin\Admin( $this->api );
		$this->pull = new Sync\Pull( $this->api );
		$this->push = new Sync\Push( $this->api );
		$this->ajax_handler = new Admin\Ajax\Handlers( $this->api );
		if ( isset( $this->admin->mapping_wizzard->mappings ) ) {
			$this->bulk_ui = new Admin\Bulk(
				$this->api,
				$this->admin->mapping_wizzard->mappings
			);
			$this->single_ui = new Admin\Single(
				$this->api,
				$this->admin->mapping_wizzard->mappings
			);
		}
	}

	public function init_hooks() {
		$this->admin->init_hooks();
		$this->pull->init_hooks();
		$this->push->init_hooks();
		$this->ajax_handler->init_hooks();
		if ( $this->bulk_ui ) {
			$this->bulk_ui->init_hooks();
			$this->single_ui->init_hooks();
		}
	}

	/**
	 * Magic getter for our object, to make protected properties accessible.
	 * @param string $field
	 * @return mixed
	 */
	public function __get( $field ) {
		return $this->{$field};
	}

}

