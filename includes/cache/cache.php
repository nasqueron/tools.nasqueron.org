<?php

/**
 * Cache calling class.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This file provides a calling class, which read the configuration, ensures
 * the cache class for the cache engine given in config exists and initializes
 * it.
 *
 * You'll find a sample of implementation in the CacheMemcached.
 * @see CacheMemcached
 *
 * If no caching mechanism, a "blackhole" void cache will be used.
 * @see CacheVoid.
 *
 * The class to call is determined from the following preference:
 * <code>
 * $Config['cache']['engine'] = 'memcached'; //will use CacheMemcached class.
 * </code>
 *
 * 0.1    2010-07-06 22:45    Initial version [DcK]
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
 * Cache caller
 */
class Cache {
    /**
     * Gets the cache instance, initializing it if needed
     *
     * The correct cache instance to initialize will be determined from the
     * $Config['cache']['engine'] preference.
     *
     * The class cache to use will be Cache + (preference engine, capitalized)
     *
     * This method will creates an instance of the specified object,
     * calling the load static method from this object class.
     *
     * Example:
     * <code>
     * $Config['cache']['engine'] = 'quux';
     * $cache = Cache::load(); //Cache:load() will call CacheQuux:load();
     * </code>
     * 
     * @return Cache the cache instance, or null if nothing is cached
     */
    static function load () {
        global $Config;
        if (
            !array_key_exists('cache', $Config) ||
            !array_key_exists('engine', $Config['cache'])
        ) {
            //cache is not configured or engine is not specified
            $engine = 'void';
        } else {
            //engine is specified in the configuration
            $engine = $Config['cache']['engine'];    
        }
                
        $engine_file = 'includes/cache/' . $engine . '.php';
        $engine_class = 'Cache' . ucfirst($engine);
        
        if (!file_exists($engine_file)) {
            message_die(GENERAL_ERROR, "Can't initialize $engine cache engine.<br />$engine_file not found.", 'Cache');
        }
        
        require_once($engine_file);
        if (!class_exists($engine_class)) {
            message_die(GENERAL_ERROR, "Can't initialize $engine cache engine.<br />$engine_class class not found.", 'Cache');
        }
        
        return call_user_func(array($engine_class, 'load'));
    }
}

?>
