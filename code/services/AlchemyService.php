<?php
/**
 * A service that uses the AlchemyAPI to retrieve information about content
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class AlchemyService {

	/**
	 * How many characters should content have before it is indexed?
	 *
	 * @var int
	 */
	public static $char_limit = 80;

	/**
	 * How many keywords should we use?
	 *
	 * @var int
	 */
	public static $max_keywords = 15;

	public static $config = array(
		'api_url'  => 'http://access.alchemyapi.com',
		'api_path' => '/calls/text/',
		'api_key'  => '',
	);

	public static function set_api_key($key) {
		self::$config['api_key'] = $key;
	}

	/**
	 * @var RestfulService
	 */
	protected $api;

	public function __construct() {
		$this->api = new RestfulService(Controller::join_links(
			self::$config['api_url'], self::$config['api_path']
		));
	}

	/**
	 * Updates a content item with data from the Alchemy API.
	 *
	 * @param DataObject $object
	 */
	public function alchemise(DataObject $object) {
		$content = '';

		if (!$fields = $object->stat('extraction_fields')) {
			$fields = array('Title', 'Content');
		}

		foreach ($fields as $field) if ($object->hasField($field)) {
			$content .= strip_tags($object->obj($field)->forTemplate()) . ' ';
		}

		if (strlen($content) < self::$char_limit) {
			return;
		}

		$this->api->setQueryString(array(
			'apikey' => self::$config['api_key'],
			'text'   => $content
		));

		$entities = $this->api->request('TextGetRankedNamedEntities');

		if (!$entities->isError()) {
			// First clear all existing entity data.
			foreach (array_keys(Alchemisable::entity_fields()) as $field) {
				$object->$field = array();
			}

			foreach ($entities->simpleXML()->entities->entity as $entity) {
				$name      = "Alc{$entity->type}";
				$relevance = $entity->relevance;

				if ($relevance > .3) {
					$values = $object->$name->getValues();
					$values[] = (string) $entity->text;
					$object->$name = $values;
				}
			}
		}

		$keywords = $this->api->request('TextGetRankedKeywords');
		$total    = 0;

		if (!$keywords->isError()) {
			$extracted = array();

			foreach ($keywords->simpleXML()->keywords->keyword as $keyword) {
				if ($total++ < self::$max_keywords) {
					$extracted[] = (string) $keyword->text;
				}
			}

			$object->AlcKeywords = $extracted;
		}
	}

}
