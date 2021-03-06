<?php

/**
 * Memcached cache.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * Cache class: memcached
 *
 * 0.1    2010-07-06 22:55    Initial version [DcK]
 *
 * @package     Zed
 * @subpackage  Cache
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * Memcached cache
 * 
 * !!! This class uses the Memcached extension AND NOT the Memcache ext !!!!
 *
 * References:
 * @link http://www.php.net/manual/en/book/memcached.php
 * @link http://memcached.org
 *
 * This class implements a singleton pattern.
 */
class CacheMemcached {
    
    /**
     * The current cache instance
     *
     * @var CacheMemcached 
     */
    static $instance = null;

    /**
     * The Memcached object
     *
     * @var Memcached 
     */    
    private $memcached = null;
    
    /**
     * Gets the cache instance, initializing it if needed
     * 
     * @return Cache the cache instance, or null if nothing is cached
     */
    static function load () {       
        //Checks extension is okay
        if (!extension_loaded('memcached')) {
            if (extension_loaded('memcache')) {
                message_die(GENERAL_ERROR, "Can't initialize $engine cache engine.<br />PHP extension memcached not loaded.<br /><strong>!!! This class uses the Memcached extension AND NOT the Memcache extension (this one is loaded) !!!</strong>", 'Cache');
            } else {
                message_die(GENERAL_ERROR, "Can't initialize $engine cache engine.<br />PHP extension memcached not loaded.", 'Cache');
            }
        }
    
        //Creates the Memcached object if needed
        if (self::$instance === null) {
            global $Config;
            
            self::$instance = new CacheMemcached();
            self::$instance->memcached = new Memcached();
            self::$instance->memcached->addServer(
                $Config['cache']['server'],
                $Config['cache']['port']
            );
        }
        
        return self::$instance;
    }
    
    /**
     * Gets the specified key's data
     *
     * @param string $key the key to fetch
     * @return mixed the data at the specified key
     */
    function get ($key) {
       return $this->memcached->get($key);
    }

    /**
     * Sets the specified data at the specified key
     *
     * @param string $key the key where to store the specified data
     * @param mixed $value the data to store
     */
    function set ($key, $value) {
        return $this->memcached->set($key, $value);
    }

    /**
     * Deletes the specified key's data
     *
     * @param string $key the key to delete
     */
    function delete ($key) {
        return $this->memcached->delete($key);
    }
}
