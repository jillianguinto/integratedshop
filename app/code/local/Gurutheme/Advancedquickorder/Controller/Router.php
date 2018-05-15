<?php

class Gurutheme_Advancedquickorder_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard {

    public function initControllerRouters($observer) {
        $front = $observer->getEvent()->getFront();
        $router = new Gurutheme_Advancedquickorder_Controller_Router();
        $front->addRouter('advancedquickorder', $router);
    }

    public function match(Zend_Controller_Request_Http $request) {

        if (!Mage::app()->isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                    ->setRedirect(Mage::getUrl('install'))
                    ->sendResponse();
            exit;
        }
        $route = Mage::helper('advancedquickorder')->_getRoute();
        $identifier = $request->getPathInfo();
        if (substr(str_replace("/", "", $identifier), 0, strlen($route)) != $route) {
            return false;
        }

        $identifier = substr_replace($request->getPathInfo(), '', 0, strlen("/" . $route . "/"));                
        if (substr($request->getPathInfo(), 0, 19) !== '/advancedquickorder') {
            if ($identifier == '') {
                $request->setModuleName('advancedquickorder')
                        ->setControllerName('index')
                        ->setActionName('index');
                return true;
            }
        }
        return false;
    }

}