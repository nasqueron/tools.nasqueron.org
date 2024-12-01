<?php

/**
 * This magic method is called when a class can't be loaded
 */
spl_autoload_register(function ($className) {
    // Application classes
    $classes['Document'] = './includes/document.php';
    $classes['InstantCommonsMedia'] = './includes/instantcommons.php';
    $classes['MediaWikiBot'] = './includes/mediawikibot.php';
    $classes['Session'] = './includes/session.php';

    $classes['Cache'] = './includes/cache/cache.php';
    $classes['CacheMemcached'] = './includes/cache/memcached.php';
    $classes['CacheVoid'] = './includes/cache/void.php';

    $classes['FilesListingController'] = './includes/controllers/FilesListingController.php';

    $classes['User'] = './includes/objects/user.php';

    // Tools classes
    $classes['AdobeSwatchExchangeFile'] = './color/ase.php';
    $classes['Color'] = './color/color.php';

    $classes['FingerClient'] = './finger/FingerClient.php';
    $classes['ThimblDocument'] = './finger/ThimblDocument.php';
    $classes['ThimblController'] = './finger/thimbl.php';

    $classes['DataSource'] = './infographics/datasources/datasource.php';
    $classes['MediaWikiDataSource'] = './infographics/datasources/mediawikidatasources.php';
    $classes['WikipediaDataSource'] = './infographics/datasources/mediawikidatasources.php';
    $classes['FrWikipediaDataSource'] = './infographics/datasources/mediawikidatasources.php';
    $classes['AssembleeNationale'] = './infographics/datasources/pol.fr.assembleenationale.php';
    $classes['SenateursFrance'] = './infographics/datasources/pol.fr.senat.php';

    $classes['JsonComposer'] = './json/JsonComposer.php';

    $classes['ListOperation'] = './lists/ListOperation.php';
    $classes['RegexpFactory'] = './lists/RegexpFactory.php';

    //Loader
    if (array_key_exists($className, $classes)) {
        require_once($classes[$className]);
    }
});

require 'vendor/autoload.php';
