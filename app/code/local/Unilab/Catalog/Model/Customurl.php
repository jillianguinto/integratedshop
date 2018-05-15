<?php 
class Unilab_Catalog_Model_Customurl extends Mage_Core_Model_Url
{   
    public function getCustomUrl($routePath = null, $routeParams = null, $exclude = null)
    { 
        $escapeQuery = false; 
        if (isset($routeParams['_fragment'])) {
            $this->setFragment($routeParams['_fragment']);
            unset($routeParams['_fragment']);
        }

        if (isset($routeParams['_escape'])) {
            $escapeQuery = $routeParams['_escape'];
            unset($routeParams['_escape']);
        }

        $query = null;
        if (isset($routeParams['_query'])) {
            $this->purgeQueryParams();
            $query = $routeParams['_query'];
            unset($routeParams['_query']);
        }

        $noSid = null;
        if (isset($routeParams['_nosid'])) {
            $noSid = (bool)$routeParams['_nosid'];
            unset($routeParams['_nosid']);
        } 
		
        $url = $this->getRouteUrl($routePath, $routeParams, $exclude);
        /** 
         * Apply query params, need call after getRouteUrl for rewrite _current values
         */
        if ($query !== null) {
            if (is_string($query)) {
                $this->setQuery($query);
            } elseif (is_array($query)) {
                $this->setQueryParams($query, !empty($routeParams['_current']));
            }
            if ($query === false) {
                $this->setQueryParams(array());
            }
        }

        if ($noSid !== true) {
            $this->_prepareSessionUrl($url);
        }

        $query = $this->getQuery($escapeQuery);
        if ($query) {
            $mark = (strpos($url, '?') === false) ? '?' : ($escapeQuery ? '&amp;' : '&');
            $url .= $mark . $query;
        }

        if ($this->getFragment()) {
            $url .= '#' . $this->getFragment();
        }

        return $this->escape($url);
    }
	
    /**
     * Retrieve route URL
     *
     * @param string $routePath
     * @param array $routeParams
     *
     * @return string
     */
    public function getRouteUrl($routePath = null, $routeParams = null, $exclude = null)
    {
        $this->unsetData('route_params');

        if (isset($routeParams['_direct'])) {
            if (is_array($routeParams)) {
                $this->setRouteParamsCustom($routeParams, false, $exclude);
            }
            return $this->getBaseUrl() . $routeParams['_direct'];
        }

        if (!is_null($routePath)){
            $this->setRoutePath($routePath);
        }
        if (is_array($routeParams) || !is_null($exclude)) {
            $this->setRouteParamsCustom($routeParams, false, $exclude);
        } 

        $url = $this->getBaseUrl() . $this->getRoutePath($routeParams);
        return $url;
    }
	
	 /**
     * Set route params
     *
     * @param array $data
     * @param boolean $unsetOldParams
     * @return Mage_Core_Model_Url
     */
    public function setRouteParamsCustom(array $data, $unsetOldParams = true, $exclude = null)
    { 
        if (isset($data['_type'])) {
            $this->setType($data['_type']);
            unset($data['_type']);
        }

        if (isset($data['_store'])) {
            $this->setStore($data['_store']);
            unset($data['_store']);
        }

        if (isset($data['_forced_secure'])) {
            $this->setSecure((bool)$data['_forced_secure']);
            $this->setSecureIsForced(true);
            unset($data['_forced_secure']);
        } elseif (isset($data['_secure'])) {
            $this->setSecure((bool)$data['_secure']);
            unset($data['_secure']);
        }

        if (isset($data['_absolute'])) {
            unset($data['_absolute']);
        }

        if ($unsetOldParams) {
            $this->unsetData('route_params');
        }

        $this->setUseUrlCache(true);
        if (isset($data['_current'])) {
            if (is_array($data['_current'])) {
                foreach ($data['_current'] as $key) {
                    if (array_key_exists($key, $data) || !$this->getRequest()->getUserParam($key)) {
                        continue;
                    }
                    $data[$key] = $this->getRequest()->getUserParam($key);
                }
            } elseif ($data['_current']) {				 
                foreach ($this->getRequest()->getUserParams() as $key => $value) {
                    if (array_key_exists($key, $data) || $this->getRouteParam($key)) {
                        continue;
                    }
                    $data[$key] = $value;
                }
                foreach ($this->getRequest()->getQuery() as $key => $value) {					
					//exclude Current attribute need not to use
					if($key == $exclude){
						continue;
					}
                    $this->setQueryParam($key, $value);
                }
                $this->setUseUrlCache(false);
            }
            unset($data['_current']);
        }

        if (isset($data['_use_rewrite'])) {
            unset($data['_use_rewrite']);
        }

        if (isset($data['_store_to_url']) && (bool)$data['_store_to_url'] === true) {
            if (!Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL, $this->getStore())
                && !Mage::app()->isSingleStoreMode()
            ) {
                $this->setQueryParam('___store', $this->getStore()->getCode());
            }
        }
        unset($data['_store_to_url']);
		 
		if(isset($data[$exclude])){
			unset($data[$exclude]);
		}
		
        foreach ($data as $k => $v) {
            $this->setRouteParam($k, $v);
        }

        return $this;
    }   
}
