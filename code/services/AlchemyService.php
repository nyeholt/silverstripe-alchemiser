<?php
/**
 * A service that uses the AlchemyAPI to retrieve information about content
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class AlchemyService {

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
	 * @return string
	 */
	public function getCategoryFor($text) {
		$this->api->setQueryString(array(
			'apikey' => self::$config['api_key'],
			'text'   => $text
		));

		$category = $this->api->request('TextGetCategory');

		if (!$category->isError()) {
			return (string) $category->simpleXML()->category;
		}
	}

	/**
	 * @return array[]
	 */
	public function getEntitiesFor($text) {
		$this->api->setQueryString(array(
			'apikey' => self::$config['api_key'],
			'text'   => $text
		));

		$request = $this->api->request('TextGetRankedNamedEntities');

		if (!$request->isError()) {
			$entities = array();

			foreach ($request->simpleXML()->entities->entity as $entity) {
				if ($entity->relevance > .3) {
					$type = (string) $entity->type;
					$text = (string) $entity->text;

					if (!array_key_exists($type, $entities)) {
						$entities[$type] = array();
					}

					$entities[$type][] = $text;
				}
			}

			return $entities;
		} else {
			return array();
		}
	}

	/**
	 * @return array
	 */
	public function getKeywordsFor($text) {
		$this->api->setQueryString(array(
			'apikey' => self::$config['api_key'],
			'text'   => $text
		));

		$keywords = $this->api->request('TextGetRankedKeywords');
		$total    = 0;

		if (!$keywords->isError()) {
			$extracted = array();

			foreach ($keywords->simpleXML()->keywords->keyword as $keyword) {
				if ($total++ < self::$max_keywords) {
					$extracted[] = (string) $keyword->text;
				}
			}

			return $extracted;
		} else {
			return array();
		}
	}

}
