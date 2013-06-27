<?php
/**
 * My Skin skin
 *
 * @file
 * @ingroup Skins
 * @author Garrett LeSage
 */

if( !defined( 'MEDIAWIKI' ) ) die( "This is an extension to the MediaWiki package and cannot be run standalone." );
 
$wgExtensionCredits['skin'][] = array(
        'path' => __FILE__,
        'name' => 'Strapping',
        'url' => "https://github.com/OSAS/strapping-mediawiki",
        'author' => 'Garrett LeSage',
        'descriptionmsg' => 'strapping-desc',
);

$wgValidSkinNames['strapping'] = 'Strapping';
$wgAutoloadClasses['SkinStrapping'] = dirname(__FILE__).'/Strapping.skin.php';
$wgExtensionMessagesFiles['SkinStrapping'] = dirname(__FILE__).'/Strapping.i18n.php';
 
$wgResourceModules['skins.strapping'] = array(
        'styles' => array(
                'strapping/bootstrap/css/bootstrap.css' => array( 'media' => 'screen' ),
                'strapping/bootstrap/awesome/css/font-awesome.css' => array( 'media' => 'screen' ),
                'strapping/screen.css' => array( 'media' => 'screen' ),
                'strapping/theme.css' => array( 'media' => 'screen' ),
	),
	'scripts' => array(
		'strapping/bootstrap/js/bootstrap.js',
		'strapping/strapping.js',
	),
        'remoteBasePath' => &$GLOBALS['wgStylePath'],
        'localBasePath' => &$GLOBALS['wgStyleDirectory'],
);

if (file_exists('strapping/fonts.css')) {
  $wgResourceModules['skins.strapping']['styles'][] = 'strapping/fonts.css';
}

# Default options to customize skin
$wgStrappingSkinLogoLocation = 'bodycontent';
$wgStrappingSkinLoginLocation = 'footer';
$wgStrappingSkinAnonNavbar = false;
$wgStrappingSkinUseStandardLayout = false;
$wgStrappingSkinDisplaySidebarNavigation = false;
# Show print/export in navbar by default
$wgStrappingSkinSidebarItemsInNavbar = array( 'coll-print_export' );
