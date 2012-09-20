<?php

if(!defined('MEDIAWIKI')) die(-1);

global $wgResourceModules;

$skinname = 'strapping';

$wgResourceModules["skins.$skinname"] = array(
    'styles' => array(
        'screen.css' => array( 'media' => 'screen' ),
    ),
    'scripts' => array(
        'scripts.js',
        'bootstrap/js/bootstrap.js',
    ),
    'remoteBasePath' => "$wgScriptPath/skins/$skinname/",
    'localBasePath' => "$IP/skins/$skinname/",
);
