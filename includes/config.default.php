<?php

    /*************************************************************************
        General site config
    *************************************************************************/
    // relative URL to your web root
    $config['site']['url'] = '/beelog';
    $config['site']['title'] = 'Beelog';

    // the terminology used to refer to "bugs" can be changed here, for example
    // you could use, Bugs, Issues, Tasks, etc.
    $config['bugs']['desc'] = 'Bug';
    $config['bugs']['desc_plural'] = 'Bugs';

    // if enabled, load times, DB usage and API access info will be printed
    // in the footer of each page
    $config['site']['showstats'] = false;


    /*************************************************************************
        Database
    *************************************************************************/
    $config['database']['dsn'] = 'mysql:host=localhost;dbname=beelog';
    $config['database']['user'] = 'beelog';
    $config['database']['pass'] = '';

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