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
 * Attach this to an object and its content will be
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class Alchemisable extends DataObjectDecorator {

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
		$fields->addFieldToTab('Root.Alchemy', new AlchemyMetadataField($this, 'AlcMetadata'));
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
		if ($this->owner->ID) {
			$alchemy = singleton('AlchemyService');
			$alchemy->alchemise($this->owner);
		}
	}
}