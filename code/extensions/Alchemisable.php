<?php
/**
 * Attach this to an object and its content will be
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class Alchemisable extends DataObjectDecorator {

	/**
	 * @var bool
	 */
	protected $automatic;

	/**
	 * Returns a map of all the Alchemy entity DB fields to human-readable names.
	 *
	 * @return array
	 */
	public function entity_fields() {
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
	 * @param bool $automatic Specify whether values should be automatically
	 *        extracted on save - defaults to FALSE and letting the user manually
	 *        analyse the content.
	 */
	public function __construct($automatic = false) {
		$this->automatic = $automatic;
		parent::__construct();
	}

	/**
	 * Returns a plain text string which should be passed to Alchemy.
	 *
	 * @return string
	 */
	public function getAlchemyContent() {
		$content = '';

		if (!$fields = $this->owner->stat('extraction_fields')) {
			$fields = array('Title', 'Content');
		}

		foreach ($fields as $field) {
			if ($this->owner->hasField($field)) {
				$content .= strip_tags($this->owner->$field) . ' ';
			}
		}

		return $content;
	}

	public function extraStatics() {
		$fields = array(
			'AlcCategory' => 'Varchar(128)',
			'AlcKeywords' => 'MultiValueField'
		);

		$entities = self::entity_fields();
		$entities = array_fill_keys(array_keys($entities), 'MultiValueField');

		return array('db' => array_merge($fields, $entities));
	}

	/**
	 * Add in some form fields for data returned from alchemy
	 *
	 * @param FieldSet $fields
	 */
	public function updateCMSFields($fields) {
		if ($this->owner->ID) {
			$field = new AlchemyMetadataField($this, 'AlcMetadata', 'Root.Alchemy.AlcMetadata');

			if ($this->automatic) {
				$field = $field->performReadonlyTransformation();
			}

			$fields->addFieldToTab('Root.Alchemy', $field);
		}
	}

	public function updateSearchableFields(&$fields) {
		$extras = $this->extraStatics();
		foreach ($extras['db'] as $field => $type) {
			$fields[$field] = array(
				'title' => $field,
				'filter' => 'PartialMatchFilter',
			);
		}
	}

	public function onBeforeWrite() {
		if ($this->automatic) {
			singleton('AlchemyService')->alchemise($this->owner);
		}
	}

}