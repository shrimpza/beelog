<?php

    /*************************************************************************
        General site config
    *************************************************************************/
    // relative URL to your web root
    $config['site']['url'] = '/~shrimp/buggen';
    $config['site']['title'] = 'Buggen';

    // if enabled, load times, DB usage and API access info will be printed
    // in the footer of each page
    $config['site']['showstats'] = false;


    /*************************************************************************
        Database
    *************************************************************************/
    $config['database']['dsn'] = 'mysql:host=localhost;dbname=buggen';
    $config['database']['user'] = 'buggen';
    $config['database']['pass'] = 'eNBpZ9GP5vVZQjq6';

    /*************************************************************************
        Default site theme (clean)
    *************************************************************************/
    $config['templates']['theme'] = 'clean';

    $config['templates']['compile_dir'] = dirname(__FILE__).'/../templates/compiled';
    $config['templates']['theme_dir'] = dirname(__FILE__).'/../templates';

    /*************************************************************************
        No need to modify anything beyond this point, unless you've writing
        additional modules and need to enable them.
    *************************************************************************/
    $config['plugins']['enabled'] = array(
                                        'users',
                                        'mainmenu',
                                        'ajaxtest',
                                    );
    $config['plugins']['directory'] = dirname(__FILE__).'/../plugins/';
    $config['plugins']['default'] = 'users';

    $GLOBALS['config'] = $config;

    ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.dirname(__FILE__).'/libs'.PATH_SEPARATOR.dirname(__FILE__).'/eve');

?>