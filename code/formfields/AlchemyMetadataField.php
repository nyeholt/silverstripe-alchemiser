<?php
/**
 * A form field that displays the metadata extracted from an object via Alchemy,
 * and allows users to send the document to Alchemy to be analysed.
 *
 * @package silverstripe-alchemy
 */
class AlchemyMetadataField extends CompositeField {

	protected $parent;

	public function __construct(DataObject $parent, $name) {
		$this->name   = $name;
		$this->parent = $parent;

		$entities = array();

		foreach (Alchemisable::entity_fields() as $field => $name) {
			if (!in_array($field, array('AlcPerson', 'AlcCompany', 'AlcOrganization'))) {
				$entities[] = new MultiValueTextField($field, $name);
			}
		}

		parent::__construct(array(
			new HeaderField('AlchemyMetadataHeader', 'Alchemy Metadata'),
			new TextField('AlcCategory', 'Category'),
			new MultiValueTextField('AlcKeywords', 'Keywords'),
			new MultiValueTextField('AlcPerson', 'Person'),
			new MultiValueTextField('AlcCompany', 'Companies'),
			new MultiValueTextField('AlcOrganization', 'Organizations'),
			new ToggleCompositeField('AlchemyFurtherMedata', 'Further Metadata', $entities)
		));
	}

	public function FieldHolder() {
		$result = '';

		foreach ($this->children as $child) {
			$result .= $child->FieldHolder();
		}

		return $result;
	}

}