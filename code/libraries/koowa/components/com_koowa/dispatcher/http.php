<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Dispatcher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaDispatcherHttp extends KDispatcherHttp implements KObjectInstantiable
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config	An optional KObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Render the page before sending the response
        $this->addCommandCallback('before.send', '_renderPage');

        //Render an exception before sending the response
        $this->addCommandCallback('before.fail', '_renderError');

        //Force the controller to the information found in the request
        if($this->getRequest()->query->has('view')) {
            $this->_controller = $this->getRequest()->query->get('view', 'cmd');
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'response'          => 'com:koowa.dispatcher.response',
            'event_subscribers' => array('unauthorized', 'notfound'),
            'user'              => 'com:koowa.user',
            'limit'             => array(
                'default' => JFactory::getApplication()->getCfg('list_limit'),
                'max'     => 100
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Force creation of a singleton
     *
     * @param  KObjectConfigInterface  $config  Configuration options
     * @param  KObjectManagerInterface $manager	A KObjectManagerInterface object
     * @return KDispatcherDefault
     */
    public static function getInstance(KObjectConfigInterface $config, KObjectManagerInterface $manager)
    {
        // Check if an instance with this identifier already exists or not
        if (!$manager->isRegistered($config->object_identifier))
        {
            //Create the singleton
            $class    = $manager->getClass($config->object_identifier);
            $instance = new $class($config);
            $manager->setObject($config->object_identifier, $instance);

            //Add the factory map to allow easy access to the singleton
            $manager->registerAlias($config->object_identifier, 'dispatcher');
        }

        return $manager->getObject($config->object_identifier);
    }

    /**
     * Render the page
     *
     * @param KDispatcherContextInterface $context
     */
    protected function _renderPage(KDispatcherContextInterface $context)
    {
        $request   = $context->request;
        $response  = $context->response;

        //Render the page
        if(!$response->isRedirect() && $response->getContentType() == 'text/html')
        {
            //Render the page
            $config = array('response' => $response);

            $this->getObject('com:koowa.controller.page', $config)
                ->layout($request->query->get('tmpl', 'cmd') == 'koowa' ? 'koowa' : 'joomla')
                ->render();
        }
    }

    /**
     * Render an exception
     *
     * @throws InvalidArgumentException If the action parameter is not an instance of Exception
     * @param KDispatcherContextInterface $context	A dispatcher context object
     */
    protected function _renderError(KDispatcherContextInterface $context)
    {
        $request   = $context->request;
        $response  = $context->response;

        //Check an exception was passed
        if(!isset($context->param) && !$context->param instanceof KException)
        {
            throw new InvalidArgumentException(
                "Action parameter 'exception' [KException] is required"
            );
        }

        //Get the exception object
        if($context->param instanceof KEventException) {
            $exception = $context->param->getException();
        } else {
            $exception = $context->param;
        }

        //Make sure the output buffers are cleared
        while(ob_get_level()) {
            ob_end_clean();
        };

        //Render the error
        if(!JDEBUG && $request->getFormat() == 'html')
        {
            if (version_compare(JVERSION, '3.0', '>=')) {
                JErrorPage::render($exception);
            } else {
                JError::raiseError($exception->getCode(), $exception->getMessage());
            }
        }
        else
        {
            //Render the exception if debug mode is enabled or if we are returning json
            if(in_array($request->getFormat(), array('json', 'html')))
            {
                $config = array(
                    'request'  => $request,
                    'response' => $response
                );

                $this->getObject('com:koowa.controller.error',  $config)
                    ->layout('default')
                    ->render($exception);

                //Do not pass response back to Joomla
                $context->request->query->set('tmpl', 'koowa');
            }
        }
    }

    /**
     * Dispatch the controller and redirect
     *
     * This function divert the standard behavior and will redirect if no view information can be found in the request.
     *
     * @param KDispatcherContextInterface $context A command context object
     * @return  ComKoowaDispatcherHttp
     */
    protected function _actionDispatch(KDispatcherContextInterface $context)
    {
        //Redirect if no view information can be found in the request
        if(!$context->request->query->has('view'))
        {
            $url = clone($context->request->getUrl());
            $url->query['view'] = $this->getController()->getView()->getName();

            $this->redirect($url);
        }

        parent::_actionDispatch($context);
    }
}
