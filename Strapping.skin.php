<?php
/**
 * Vector - Modern version of MonoBook with fresh look and many usability
 * improvements.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) ) {
  die( -1 );
}

/**
 * SkinTemplate class for Vector skin
 * @ingroup Skins
 */
class SkinStrapping extends SkinTemplate {

  var $skinname = 'strapping', $stylename = 'strapping',
    $template = 'StrappingTemplate', $useHeadElement = true;

  /**
   * Initializes output page and sets up skin-specific parameters
   * @param $out OutputPage object to initialize
   */
  public function initPage( OutputPage $out ) {
    global $wgLocalStylePath;

    parent::initPage( $out );

    // Append CSS which includes IE only behavior fixes for hover support -
    // this is better than including this in a CSS fille since it doesn't
    // wait for the CSS file to load before fetching the HTC file.
    $min = $this->getRequest()->getFuzzyBool( 'debug' ) ? '' : '.min';
    $out->addHeadItem( 'csshover',
      '<!--[if lt IE 7]><style type="text/css">body{behavior:url("' .
        htmlspecialchars( $wgLocalStylePath ) .
        "/{$this->stylename}/csshover{$min}.htc\")}</style><![endif]-->"
    );

    $out->addHeadItem('responsive', '<meta name="viewport" content="width=device-width, initial-scale=1.0">');
    $out->addModuleScripts( 'skins.strapping' );
  }

  /**
   * Load skin and user CSS files in the correct order
   * fixes bug 22916
   * @param $out OutputPage object
   */
  function setupSkinUserCss( OutputPage $out ){
    global $wgResourceModules;

    parent::setupSkinUserCss( $out );

    // FIXME: This is the "proper" way to include CSS
    // however, MediaWiki's ResourceLoader messes up media queries
    // See: https://bugzilla.wikimedia.org/show_bug.cgi?id=38586
    // &: http://stackoverflow.com/questions/11593312/do-media-queries-work-in-mediawiki
    //
    //$out->addModuleStyles( 'skins.strapping' );

    // Instead, we're going to manually add each, 
    // so we can use media queries
    foreach ( $wgResourceModules['skins.strapping']['styles'] as $cssfile => $cssvals ) {
      if (isset($cssvals)) {
        $out->addStyle( $cssfile, $cssvals['media'] );
      } else {
        $out->addStyle( $cssfile );
      }
    }

  }
}

/**
 * QuickTemplate class for Vector skin
 * @ingroup Skins
 */
class StrappingTemplate extends BaseTemplate {

  /* Functions */

  /**
   * Outputs the entire contents of the (X)HTML page
   */
  public function execute() {
    global $wgGroupPermissions;
    global $wgVectorUseIconWatch;
    global $wgSearchPlacement;
    global $wgStrappingSkinLogoLocation;
    global $wgStrappingSkinLoginLocation;
    global $wgStrappingSkinAnonNavbar;
    global $wgStrappingSkinUseStandardLayout;

    if (!$wgSearchPlacement) {
      $wgSearchPlacement['header'] = true;
      $wgSearchPlacement['nav'] = false;
      $wgSearchPlacement['footer'] = false;
    }

    // Build additional attributes for navigation urls
    $nav = $this->data['content_navigation'];

    if ( $wgVectorUseIconWatch ) {
      $mode = $this->getSkin()->getTitle()->userIsWatching() ? 'unwatch' : 'watch';
      if ( isset( $nav['actions'][$mode] ) ) {
        $nav['views'][$mode] = $nav['actions'][$mode];
        $nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
        $nav['views'][$mode]['primary'] = true;
        unset( $nav['actions'][$mode] );
      }
    }

    $xmlID = '';
    foreach ( $nav as $section => $links ) {
      foreach ( $links as $key => $link ) {
        if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
          $link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
        }

        $xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
        $nav[$section][$key]['attributes'] =
          ' id="' . Sanitizer::escapeId( $xmlID ) . '"';
        if ( $link['class'] ) {
          $nav[$section][$key]['attributes'] .=
            ' class="' . htmlspecialchars( $link['class'] ) . '"';
          unset( $nav[$section][$key]['class'] );
        }
        if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
          $nav[$section][$key]['key'] =
            Linker::tooltip( $xmlID );
        } else {
          $nav[$section][$key]['key'] =
            Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
        }
      }
    }
    $this->data['namespace_urls'] = $nav['namespaces'];
    $this->data['view_urls'] = $nav['views'];
    $this->data['action_urls'] = $nav['actions'];
    $this->data['variant_urls'] = $nav['variants'];

    // Output HTML Page
    $this->html( 'headelement' );
?>

<?php if ( $wgGroupPermissions['*']['edit'] || $wgStrappingSkinAnonNavbar || $this->data['loggedin'] ) { ?>
<div id="userbar" class="navbar navbar-static">
  <div class="navbar-inner">
    <div style="width: auto;" class="container">

      <div class="pull-left">
        <?php
          if ( $wgStrappingSkinLogoLocation == 'navbar' ) {
            $this->renderLogo();
          }
          # Page header & menu
          $this->renderNavigation( array( 'PAGE' ) );

          # This content in other languages
          if ( $this->data['language_urls'] ) {
            $this->renderNavigation( array( 'LANGUAGES' ) );
          }

          # Edit button
          $this->renderNavigation( array( 'EDIT' ) ); 
          
          # Actions menu
          $this->renderNavigation( array( 'ACTIONS' ) ); 

          # Sidebar items to display in navbar
          $this->renderNavigation( array( 'SIDEBARNAV' ) );

          if ( !isset( $portals['TOOLBOX'] ) ) {
            $this->renderNavigation( array( 'TOOLBOX' ) ); 
          }
        ?>
      </div>

      <div class="pull-right">
        <?php
          if ($wgSearchPlacement['header']) {
            $this->renderNavigation( array( 'SEARCH' ) ); 
          }

          # Personal menu (at the right)
          $this->renderNavigation( array( 'PERSONAL' ) ); 
        ?>
      </div>

    </div>
  </div>
</div>
<?php } ?>

    <div id="mw-page-base" class="noprint"></div>
    <div id="mw-head-base" class="noprint"></div>

    <!-- Header -->
    <div id="page-header" class="container <?php echo $this->data['loggedin'] ? 'signed-in' : 'signed-out'; ?>">
      <section class="span12">

      <?php
      if ( $wgStrappingSkinLogoLocation == 'bodycontent' ) {
        $this->renderLogo();
      } ?>

      <ul class="navigation nav nav-pills pull-right searchform-disabled">

      <?php
      $this->renderNavigation( array( 'SIDEBAR' ) );

      if ($wgSearchPlacement['nav']) {
        $this->renderNavigation( array( 'SEARCHNAV' ) );
      }

      ?>

      </ul>

    </section>
    </div>

    <?php if ($this->data['loggedin']) {
      $userStateClass = "user-loggedin";
    } else {
      $userStateClass = "user-loggedout";
    } ?>

    <?php if ($wgGroupPermissions['*']['edit'] || $this->data['loggedin']) {
      $userStateClass += " editable";
    } else {
      $userStateClass += " not-editable";
    } ?>

    <!-- content -->
    <section id="content" class="mw-body container <?php echo $userStateClass; ?>">
      <div id="top"></div>
      <div id="mw-js-message" style="display:none;"<?php $this->html( 'userlangattributes' ) ?>></div>
      <?php if ( $this->data['sitenotice'] ): ?>
      <!-- sitenotice -->
      <div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
      <!-- /sitenotice -->
      <?php endif; ?>
      <!-- bodyContent -->
      <div id="bodyContent">
        <?php if( $this->data['newtalk'] ): ?>
        <!-- newtalk -->
        <div class="usermessage"><?php $this->html( 'newtalk' )  ?></div>
        <!-- /newtalk -->
        <?php endif; ?>
        <?php if ( $this->data['showjumplinks'] ): ?>
        <!-- jumpto -->
        <div id="jump-to-nav" class="mw-jump">
          <?php $this->msg( 'jumpto' ) ?> <a href="#mw-head"><?php $this->msg( 'jumptonavigation' ) ?></a>,
          <a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
        </div>
        <!-- /jumpto -->
        <?php endif; ?>


        <!-- innerbodycontent -->
        <?php # Peek into the body content of articles, to see if a custom layout is used
        if ($wgStrappingSkinUseStandardLayout || preg_match("/class.*row/i", $this->data['bodycontent']) && $this->data['articleid']) {
          # If there's a custom layout, the H1 and layout is up to the page ?>
          <div id="innerbodycontent" class="layout">
            <h1 id="firstHeading" class="firstHeading page-header">
              <span dir="auto"><?php $this->html( 'title' ) ?></span>
            </h1>
            <!-- subtitle -->
            <div id="contentSub" <?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
            <!-- /subtitle -->
            <?php if ( $this->data['undelete'] ): ?>
            <!-- undelete -->
            <div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
            <!-- /undelete -->
            <?php endif; ?>
            <?php $this->html( 'bodycontent' ); ?>
          </div>
        <?php } else {
          # If there's no custom layout, then we automagically add one ?>
          <div id="innerbodycontent" class="row nolayout"><div class="offset1 span10">
            <h1 id="firstHeading" class="firstHeading page-header">
              <span dir="auto"><?php $this->html( 'title' ) ?></span>
            </h1>
            <!-- subtitle -->
            <div id="contentSub" <?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
            <!-- /subtitle -->
            <?php if ( $this->data['undelete'] ): ?>
            <!-- undelete -->
            <div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
            <!-- /undelete -->
            <?php endif; ?>
            <?php $this->html( 'bodycontent' ); ?>
          </div></div>
        <?php } ?>
        <!-- /innerbodycontent -->

        <?php if ( $this->data['printfooter'] ): ?>
        <!-- printfooter -->
        <div class="printfooter">
        <?php $this->html( 'printfooter' ); ?>
        </div>
        <!-- /printfooter -->
        <?php endif; ?>
        <?php if ( $this->data['catlinks'] ): ?>
        <!-- catlinks -->
        <?php $this->html( 'catlinks' ); ?>
        <!-- /catlinks -->
        <?php endif; ?>
        <?php if ( $this->data['dataAfterContent'] ): ?>
        <!-- dataAfterContent -->
        <?php $this->html( 'dataAfterContent' ); ?>
        <!-- /dataAfterContent -->
        <?php endif; ?>
        <div class="visualClear"></div>
        <!-- debughtml -->
        <?php $this->html( 'debughtml' ); ?>
        <!-- /debughtml -->
      </div>
      <!-- /bodyContent -->
    </section>
    <!-- /content -->

      <!-- footer -->

      <?php
        /* Support a custom footer, or use MediaWiki's default, if footer.php does not exist. */
        $footerfile = dirname(__FILE__).'/footer.php';

        if ( file_exists($footerfile) ):
          ?><div id="footer" class="footer container custom-footer"><?php
            include( $footerfile );
          ?></div><?php
        else:
      ?>

      <div id="footer" class="footer container"<?php $this->html( 'userlangattributes' ) ?>>
        <div class="row">
    <?php
      $footerLinks = $this->getFooterLinks();

      if (is_array($footerLinks)) {
        foreach($footerLinks as $category => $links ):
          if ($category === 'info') { continue; } ?>

            <ul id="footer-<?php echo $category ?>">
              <?php foreach( $links as $link ): ?>
                <li id="footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
              <?php endforeach; ?>
              <?php
                if ($category === 'places') {

                  # Show sign in link, if not signed in
                  if ($wgStrappingSkinLoginLocation == 'footer' && !$this->data['loggedin']) {
                    $personalTemp = $this->getPersonalTools();

                    if (isset($personalTemp['login'])) {
                      $loginType = 'login';
                    } else {
                      $loginType = 'anonlogin';
                    }

                    ?><li id="pt-login"><a href="<?php echo $personalTemp[$loginType]['links'][0]['href'] ?>"><?php echo $personalTemp[$loginType]['links'][0]['text']; ?></a></li><?php
                  }

                  # Show the search in footer to all
                  if ($wgSearchPlacement['footer']) {
                    echo '<li>';
                    $this->renderNavigation( array( 'SEARCHFOOTER' ) ); 
                    echo '</li>';
                  }
                }
              ?>
            </ul>
          <?php 
              endforeach; 
            }
          ?>
          <?php $footericons = $this->getFooterIcons("icononly");
          if ( count( $footericons ) > 0 ): ?>
            <ul id="footer-icons" class="noprint">
    <?php      foreach ( $footericons as $blockName => $footerIcons ): ?>
              <li id="footer-<?php echo htmlspecialchars( $blockName ); ?>ico">
    <?php        foreach ( $footerIcons as $icon ): ?>
                <?php echo $this->getSkin()->makeFooterIcon( $icon ); ?>

    <?php        endforeach; ?>
              </li>
    <?php      endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
      <!-- /footer -->

<?php endif; ?>

    <?php $this->printTrail(); ?>

  </body>
</html>
<?php
  }

  /**
   * Render logo
   */
  private function renderLogo() {
        $mainPageLink = $this->data['nav_urls']['mainpage']['href'];
        $toolTip = Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) );
?>
                  <ul class="nav logo-container" role="navigation"><li id="p-logo"><a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>" <?php echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) ) ?>><img src="<?php $this->text( 'logopath' ); ?>" alt="<?php $this->html('sitename'); ?>"></a><li></ul>
<?php
  }

  /**
   * Render one or more navigations elements by name, automatically reveresed
   * when UI is in RTL mode
   *
   * @param $elements array
   */
  private function renderNavigation( $elements ) {
    global $wgVectorUseSimpleSearch;
    global $wgStrappingSkinLoginLocation;
    global $wgStrappingSkinDisplaySidebarNavigation;
    global $wgStrappingSkinSidebarItemsInNavbar;

    // If only one element was given, wrap it in an array, allowing more
    // flexible arguments
    if ( !is_array( $elements ) ) {
      $elements = array( $elements );
    // If there's a series of elements, reverse them when in RTL mode
    } elseif ( $this->data['rtl'] ) {
      $elements = array_reverse( $elements );
    }
    // Render elements
    foreach ( $elements as $name => $element ) {
      echo "\n<!-- {$name} -->\n";
      switch ( $element ) {

        case 'EDIT':
          if ( !array_key_exists('edit', $this->data['content_actions']) ) {
            break;
          }
          $navTemp = $this->data['content_actions']['edit'];

          if ($navTemp) { ?>
            <div class="actions pull-left nav">
                <a id="b-edit" href="<?php echo $navTemp['href']; ?>" class="btn"><i class="icon-edit"></i> <?php echo $navTemp['text']; ?></a>
            </div>
          <?php } 
        break;


        case 'PAGE':
          $theMsg = 'namespaces';
          $theData = array_merge($this->data['namespace_urls'], $this->data['view_urls']);
          ?>
          <ul class="nav" role="navigation">
            <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
              <?php
              foreach ( $theData as $link ) {
                  if ( array_key_exists( 'context', $link ) && $link['context'] == 'subject' ) {
              ?>
              <a data-toggle="dropdown" class="dropdown-toggle brand" role="menu"><?php echo htmlspecialchars( $link['text'] ); ?> <b class="caret"></b></a>
                  <?php } ?>
              <?php } ?>
              <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>

                <?php 
                foreach ( $theData as $link ) {
                  # Skip a few redundant links
                  if (preg_match('/^ca-(view|edit)$/', $link['id'])) { continue; }

                  ?><li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?> tabindex="-1"><?php echo htmlspecialchars( $link['text'] ) ?></a></li><?php
                }

          ?></ul></li></ul><?php

        break;


        case 'TOOLBOX':

          $theMsg = 'toolbox';
          $theData = array_reverse($this->getToolbox());
          ?>

          <ul class="nav" role="navigation">

            <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">

              <a data-toggle="dropdown" class="dropdown-toggle" role="button"><?php $this->msg($theMsg) ?> <b class="caret"></b></a>

              <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>

                <?php
                  foreach( $theData as $key => $item ) {
                    if (preg_match('/specialpages|whatlinkshere/', $key)) {
                      echo '<li class="divider"></li>';
                    }

                    echo $this->makeListItem( $key, $item );
                  }
                ?>
              </ul>

            </li>

          </ul>
          <?php
        break;


        case 'VARIANTS':

          $theMsg = 'variants';
          $theData = $this->data['variant_urls'];
          ?>
          <?php if (count($theData) > 0) { ?>
            <ul class="nav" role="navigation">
              <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
                <a data-toggle="dropdown" class="dropdown-toggle" role="button"><?php $this->msg($theMsg) ?> <b class="caret"></b></a>
                <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>
                  <?php foreach ( $theData as $link ): ?>
                    <li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?> tabindex="-1"><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
            </ul>
          <?php }

        break;

        case 'VIEWS':
          $theMsg = 'views';
          $theData = $this->data['view_urls'];
          ?>
          <?php if (count($theData) > 0) { ?>
            <ul class="nav" role="navigation">
              <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
                <a data-toggle="dropdown" class="dropdown-toggle" role="button"><?php $this->msg($theMsg) ?> <b class="caret"></b></a>
                <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>
                  <?php foreach ( $theData as $link ): ?>
                    <li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?> tabindex="-1"><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
            </ul>
          <?php }
        break;


        case 'ACTIONS':

          $theMsg = 'actions';
          $theData = array_reverse($this->data['action_urls']);
          
          if (count($theData) > 0) {
            ?><ul class="nav" role="navigation">
              <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
                <a data-toggle="dropdown" class="dropdown-toggle" role="button"><?php echo $this->msg( 'actions' ); ?> <b class="caret"></b></a>
                <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>
                  <?php foreach ( $theData as $link ):

                    if (preg_match('/MovePage/', $link['href'])) {
                      echo '<li class="divider"></li>';
                    }

                    ?>

                    <li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?> tabindex="-1"><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
            </ul><?php
          }

        break;


        case 'PERSONAL':
          $theMsg = 'personaltools';
          $theData = $this->getPersonalTools();
          $theTitle = $this->data['username'];
          $showPersonal = false;
          foreach ( $theData as $key => $item ) {
            if ( !preg_match('/(notifications|login|createaccount)/', $key) ) {
              $showPersonal = true;
            }
          }

          ?>
          <ul class="nav pull-right" role="navigation">
            <li class="dropdown" id="p-notifications" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
            <?php if ( array_key_exists('notifications', $theData) ) {
              echo $this->makeListItem( 'notifications', $theData['notifications'] );
            } ?>
            </li>
            <?php if ( $wgStrappingSkinLoginLocation == 'navbar' ): ?>
            <li class="dropdown" id="p-createaccount" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
              <?php if ( array_key_exists('createaccount', $theData) ) {
                echo $this->makeListItem( 'createaccount', $theData['createaccount'] );
              } ?>
            </li>
            <li class="dropdown" id="p-login" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
            <?php if ( array_key_exists('login', $theData) ) {
                echo $this->makeListItem( 'login', $theData['login'] );
            } ?>
            </li>
            <?php endif; ?>
            <?php
            if ( $showPersonal ):
            ?>
            <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( !$showPersonal ) echo ' emptyPortlet'; ?>">
              <a data-toggle="dropdown" class="dropdown-toggle" role="button">
                <i class="icon-user"></i>
                <?php echo $theTitle; ?> <b class="caret"></b></a>
              <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>
              <?php foreach( $theData as $key => $item ) {

                if (preg_match('/preferences|logout/', $key)) {
                  echo '<li class="divider"></li>';
                } else if ( preg_match('/(notifications|login|createaccount)/', $key) ) {
                  continue;
                }

                echo $this->makeListItem( $key, $item );
              } ?>
              </ul>
            </li>
            <?php endif; ?>
          </ul>
          <?php
        break;


        case 'SEARCH':
          ?>
            <form class="navbar-search" action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
              <input id="searchInput" class="search-query" type="search" accesskey="f" title="<?php $this->text('searchtitle'); ?>" placeholder="<?php $this->msg('search'); ?>" name="search" value="<?php echo htmlspecialchars ($this->data['search']); ?>">
              <?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton btn hidden' ) ); ?>
            </form>

          <?php
        break;


        case 'SEARCHNAV':
          ?>
        <li>
          <a id="n-Search" class="search-link"><i class="icon-search"></i>Search</a>
          <form class="navbar-search" action="<?php $this->text( 'wgScript' ) ?>" id="nav-searchform">
                        <input id="nav-searchInput" class="search-query" type="search" accesskey="f" title="<?php $this->text('searchtitle'); ?>" placeholder="<?php $this->msg('search'); ?>" name="search" value="<?php echo htmlspecialchars ($this->data['search']); ?>">
                        <?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton btn hidden' ) ); ?>
          </form>
        </li>

          <?php
        break;


        case 'SEARCHFOOTER':
          ?>
            <form class="" action="<?php $this->text( 'wgScript' ) ?>" id="footer-search">
              <i class="icon-search"></i><b class="border"></b><input id="footer-searchInput" class="search-query" type="search" accesskey="f" title="<?php $this->text('searchtitle'); ?>" placeholder="<?php $this->msg('search'); ?>" name="search" value="<?php echo htmlspecialchars ($this->data['search']); ?>">
              <?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton btn hidden' ) ); ?>
            </form>

          <?php
        break;


        case 'SIDEBARNAV':
          foreach ( $this->data['sidebar'] as $name => $content ) {
            if ( !$content ) {
              continue;
            }
            if ( !in_array( $name, $wgStrappingSkinSidebarItemsInNavbar ) ) {
                    continue;
            }
            $msgObj = wfMessage( $name );
            $name = htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $name ); ?>
          <ul class="nav" role="navigation">
          <li class="dropdown" id="p-<?php echo $name; ?>" class="vectorMenu">
          <a data-toggle="dropdown" class="dropdown-toggle" role="menu"><?php echo htmlspecialchars( $name ); ?> <b class="caret"></b></a>
          <ul aria-labelledby="<?php echo htmlspecialchars( $name ); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>><?php
            # This is a rather hacky way to name the nav.
            # (There are probably bugs here...) 
            foreach( $content as $key => $val ) {
              $navClasses = '';

              if (array_key_exists('view', $this->data['content_navigation']['views']) && $this->data['content_navigation']['views']['view']['href'] == $val['href']) {
                $navClasses = 'active';
              }?>

                <li class="<?php echo $navClasses ?>"><?php echo $this->makeLink($key, $val); ?></li><?php
            }
          }?>
         </li>
         </ul></ul><?php
        break;


        case 'SIDEBAR':
          foreach ( $this->data['sidebar'] as $name => $content ) {
            if ( !isset($content) ) {
              continue;
            }
            if ( in_array( $name, $wgStrappingSkinSidebarItemsInNavbar ) ) {
                    continue;
            }
            $msgObj = wfMessage( $name );
            $name = htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $name );
            if ( $wgStrappingSkinDisplaySidebarNavigation ) { ?>
              <li class="dropdown">
                <a data-toggle="dropdown" class="dropdown-toggle" role="button"><?php echo htmlspecialchars( $name ); ?><b class="caret"></b></a>
                <ul aria-labelledby="<?php echo htmlspecialchars( $name ); ?>" role="menu" class="dropdown-menu"><?php
            }
            # This is a rather hacky way to name the nav.
            # (There are probably bugs here...) 
            foreach( $content as $key => $val ) {
              $navClasses = '';

              if (array_key_exists('view', $this->data['content_navigation']['views']) && $this->data['content_navigation']['views']['view']['href'] == $val['href']) {
                $navClasses = 'active';
              }?>

                <li class="<?php echo $navClasses ?>"><?php echo $this->makeLink($key, $val); ?></li><?php
            }
            if ( $wgStrappingSkinDisplaySidebarNavigation ) {?>                </ul>              </li><?php
            }          }
        break;

        case 'LANGUAGES':
          $theMsg = 'otherlanguages';
          $theData = $this->data['language_urls']; ?>
          <ul class="nav" role="navigation">
            <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
              <a data-toggle="dropdown" class="dropdown-toggle brand" role="menu"><?php echo $this->html($theMsg) ?> <b class="caret"></b></a>
              <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>

              <?php foreach( $content as $key => $val ) { ?>
                <li class="<?php echo $navClasses ?>"><?php echo $this->makeLink($key, $val, $options); ?></li><?php
              }?>

              </ul>            </li>
          </ul><?php
          break;
      }
      echo "\n<!-- /{$name} -->\n";
    }
  }
}
