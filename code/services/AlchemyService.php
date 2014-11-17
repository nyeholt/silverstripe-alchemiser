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
	public $charLimit = 80;

	
	public $maxKeywords = 15;
	
	/**
	 * How many keywords should we use?
	 *
	 * @var int
	 */
	public static $max_keywords = 15;

//	public static $config = array(
//		'api_url'  => 'http://access.alchemyapi.com',
//		'api_path' => '/calls/text/',
//		'api_key'  => '',
//	);

	/**
	 * @var AlchemyAPI
	 */
	public $alchemy;

	public function __construct() {
		
	}

	/**
	 * Automatically extracts and replaces the category, keywords and entities
	 * for a data object.
	 *
	 * @param DataObject $object
	 */
	public function alchemise(DataObject $object) {
		if (!$object->hasExtension('Alchemisable')) {
			throw new Exception('The object must have the Alchemisable extension.');
		}

		$text = $object->getContentForAlchemy();

		if (strlen($text) < $this->charLimit) {
			return;
		}

		$alchemyInfo = $object->AlchemyMetadata->getValues();
		if (!$alchemyInfo) {
			$alchemyInfo = array();
		}
		
		$cat = $this->getCategoryFor($text);
		$keywords = $this->getKeywordsFor($text);
		$entities = $this->getEntitiesFor($text);
		
		$alchemyInfo['Category'] = $cat;
		$alchemyInfo['Keywords'] = $keywords;
		$alchemyInfo['Entities'] = $entities;

		$object->AlchemyMetadata = $alchemyInfo;


//		foreach (Alchemisable::entity_fields() as $field => $name) {
//			$name = substr($field, 3);
//
//			if (array_key_exists($name, $entities)) {
//				$object->$field = $entities[$name];
//			} else {
//				$object->$field = array();
//			}
//		}
	}

	/**
	 * @return string
	 */
	public function getCategoryFor($text) {
		$info = $this->alchemy->category('text', $text);
		if (isset($info['category'])) {
			return $info['category'];
		}
	}

	/**
	 * @return array[]
	 */
	public function getEntitiesFor($text) {
		
		$entities = $this->alchemy->entities('text', $text); 
		
		$items = array();
		
		$words = array();

		if (isset($entities['entities'])) {
			foreach ($entities['entities'] as $entity) {
				if ($entity['relevance'] > 0.3) {
					$type = (string) $entity['type'];
					$text = (string) $entity['text'];

					if (!array_key_exists($type, $items)) {
						$items[$type] = array();
					}

					$items[$type][] = $text;
					$words[] = $text;
				}
			}
		}
		
		return $words;
		
//		$this->api->setQueryString(array(
//			'apikey' => self::$config['api_key'],
//			'text'   => $text
//		));
//
//		$request = $this->api->request('TextGetRankedNamedEntities');
//
//		if (!$request->isError()) {
//			$entities = array();
//
//			foreach ($request->simpleXML()->entities->entity as $entity) {
//				if ($entity->relevance > .3) {
//					$type = (string) $entity->type;
//					$text = (string) $entity->text;
//
//					if (!array_key_exists($type, $entities)) {
//						$entities[$type] = array();
//					}
//
//					$entities[$type][] = $text;
//				}
//			}
//
//			return $entities;
//		} else {
//			return array();
//		}
	}

	/**
	 * @return array
	 */
	public function getKeywordsFor($text) {
		$keywords = $this->alchemy->keywords('text', $text);
		
		$words = array();
		
		if (isset($keywords['keywords'])) {
			$total = 0;
			foreach ($keywords['keywords'] as $keyword) {
				if ($total++ < $this->maxKeywords) {
					$words[] = (string) $keyword['text'];
				}
			}
		}
		
		return $words;
	}
	
	public function getConceptsFor($text) {
		$concepts = $this->alchemy->concepts('text', $text);
		$words = array();
		if (isset($concepts['concepts'])) {
			$total = 0;
			foreach ($concepts['concepts'] as $keyword) {
				if ($total++ < $this->maxKeywords) {
					$words[] = (string) $keyword['text'];
				}
			}
		}
		return $words;
	}
	
	public function getTaxonomyFor($text) {
		$taxonomy = $this->alchemy->taxonomy('text', $text);
		
		$words = array();
		if (isset($taxonomy['taxonomy'])) {
			$total = 0;
			foreach ($taxonomy['taxonomy'] as $keyword) {
				$words[] = (string) $keyword['label'];
			}
		}
		return $words;
	}
}
