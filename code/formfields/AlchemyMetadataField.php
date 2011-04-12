<?php
/**
 * A form field that displays the metadata extracted from an object via Alchemy,
 * and allows users to send the document to Alchemy to be analysed.
 *
 * @package silverstripe-alchemy
 */
class AlchemyMetadataField extends CompositeField {

	public static $allowed_actions = array(
		'analyse'
	);

	protected $parent;
	protected $fullName;

	public function __construct(DataObject $parent, $name, $fullName) {
		$this->parent   = $parent;
		$this->name     = $name;
		$this->fullName = $fullName;

		$entities = array();

		foreach (Alchemisable::entity_fields() as $field => $name) {
			if (!in_array($field, array('AlcPerson', 'AlcCompany', 'AlcOrganization'))) {
				$entities[] = new MultiValueTextField($field, $name);
			}
		}

		parent::__construct(array(
			new HeaderField('ExtactedMetadataHeader', 'Extracted Metadata'),
			new TextField('AlcCategory', 'Category'),
			new MultiValueTextField('AlcKeywords', 'Keywords'),
			new MultiValueTextField('AlcPerson', 'Person'),
			new MultiValueTextField('AlcCompany', 'Companies'),
			new MultiValueTextField('AlcOrganization', 'Organizations'),
			new ToggleCompositeField('AlchemyFurtherMedata', 'Further Metadata', $entities),
			new LiteralField('AlchemyLogo', '<a href="http://www.alchemyapi.com/" target="_blank" style="float: right"><img src="http://www.alchemyapi.com/images/alchemyAPI.jpg" /></a>')
		));
	}

	public function analyse() {
		$service = singleton('AlchemyService');
		$record  = $this->form->getRecord();
		$content = $record->getAlchemyContent();

		$oldCat = $record->AlcCategory;
		$newCat = $service->getCategoryFor($content);

		$oldKeys = $record->AlcKeywords->getValues();
		if (!$oldKeys) {
			$oldKeys = array();
		}
		$newKeys = $service->getKeywordsFor($content);
		$addKeys = array_diff($newKeys, $oldKeys);
		$rmKeys  = array_diff($oldKeys, $newKeys);

		sort($addKeys);
		sort($rmKeys);

		$entities    = $service->getEntitiesFor($content);
		$entsChanged = new DataObjectSet();
		$pos         = 1;

		foreach (Alchemisable::entity_fields() as $field => $title) {
			$type = substr($field, 3);
			$old  = (array) $record->$field->getValues();
			$new  = array_key_exists($type, $entities) ? $entities[$type] : array();

			$added = array_diff($new, $old);
			$rmed  = array_diff($old, $new);

			if ($added || $rmed) {
				$entsChanged->push(new ArrayData(array(
					'Title'   => ucwords($title),
					'Name'    => $field,
					'Added'   => $this->arrToSet($added, array('ParentPos' => $pos)),
					'Removed' => $this->arrToSet($rmed, array('ParentPos' => $pos))
				)));
			}

			$pos++;
		}

		// If there are no changes made, then return that to the user.
		if ($oldCat == $newCat && !$addKeys && !$rmKeys && !count($entsChanged)) {
			return '<p>There was no additional metadata extracted from the document.</p>';
		}

		$data = new ArrayData(array(
			'CategoryChanged' => $oldCat != $newCat,
			'OldCategory'     => $oldCat,
			'NewCategory'     => $newCat,
			'KeywordsChanged' => $addKeys || $rmKeys,
			'KeywordsAdded'   => $this->arrToSet($addKeys),
			'KeywordsRemoved' => $this->arrToSet($rmKeys),
			'EntitiesChanged' => $entsChanged
		));
		return $data->renderWith('AlchemyMetadataField_analyse');
	}

	protected function arrToSet(array $arr, $extra = array()) {
		$set = new DataObjectSet();

		foreach ($arr as $name) {
			$set->push(new ArrayData(array_merge($extra, array(
				'Name' => $name
			))));
		}

		return $set;
	}

	public function FieldHolder() {
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(Director::protocol() . 'ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.min.js');
		Requirements::css(Director::protocol() . 'ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/base/jquery-ui.css');
		Requirements::javascript(ALCHEMISER_DIR . '/javascript/AlchemyMetadataField.js');
		Requirements::css(ALCHEMISER_DIR . '/css/AlchemyMetadataField.css');

		return $this->renderWith('AlchemyMetadataField');
	}

	public function Link($action = null) {
		return Controller::join_links(
			$this->form->FormAction(), 'field/' . $this->fullName, $action
		);
	}

}