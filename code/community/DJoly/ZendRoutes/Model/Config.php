<?php
class DJoly_ZendRoutes_Model_Config
{
    protected $_routeConfigs = array();
    protected $_routes = array();
    protected $_modules = array();

    protected $_typeClass = array(
        'standard' => 'Zend_Controller_Router_Route',
        'regex'    => 'Zend_Controller_Router_Route_Regex'
    );

    public function initRoutes()
    {
        $routes = Mage::getConfig()->getNode('frontend/routes/route');
        foreach($routes as $routeConfig){
           $this->initRoute($routeConfig);
        }
    }

    public function getRoutes(){
        if(empty($this->_routes)){
            $this->initRoutes();
        }

        return $this->_routes;
    }

    /**
     *
     * @param <type> $routeConfig
     * @return Zend_Controller_Router_Route_Interface
     */
    public function initRoute($routeConfig)
    {
        $routeName = (string)$routeConfig->name;
        $this->_routeConfigs[$routeName] = $routeConfig;
        if(empty($routeName))
            Mage::throwException('Zend route must have a name. Name missing, please check configuration.');

        $module = (string)$routeConfig->module;

        if(empty($module))
            Mage::throwException('Zend route must have a module defined. Please check configuration.');

        $routeType = (string)$routeConfig->type;

        if(empty($routeType))
            Mage::throwException('Zend route type not defined. Please check configuration.');

        if(!isset($this->_typeClass[$routeType]))
            Mage::throwException(sprintf('%s is not a valid Zend route type. Please check configuration.',$routeType));

        //Gather args for route instantiation
        $route = (string)$routeConfig->args->route;

        $defaults = array();
        foreach($routeConfig->args->defaults->children() as $default){
            $defaults[$default->getName()] = (string)$default;
        }

        if(!isset($defaults['module']) || !isset($defaults['controller']) || !isset($defaults['action']))
            throw new Exception('Route must include module, controller, and action values.');

        $this->_modules[$defaults['module']] = $module;

        //Instantiate route
        $routeClass = $this->_typeClass[$routeType];
        $route = new $routeClass($route,$defaults);
        $this->_routes[$routeName] = $route;
        return $route;

    }

    /**
     * Returns module names for all routes
     * 
     * @return array
     */
    public function getModules(){
        if(empty($this->_modules)){
            $this->initRoutes();
        }

        return $this->_modules;
    }

    public function getRouteFromConfig($routeName){
        if(empty($this->_routeConfigs)){
            $this->initRoutes();
        }

        if(!isset($this->_routeConfigs[$routeName])){
            Mage::throwException(sprintf('No route config for the route %s',$routeName));
        }

        return $this->initRoute($this->_routeConfigs[$routeName]);
    }

    /**
     * @param string $routeName
     * @return boolean
     */
    public function isSecureRoute($routeName)
    {
        if(empty($this->_routeConfigs)){
            $this->initRoutes();
        }
        
        if(!isset($this->_routeConfigs[$routeName])){
            Mage::throwException(sprintf('No route config for the route %s',$routeName));
        }

        return $this->_routeConfigs[$routeNname]->secure ? true : false;
    }


}

