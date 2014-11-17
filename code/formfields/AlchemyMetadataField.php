<?php
/**
 * A form field that displays the metadata extracted from an object via Alchemy,
 * and allows users to send the document to Alchemy to be analysed.
 *
 * @package silverstripe-alchemy
 */
class AlchemyMetadataField extends CompositeField {

	private static $allowed_actions = array(
		'analyse'
	);

	protected $parent;
	protected $fullName;

	public function __construct(DataObject $parent, $name, $fullName) {
		$this->parent   = $parent;
		$this->name     = $name;
		$this->fullName = $fullName;

		$entities = array();

//		foreach (Alchemisable::entity_fields() as $field => $name) {
//			if (!in_array($field, array('AlcPerson', 'AlcCompany', 'AlcOrganization'))) {
//				$entities[] = new MultiValueTextField($field, $name);
//			}
//		}
		
		
		
		$fields = array(
			new HeaderField('ExtactedMetadataHeader', 'Extracted Metadata'),
			
//			new MultiValueTextField('AlchemyMetadata[Category]', 'Person'),
//			new MultiValueTextField('AlcCompany', 'Companies'),
//			new MultiValueTextField('AlcOrganization', 'Organizations'),
//			new ToggleCompositeField('AlchemyFurtherMedata', 'Further Metadata', $entities),
			
		);
		
		$alcFields = $parent->getDefaultAlchemyFields();
		$data = $parent->getAlchemyData();
		
		foreach ($alcFields as $fname => $default) {
			$type = is_array($default) ? 'MultiValueTextField' : 'TextField';
			$field = new $type($name . '-' . $fname, $fname, $data[$fname]);
			$field->addExtraClass('alchemy-populated');
			$fields[] = $field;
		}
		$fields[] = new LiteralField('AlchemyLogo', '<a href="http://www.alchemyapi.com/" target="_blank" style="float: right"><img src="http://www.alchemyapi.com/images/alchemyAPI.jpg" /></a>');

		parent::__construct($fields);
	}

	public function analyse() {
		$service = singleton('AlchemyService');
		$record  = $this->form->getRecord();
		$content = $record->getContentForAlchemy();
		
		$data = $record->AlchemyMetadata->getValues();
		if (!$data) {
			$data = array();
		}
		
		$properties = Config::inst()->get('Alchemisable', 'stored_metadata');
		
		$changes = ArrayList::create();
		
		foreach ($properties as $prop => $def) {
			$method = "get{$prop}For";
			
			$oldData = isset($data[$prop]) ? $data[$prop] : $def;
			$newData = $def;
			
			if (method_exists($service, $method)) {
				$newData = $service->$method($content);
			} else {
				continue;
			}
			
			$info = ArrayData::create(array(
				'Title'		=> $prop,
			));
			// see whether we're expecting array/string data, and store appropriate diff
			if (is_array($def)) {
				$addItems = array_diff($newData, $oldData);
				$rmItems  = array_diff($oldData, $newData);

				sort($addItems);
				sort($rmItems);
				
				if (count($addItems) || count($rmItems)) {
					$info->AddInfo = $this->arrToSet($addItems);
					$info->RemoveInfo = $this->arrToSet($rmItems);
					$info->Type = 'array';
					$changes->push($info);
				}
				
			} else if ($oldData != $newData) {
				$info->RemoveInfo = $oldData;
				$info->AddInfo = $newData;
				$info->Type = 'field';
				$changes->push($info);
			}
		}
		
//		$oldCat = isset($data['Category']) ? $data['Category'] : '';
//		$newCat = $service->getCategoryFor($content);
//
//		$oldKeys = isset($data['Keywords']) ? $data['Keywords'] : '';
//		if (!$oldKeys) {
//			$oldKeys = array();
//		}
//
//		$concepts = $service->getConceptsFor($content);
//		
//		$taxonomy = $service->getTaxonomyFor($content);

//		$entities    = $service->getEntitiesFor($content);
//		$entsChanged = new DataObjectSet();
//		$pos         = 1;
//
//		foreach (Alchemisable::entity_fields() as $field => $title) {
//			$type = substr($field, 3);
//			$old  = (array) $record->$field->getValues();
//			$new  = array_key_exists($type, $entities) ? $entities[$type] : array();
//
//			$added = array_diff($new, $old);
//			$rmed  = array_diff($old, $new);
//
//			if ($added || $rmed) {
//				$entsChanged->push(new ArrayData(array(
//					'Title'   => ucwords($title),
//					'Name'    => $field,
//					'Added'   => $this->arrToSet($added, array('ParentPos' => $pos)),
//					'Removed' => $this->arrToSet($rmed, array('ParentPos' => $pos))
//				)));
//			}
//
//			$pos++;
//		}

//		// If there are no changes made, then return that to the user.
//		if ($oldCat == $newCat && !$addKeys && !$rmKeys) { // && !count($entsChanged)) {
//			return '<p>There was no additional metadata extracted from the document.</p>';
//		}
//
//		$data = new ArrayData(array(
//			'CategoryChanged' => $oldCat != $newCat,
//			'OldCategory'     => $oldCat,
//			'NewCategory'     => $newCat,
//			'KeywordsChanged' => $addKeys || $rmKeys,
//			'KeywordsAdded'   => $this->arrToSet($addKeys),
//			'KeywordsRemoved' => $this->arrToSet($rmKeys),
////			'EntitiesChanged' => $entsChanged
//		));
		$data = new ArrayData(array(
			'Changes' => $changes
		));
		return $data->renderWith('AlchemyMetadataField_analyse');
	}

	protected function arrToSet(array $arr, $extra = array()) {
		$set = ArrayList::create();

		foreach ($arr as $name) {
			$set->push(new ArrayData(array_merge($extra, array(
				'Name' => $name
			))));
		}

		return $set;
	}

	public function FieldHolder($properties = array()) {
//		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
//		Requirements::javascript(Director::protocol() . 'ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.min.js');
//		Requirements::css(Director::protocol() . 'ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/base/jquery-ui.css');
		Requirements::javascript(ALCHEMISER_DIR . '/javascript/AlchemyMetadataField.js');
		Requirements::css(ALCHEMISER_DIR . '/css/AlchemyMetadataField.css');

		return $this->renderWith('AlchemyMetadataField');
	}
	
	public function saveInto(\DataObjectInterface $record) {
		parent::saveInto($record);
		$data = $record->getAlchemyData();
		
		$fields = $record->getDefaultAlchemyFields();
		foreach ($fields as $name => $default) {
			$data[$name] = $this->children->dataFieldByName($this->name . '-' . $name)->Value();
		}
		
		$record->AlchemyMetadata = $data;
	}

	public function Link($action = null) {
		$n = $this->getName();
		return Controller::join_links(
			$this->form->FormAction(), 'field/' . $n, $action
		);
	}
	
	public function hasData() {
		return true; 
	}

}