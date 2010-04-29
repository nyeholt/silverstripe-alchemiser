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

	public function extraStatics() {
		return array(
			'db' => array(
				// category
				'AlcCategory' => 'Varchar(128)',
				// keywords
				'AlcKeywords' => 'MultiValueField',
				// now all the possible entities
				'AlcAnniversary' => 'MultiValueField',
				'AlcCity' => 'MultiValueField',
				'AlcCompany' => 'MultiValueField',
				'AlcContinent' => 'MultiValueField',
				'AlcCountry' => 'MultiValueField',
				'AlcEntertainmentAward' => 'MultiValueField',
				'AlcFacility' => 'MultiValueField',
				'AlcFieldTerminology' => 'MultiValueField',
				'AlcFinancialMarketIndex' => 'MultiValueField',
				'AlcGeographicFeature' => 'MultiValueField',
				'AlcHealthCondition' => 'MultiValueField',
				'AlcHoliday' => 'MultiValueField',
				'AlcMovie' => 'MultiValueField',
				'AlcMusicGroup' => 'MultiValueField',
				'AlcNaturalDisaster' => 'MultiValueField',
				'AlcOrganization' => 'MultiValueField',
				'AlcPerson' => 'MultiValueField',
				'AlcPrintMedia' => 'MultiValueField',
				'AlcRadioProgram' => 'MultiValueField',
				'AlcRadioStation' => 'MultiValueField',
				'AlcRegion' => 'MultiValueField',
				'AlcSport' => 'MultiValueField',
				'AlcStateOrCounty' => 'MultiValueField',
				'AlcTechnology' => 'MultiValueField',
				'AlcTelevisionShow' => 'MultiValueField',
				'AlcTelevisionStation' => 'MultiValueField',
			),
		);
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
?>