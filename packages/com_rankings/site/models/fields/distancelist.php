<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.7
 * @package    Rankings
 * @subpackage Form
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports a custom SQL select list from an external database
 */
class JFormFieldDistanceList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	public $type = 'DistanceList';

	/**
	 * Method to get the custom field options.
	 * Use the query attribute to supply a query to generate the list.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		$options = array();
		
		$values = array('10', '25', '50', '100', '12', '24', 'Other', 'Hill Climb');

		foreach ($values as $value)
		{
			switch ($value)
			{
				case '10':
				case '25':
				case '50':
				case '100':
					$text = $value . ' miles';
					break;
				
				case '12':
				case '24':
					$text = $value . ' hours';
					break;

				default:
					$text = $value;
			}

			$options[] = JHtml::_('select.option', $value, $text);
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}