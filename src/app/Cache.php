<?php

namespace Afosto\ActiveAnts;

class Cache {
    
    /**
     * Set the user session hash
     * @var string
     */
    private $_hash;
    
    /**
     * The path to the cache directory
     * @var string 
     */
    private $_cacheDirectory;
    
    /**
     * Set the directory used for caching
     * @param string $hash  
     * @param string $cacheDirectory
     * @throws ApiException
     */
    public function __construct($hash, $cacheDirectory) {
        $this->_hash = $hash;
        $this->_cacheDirectory = $cacheDirectory;
        if (!is_writable($cacheDirectory)) {
            throw new ApiException('Cache directory is not writable');
        }
        return $this;
    }    

    /**
     * Set the cache key value
     * @param string $key
     * @param string $contents
     * @param integer $expiry       Seconds untill expiration
     */
    public function setCache($key, $contents, $expiry) {
        if (file_put_contents($this->_cacheDirectory. '/' . $this->_getCacheKey($key), serialize($contents)) === FALSE) {
            throw new ApiException('Cache storage failed');
        }
        touch($this->_cacheDirectory . '/' . $this->_getCacheKey($key), (time() + (int) $expiry));
    }

    /**
     * Returns a cache key
     * @param string $key
     * @return string|boolean
     */
    public function getCache($key) {
        if (file_exists($this->_cacheDirectory . '/' . $this->_getCacheKey($key))) {
            if (filemtime($this->_cacheDirectory . '/' . $this->_getCacheKey($key)) < time()) {
                unlink($this->_cacheDirectory . '/' . $this->_getCacheKey($key));
                return false;
            }
            return unserialize(file_get_contents($this->_cacheDirectory . '/' . $this->_getCacheKey($key)));
        }
        return false;
    }
    
    /**
     * Returns the cache key, based on client specific data
     * @param string $key
     * @return string
     */
    private function _getCacheKey($key) {
        return $this->_hash . '-' . $key . '.bin';
    }
    
}
