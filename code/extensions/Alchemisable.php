<?php
/**
 * Attach this to an object and its content will be
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class Alchemisable extends DataExtension {
	
	private static $db = array(
		'AlchemyMetadata'		=> 'MultiValueField',
	);
	
	private static $stored_metadata = array(
		'Keywords'	=> array(),
		'Category'	=> '',
		'Concepts'	=> array(),
		'Entities'	=> array(),
		'Taxonomy'	=> array()
	);
	
	/**
	 * @var bool
	 */
	public $automatic = false;

	/**
	 * Returns a map of all the Alchemy entity DB fields to human-readable names.
	 *
	 * @return array
	 */
	public static function entity_fields() {
		return array(
			'AlcAutomobile'           => 'Automobiles',
			'AlcAnniversary'          => 'Anniversaries',
			'AlcCity'                 => 'Cities',
			'AlcCompany'              => 'Companies',
			'AlcContinent'            => 'Continents',
			'AlcCountry'              => 'Countries',
			'AlcDrug'                 => 'Drugs',
			'AlcEntertainmentAward'   => 'Entertainment awards',
			'AlcFacility'             => 'Facilities',
			'AlcFieldTerminology'     => 'Field terminologies',
			'AlcFinancialMarketIndex' => 'Financial market indexes',
			'AlcGeographicFeature'    => 'Geographic features',
			'AlcHealthCondition'      => 'Health conditions',
			'AlcHoliday'              => 'Holidays',
			'AlcMovie'                => 'Movies',
			'AlcMusicGroup'           => 'Music groups',
			'AlcNaturalDisaster'      => 'Natural disasters',
			'OperatingSystem'         => 'Operating systems',
			'AlcOrganization'         => 'Organizations',
			'AlcPerson'               => 'People',
			'AlcPrintMedia'           => 'Print media',
			'AlcRadioProgram'         => 'Radio programs',
			'AlcRadioStation'         => 'Radio stations',
			'AlcRegion'               => 'Regions',
			'AlcSport'                => 'Sports',
			'AlcStateOrCounty'        => 'States or countries',
			'AlcTechnology'           => 'Technology',
			'AlcTelevisionShow'       => 'Television shows',
			'AlcTelevisionStation'    => 'Television stations'
		);
	}

	/**
	 * Returns a plain text string which should be passed to Alchemy.
	 *
	 * @return string
	 */
	public function getContentForAlchemy() {
		$content = '';

		$fields = $this->owner->config()->extraction_fields;
		
		if (!$fields) {
			$fields = array('Title', 'Content');
		}

		foreach ($fields as $field) {
			if ($this->owner->hasField($field)) {
				$content .= strip_tags($this->owner->$field) . ' ';
			}
		}

		return $content;
	}
	
	public function getDefaultAlchemyFields() {
		$fields = Config::inst()->get($this->owner->class, 'stored_metadata');
		return $fields;
	}
	
	public function getAlchemyData() {
		$data = $this->owner->AlchemyMetadata->getValues();
		if (!$data) {
			$data = array();
		}

		$fields = $this->getDefaultAlchemyFields();
		
		foreach ($fields as $fname => $default) {
			if (!isset($data[$fname])) {
				$data[$fname] = $default;
			}
		}
		return $data;
	}

	/**
	 * Add in some form fields for data returned from alchemy
	 *
	 * @param FieldSet $fields
	 */
	public function updateCMSFields(\FieldList $fields) {
		if ($this->owner->ID) {
			$field = new AlchemyMetadataField($this, 'AlchemyMetadata', 'Root.Alchemy.AlcMetadata');
			if ($this->automatic) {
				$field = $field->performReadonlyTransformation();
			}
			
//			$data = $this->owner->AlchemyMetadata->getValues();
//			if (!$data) {
//				$data = array();
//			}
//
//			$data = nl2br(str_replace("  ", "&nbsp;", json_encode($data, JSON_PRETTY_PRINT)));
//			$field = LiteralField::create('AlchemyInfo', $data);
			
			if ($rootTab = $fields->fieldByName('Root')) {
				$fields->addFieldToTab('Root.Alchemy', $field); 
			} else {
				$fields->push($field);
			}
			
		}
	}

//	public function updateSearchableFields(&$fields) {
//		$extras = $this->extraStatics();
//		foreach ($extras['db'] as $field => $type) {
//			$fields[$field] = array(
//				'title' => $field,
//				'filter' => 'PartialMatchFilter',
//			);
//		}
//	}

	public function onBeforeWrite() {
		if ($this->automatic) {
			singleton('AlchemyService')->alchemise($this->owner);
		}
	}
	
	public function additionalSolrValues() {
		$data = $this->getAlchemyData();
		$alc = array();
		foreach ($data as $field => $val) {
			$alc[$field] = $val;
		}
		return $alc;
	}
	
	public function updateSolrSearchableFields(&$fields) {
		$configed = Config::inst()->get('Alchemisable', 'stored_metadata');
		
		if (is_array($configed)) {
			foreach ($configed as $name => $default) {
				$fields[$name] = array('filter' => 'PartialMatchFilter', 'title' => $name);
			}
		}
	}
}