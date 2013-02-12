<?php

/**
 * "Blackhole" void cache.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * Cache class: void
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
 * "blackhole" void cache
 *
  * This class doesn't cache information, it'a void wrapper
 *  get will always return null
 *  set and delete do nothing
 *
 * It will be used by default if no cache is specified.
 *
 * This class implements a singleton pattern.
 */
class CacheVoid {
    /**
     * @var CacheVoid the current cache instance
     */
    static $instance = null;
    
    /**
     * Gets the cache instance, initializing it if needed
     * 
     * @return Cache the cache instance, or null if nothing is cached
     */
    static function load () {       
        if (self::$instance === null) {
            self::$instance = new CacheVoid();
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
       return null;
    }

    /**
     * Sets the specified data at the specified key
     *
     * @param string $key the key where to store the specified data
     * @param mixed $value the data to store
     */    
    function set ($key, $value) { }

    /**
     * Deletes the specified key's data
     *
     * @param string $key the key to delete
     */
    function delete ($key) { }
}
?>