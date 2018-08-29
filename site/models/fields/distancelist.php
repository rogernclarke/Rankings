<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    0.0.1
 * @package    Rankings
 * @subpackage Form
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports a custom SQL select list from an external database
 *
 * @since  0.1
 */
class JFormFieldDistanceList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  0.1
	 */
	public $type = 'DistanceList';

	/**
	 * Method to get the custom field options.
	 * Use the query attribute to supply a query to generate the list.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   0.1
	 */
	protected function getOptions()
	{
		$options = array();
		
		$items = array('10', '25', '50', '100', 'Other');

		foreach ($items as $item)
		{
			$options[] = JHtml::_('select.option', $item, $item);
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}