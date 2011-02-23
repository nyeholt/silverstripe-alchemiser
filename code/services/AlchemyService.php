<?php
/*

Copyright (c) 2009, SilverStripe Australia PTY LTD - www.silverstripe.com.au
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of SilverStripe nor the names of its contributors may be used to endorse or promote products derived from this software
      without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
OF SUCH DAMAGE.
*/

/**
 * A service that uses the AlchemyAPI to retrieve information about content
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class AlchemyService
{
    protected $api;

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
		'api_url' => 'http://access.alchemyapi.com',
		'api_key' => '',
	);

	public static function set_api_key($key) {
		self::$config['api_key'] = $key;
	}

	public function  __construct() {
		$this->api = new WebApiClient(self::$config['api_url']);
		$this->api->setMethods(self::$methods);
		$this->api->setGlobalParam('apikey', self::$config['api_key']);
		$this->api->setGlobalParam('outputMode', 'json');
	}

	/**
	 * Updates a content item with data found by alchemy.
	 *
	 * Note that this does NOT save the object once finished. Callers must handle that themselves
	 *
	 * @param SiteTree $object
	 */
	public function alchemise($object, $force=false) {
		
		$fields = $object->stat('extraction_fields');
		if (!$fields) {
			$fields = array('Title', 'Content');
		}
		$content = '';
		foreach ($fields as $name) {
			if ($object->hasField($name) && ($force || $object->isChanged($name))) {
				$content .= ' ' . $object->$name;
			}
		}

		// clean it up a bit - might need to be a bit more forceful with this. 
		$content = strip_tags($content);

		// Need to have a reasonable amount of content change before indexing...
		if (strlen($content) < self::$char_limit) {
			return;
		}

		$result = null;
		try {
			$result = $this->getEntities($content);
		} catch (Exception $e) {
			SS_Log::log($e, SS_Log::ERR);
		}

		if ($result && $result->status == 'OK' && isset($result->entities) && count($result->entities)) {
			foreach ($result->entities as $entity) {
				$field = 'Alc'.$entity->type;
				if (!$object->hasField($field)) {
					singleton('AlcUtils')->log("Alchemy returned field $field but it was not available on object $object->ID", SS_Log::WARN);
					continue;
				}
				// make sure the field is empty because we're adding new data in
				$object->$field = array();
			}

			foreach ($result->entities as $entity) {
				$field = 'Alc'.$entity->type;
				$relevance = $entity->relevance;
				if ($relevance > 0.3) {
					if (!$object->hasField($field)) {
						singleton('AlcUtils')->log("Alchemy returned field $field but it was not available on object $object->ID", SS_Log::WARN);
						continue;
					}
					$cur = $object->$field->getValues();
					$cur[] = $entity->text;
					$object->$field = $cur;
				}
			}
		}

		try {
			usleep(500);
			$result = $this->getKeywords($content);
		} catch (Exception $e) {
			SS_Log::log($e, SS_Log::ERR);
		}

		if ($result && $result->status == 'OK' && isset($result->keywords) && count($result->keywords)) {
			$keywords = array();
			 
			for ($i = 0, $c = count($result->keywords); $i < self::$max_keywords; $i++) {
				$keyword = $result->keywords[$i];
				$keywords[] = $keyword->text;
			}
			$object->AlcKeywords = $keywords;
		} else {
			singleton('AlcUtils')->log("There was an error getting keywords for $object->ID, result is ".var_export($result, true), SS_Log::ERR);
		}
	}

	public function getEntities($content) {
		return $this->call('getEntities', array('text' => $content));
	}

	public function getKeywords($content) {
		return $this->call('getKeywords', array('text' => $content));
	}

	public function call($method, $args = null) {
		return $this->api->callMethod($method, $args);
	}


	public static $methods = array(
		'getEntities' => array(
			'method' => 'POST',
			'url' => '/calls/text/TextGetRankedNamedEntities',
			'params' => array('text'),
			'cache' => 600,
			'return' => 'json'
			// 'enctype' => Zend_Http_Client::ENC_FORMDATA,
		),
		'getCategory' => array(
			'method' => 'POST',
			'url' => '/calls/text/TextGetCategory',
			'params' => array('text'),
			'cache' => 600,
			'return' => 'json'
		),
		
		'getKeywords' => array(
			'method' => 'POST',
			'url' => '/calls/text/TextGetRankedKeywords',
			'params' => array('text'),
			'cache' => 600,
			'return' => 'json'
		),
	);
}
?>