<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Rankings Component Default Controller
 */
class RankingsControllersDefault extends JControllerBase
{
    /**
     * Protected fields
     **/
    // Redirection
    protected $message = 'message';
    protected $messageType = null;
    protected $redirect = null;

    public function execute()
    {
        // Get the input
        $jinput = $this->getInput();

        // Get the document object
        $document = JFactory::getDocument();
        $viewName = $jinput->getWord('view', 'cpanel');
        $viewName = JFile::makeSafe($viewName);
        $viewFormat = $document->getType();
        $layoutName = $jinput->getWord('layout', 'list');
        $layoutName = JFile::makeSafe($layoutName);
        
        $viewClass = 'RankingsViews' . ucfirst($viewName) . ucfirst($viewFormat);
        $modelClass = 'RankingsModels' . ucfirst($viewName);

        // Register the path for the models
        JLoader::register('RankingsModelsDefault', JPATH_COMPONENT . '/models/default.php');
        JLoader::register($modelClass, JPATH_COMPONENT . '/models/' . $viewName . '.php');

        // Register the layout paths for the view
        $paths = new SplPriorityQueue;
        $paths->insert(JPATH_COMPONENT . '/views/' . $viewName . '/tmpl', 'normal');

        $view = new $viewClass(new $modelClass(''), $paths);
        
        $view->setLayout($layoutName);

        // Render our view.
        echo $view->render();
        return true;
    }

    /**
     * Redirects the browser or returns false if no redirect is set.
     *
     * @return  boolean  False if no redirect exists.
     **/
    public function redirect()
    {
        if ($this->redirect)
        {
            $app = $this->getApplication();

            // Enqueue the redirect message
            $app->enqueueMessage($this->message, $this->messageType);

            // Execute the redirect
            $app->redirect($this->redirect);
        }
        return false;
    }

    /**
     * Set a URL for browser redirection
     *
     * @param   string  $url   URL to redirect to
     * @param   string  $msg   Message to display on redirect. Optional, defaults to value set internally by controller, if any
     * @param   string  $type  Message type. Optional, defaults to 'message' or the type set by a previous call to setMessage
     * @return  RaceControllersDefault  This object to support chaining
     **/
    public function setRedirect($url, $msg = null, $type = null)
    {
        $this->redirect = $url;

        if ($msg !== null)
        {
            // Controller may have set this directly
            $this->message = $msg;
        }

        // Ensure the type is not overwritten by a previous call to setMessage
        if (empty($type))
        {
            if (empty($this->messageType))
            {
                $this->messageType = 'message';
            }
        }
        // If the type is explicitly set, set it
        else
        {
            $this->messageType = $type;
        }
        return $this;
    }
}