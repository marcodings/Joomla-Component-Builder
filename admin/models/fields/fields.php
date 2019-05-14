<?php
/**
 * @package    Joomla.Component.Builder
 *
 * @created    30th April, 2015
 * @author     Llewellyn van der Merwe <http://www.joomlacomponentbuilder.com>
 * @github     Joomla Component Builder <https://github.com/vdm-io/Joomla-Component-Builder>
 * @copyright  Copyright (C) 2015 - 2019 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Fields Form Field class for the Componentbuilder component
 */
class JFormFieldFields extends JFormFieldList
{
	/**
	 * The fields field type.
	 *
	 * @var		string
	 */
	public $type = 'fields';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array    An array of JHtml options.
	 */
	protected function getOptions()
	{
		$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select($db->quoteName(array('a.id','a.name','b.name'),array('id','field_name','type')));
$query->from($db->quoteName('#__componentbuilder_field', 'a'));
		$query->join('LEFT', '#__componentbuilder_fieldtype AS b ON b.id = a.fieldtype');
$query->where($db->quoteName('a.published') . ' >= 1');
$query->order('a.name ASC');
$db->setQuery((string)$query);
$items = $db->loadObjectList();
$options = array();
if ($items)
{
	$options[] = JHtml::_('select.option', '', 'Select an option');
	foreach($items as $item)
	{
		$options[] = JHtml::_('select.option', $item->id, $item->field_name . ' [' . $item->type . ']');
	}
}

return $options;
	}
}
