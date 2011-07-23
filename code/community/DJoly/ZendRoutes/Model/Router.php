<?php
class DJoly_ZendRoutes_Model_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    public function match(Zend_Controller_Request_Http $request)
    {
        $this->fetchDefault();
        $config = Mage::getSingleton('zendroutes/config');
        $routes = $config->getRoutes();
        $modules = $config->getModules();

        $matchedRoute = false;
        $secure = false;
        foreach($routes as $name => $route){
            $match = $route->match(trim($request->getPathInfo(),'/'));
            if($match === false) continue;

            $matchedRoute = true;
            $secure = $config->isSecureRoute($name);

            foreach($match as $param => $value){

                $request->setParam($param, $value);

                if ($param === $request->getModuleKey()) {
                    $request->setModuleName($value);
                }
                if ($param === $request->getControllerKey()) {
                    $request->setControllerName($value);
                }
                if ($param === $request->getActionKey()) {
                    $request->setActionName($value);
                }
            }
            break;
        }

        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();


        if($matchedRoute){
            if($secure 
                && !Mage::app()->getStore()->isCurrentlySecure()
                && Mage::getStoreConfigFlag('web/secure/use_in_frontend')
                && substr(Mage::getStoreConfig('web/secure/base_url'),0,5)=='https'
            ){
                $url = $this->_getCurrentSecureUrl($request);
                Mage::app()->getFrontController()->getResponse()
                    ->setRedirect($url)
                    ->sendResponse();
                exit;
            }

            $controllerFileName = $this->getControllerFileName($modules[$module], $controller);
            require_once $controllerFileName;

            $controllerClassName = $this->getControllerClassName($modules[$module], $controller);
            $controllerInstance = Mage::getControllerInstance($controllerClassName, $request, Mage::app()->getFrontController()->getResponse());
            
            $request->setControllerModule($modules[$module])
                    ->setDispatched(true);
            
            $controllerInstance->dispatch($action);
        }

        return $matchedRoute;
    }
}
