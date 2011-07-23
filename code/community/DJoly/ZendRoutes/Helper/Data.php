<?php
class DJoly_ZendRoutes_Helper_Data extends Mage_Core_Helper_Url
{
    public function getUrl($route,array $data = array(),array $config = array()){

        if(!isset($config['reset'])){
            $config['reset'] = false;
        } elseif(!is_bool($config['reset'])){
            throw new InvalidArgumentException('Encoded configuration setting must be boolean.');
        }

        if(!isset($config['encode'])){
            $config['encode'] = false;
        } elseif(!is_bool($config['encode'])){
            throw new InvalidArgumentException('Encode configuration setting must be boolean.');
        }

        if(!isset($config['partial'])){
            $config['partial'] = false;
        } elseif(!is_bool($config['partial'])){
            throw new InvalidArgumentException('Encoded configuration setting must be boolean.');
        }

        //Get route
        $route = Mage::getSingleton('zendroutes/config')->getRouteFromConfig($route);

        $baseUrl = Mage::getBaseUrl('link', (isset($config['_secure']) && $config['_secure'] === true));

        return $baseUrl . $route->assemble($data,$config['reset'],$config['encode'],$config['partial']);

    }
}
