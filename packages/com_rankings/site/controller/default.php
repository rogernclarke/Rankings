<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Rider Controller for Rankings component
 *
 * @since 2.0
 */
class RankingsControllerDefault extends JControllerLegacy
{
	/**
	 * Display the rider.
	 *
	 * @return  boolean  True
	 *
	 * @since 2.0
	 */
	public function display($viewName, $modelNames = array(), $cacheable = false, $urlparams = array())
	{
		// Set the view name
		$this->input->set('view', $viewName);

		// Get the view
		$document 	= \JFactory::getDocument();
		$viewType 	= $document->getType();
		$viewLayout = $this->input->get('layout', 'default', 'string');
		$view 		= $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));

		// Get the ID from the request
		$id	= $this->input->getInt('cid');

		// Get the default model
		if ($model = $this->getModel($viewName))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Get the ID from the request
		if ($id	= $this->input->getInt('cid'))
		{
			// Get the item
			$item = $model->getItem($id);

			// Increment the hits on the default model item
			$model->hit();
		}
		else
		{
			switch ($viewName)
			{
				case "dashboard":
				case "ridersbydistance":
				case "ridersbyridecount":
				case "ridersbytime":
					if ($model = $this->getModel('statistics'))
					{
						// Push the model into the view (as default)
						$view->setModel($model, true);
					}
					break;

				case "eventtotalsbydistrict":
					if ($model = $this->getModel('events'))
					{
						// Push the model into the view (as default)
						$view->setModel($model, true);
					}
					break;

				case "rankings":
					// Get the ranking type
					$array  = $this->input->get('type', array(), 'ARRAY');

					if (!empty($array))
					{
						$rankingType = $array[0];
					}

					$config['subcontext'] = 'rankings.' . $rankingType;

					if ($model = $this->getModel('rides', '', $config))
					{
						// Push the model into the view (as default)
						$view->setModel($model, false);
					}
					break;
			}
		}

		// Get additional models
		$config['subcontext'] = $viewName;

		foreach ($modelNames as $modelName)
		{
			if ($additionalModel = $this->getModel($modelName, '', $config))
			{
				$additionalModel->set($viewName . 'Id', $id);

				if ($year = $model->getState('filter.year'))
				{
					$additionalModel->set('year', $year);
				}

				// Push the model into the view
				$view->setModel($additionalModel);
			}
		}

		// Display the view
		$cacheable 		= true;
		$urlparams  = array(
			'cid' => 'INT',
		);

		$view->document = $document;

		$cacheable = true;

		// Display the view
		if ($cacheable && $viewType !== 'feed' && \JFactory::getConfig()->get('caching') >= 1)
		{
			$option = $this->input->get('option');
			if (is_array($urlparams))
			{
				$app = \JFactory::getApplication();
				if (!empty($app->registeredurlparams))
				{
					$registeredurlparams = $app->registeredurlparams;
				}
				else
				{
					$registeredurlparams = new \stdClass;
				}
				foreach ($urlparams as $key => $value)
				{
					// Add your safe URL parameters with variable type as value {@see \JFilterInput::clean()}.
					$registeredurlparams->$key = $value;
				}
				$app->registeredurlparams = $registeredurlparams;
			}
			try
			{
				/** @var \JCacheControllerView $cache */
				$cache = \JFactory::getCache($option, 'view');
				$cache->get($view, 'display');
			}
			catch (\JCacheException $exception)
			{
				$view->display();
			}
		}
		else
		{
			$view->display();
		}

		return $this;
	}
}
