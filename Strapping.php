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

  function tocList($toc) {
    $this->savedToc = parent::tocList($toc);

    #return "<section class='mytocthing'>" . $this-savedToc . "</section>";
    return "";
  }

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
    $out->addScript('<script type="text/javascript" src="/skins/strapping/bootstrap/js/bootstrap.js"></script>');
    $out->addScript('<script type="text/javascript" src="/skins/strapping/strapping.js"></script>');
  }

  /**
   * Load skin and user CSS files in the correct order
   * fixes bug 22916
   * @param $out OutputPage object
   */
  function setupSkinUserCss( OutputPage $out ){
    parent::setupSkinUserCss( $out );
    /*
      $out->addModuleStyles( 'skins.strapping' );
     */
    $out->addStyle( 'strapping/screen.css', 'screen' );
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
    global $wgVectorUseIconWatch;
    global $wgSearchPlacement;

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

    // Reverse horizontally rendered navigation elements
    # We're changing theese vertically, 
    # so flipping them shouldn't be necessary
    if ( $this->data['rtl'] ) {
      #$this->data['view_urls'] =
        #array_reverse( $this->data['view_urls'] );
      #$this->data['namespace_urls'] =
        #array_reverse( $this->data['namespace_urls'] );
      #$this->data['personal_urls'] =
        #array_reverse( $this->data['personal_urls'] );
    }
    // Output HTML Page
    $this->html( 'headelement' );
?>

<?php if ($this->data['loggedin']) { ?>
<div id="userbar" class="navbar navbar-static">
  <div class="navbar-inner">
    <div style="width: auto;" class="container">

      <div class="pull-left">
        <?php 
          # Page header & menu
          $this->renderNavigation( array( 'PAGE' ) );

          # Edit button
          $this->renderNavigation( array( 'EDIT' ) ); 
          
          # Actions menu
          $this->renderNavigation( array( 'ACTIONS' ) ); 

          if ( !isset( $portals['TOOLBOX'] ) ) {
            $this->renderNavigation( array( 'TOOLBOX' ) ); 
          }
        ?>
      </div>

      <div class="pull-right">
        <?php
          # Namespaces, views, & variants have been merged into the page menu above
          #$this->renderNavigation( array( 'NAMESPACES', 'VIEWS', 'VARIANTS' ) ); 

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
<!--
<pre><?php print_r($this->getToolbox()); ?></pre>
<pre><?php print_r($this->data['content_actions']); ?></pre>
<pre><?php #print_r($this->data); ?></pre>
-->
<?php } ?>
<!--<pre><?php print_r($this->getPersonalTools()); ?></pre>-->


    <div id="mw-page-base" class="noprint"></div>
    <div id="mw-head-base" class="noprint"></div>

    <!-- Header -->
    <div id="page-header" class="container">
      <section class="row">

      <section class="span12">

        <!-- logo -->
        <div id="p-logo" class="logo pull-left"><a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>" <?php echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) ) ?>><img src="<?php $this->text( 'logopath' ); ?>" alt="<?php $this->html('sitename'); ?>"></a></div>
        <!-- /logo -->

      <ul class="navigation nav nav-pills pull-right searchform-disabled">

      <?php foreach ( $this->data['sidebar'] as $name => $content ) {
          # This is a rather hacky way to name the nav.
          # (There are probably bugs here...) 
          foreach( $content as $key => $val ) {
            $navClasses = '';

            if ($this->data['content_navigation']['views']['view']['href'] == $val['href']) {
              $navClasses = 'active';
            }

            echo "<li class='$navClasses'>" . $this->makeLink($key, $val, $options) . "</li>";
          }
      }

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

    <!-- content -->
    <section id="content" class="mw-body container <?php echo $userStateClass; ?>">
      <div id="top"></div>
      <div id="mw-js-message" style="display:none;"<?php $this->html( 'userlangattributes' ) ?>></div>
      <?php if ( $this->data['sitenotice'] ): ?>
      <!-- sitenotice -->
      <div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
      <!-- /sitenotice -->
      <?php endif; ?>
      <!-- firstHeading -->
      <!--
      <h1 id="firstHeading" class="firstHeading page-header">
        <span dir="auto"><?php $this->html( 'title' ) ?></span>
      </h1>
      -->
      <!-- /firstHeading -->
      <!-- bodyContent -->
      <div id="bodyContent">
        <?php if ( $this->data['isarticle'] ): ?>
        <!-- tagline -->
        <!--<div id="siteSub"><?php $this->msg( 'tagline' ) ?></div>-->
        <!-- /tagline -->
        <?php endif; ?>
        <!-- subtitle -->
        <div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
        <!-- /subtitle -->
        <?php if ( $this->data['undelete'] ): ?>
        <!-- undelete -->
        <div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
        <!-- /undelete -->
        <?php endif; ?>
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

        <!-- bodycontent -->
        <?php # Peek into the body content, to see if a custom layout is used ?>
        <?php if (preg_match("/class.*row/i", $this->data['bodycontent'])) { ?>
          <?php # If there's a custom layout, the H1 and layout is up to the page ?>
          <div class="layout">
            <?php $this->html( 'bodycontent' ); ?>
          </div>
        <?php } else { ?>
          <?php # If there's no custom layout, then we automagically add one ?>
          <div class="row nolayout"><div class="offset1 span10">
            <h1 id="firstHeading" class="firstHeading page-header">
              <span dir="auto"><?php $this->html( 'title' ) ?></span>
            </h1>
            <?php $this->html( 'bodycontent' ); ?>
          </div></div>
        <?php } ?>
        <!-- /bodycontent -->

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

    <?php if ($this->data['loggedin']) { ?>

      <!-- panel -->
      <div id="mw-panel" class="noprint">
        <?php $this->renderPortals(); ?>
      </div>
      <!-- /panel -->

    <?php } ?>

      <!-- footer -->
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
                  if (!$this->data['loggedin']) {
                    $personalTemp = $this->getPersonalTools();
                    ?><li id="pt-login"><a href="<?php echo $personalTemp['login']['links'][0]['href'] ?>"><?php echo $personalTemp['login']['links'][0]['text']; ?></a></li><?php
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

    <?php $this->printTrail(); ?>

  </body>
</html>
<?php
  }

  /**
   * Render a series of portals
   *
   * @param $portals array
   */
  private function renderPortals( $portals ) {
    // Force the rendering of the following portals
    if ( !isset( $portals['SEARCH'] ) ) {
      $portals['SEARCH'] = false;
    }
    if ( !isset( $portals['TOOLBOX'] ) ) {
      $portals['TOOLBOX'] = false;
    }
    if ( !isset( $portals['LANGUAGES'] ) ) {
      $portals['LANGUAGES'] = true;
    }
    // Render portals
    foreach ( $portals as $name => $content ) {
      if ( $content === false )
        continue;

      echo "\n<!-- {$name} -->\n";
      switch( $name ) {
        case 'SEARCH':
          break;

        case 'TOOLBOX':
          $this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
          break;

        case 'LANGUAGES':
          if ( $this->data['language_urls'] ) {
            $this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
          }
          break;

        default:
          $this->renderPortal( $name, $content );
        break;
      }
      echo "\n<!-- /{$name} -->\n";
    }
  }

  private function renderPortal( $name, $content, $msg = null, $hook = null ) {
    if ( $msg === null ) {
      $msg = $name;
    }
    ?>
<div class="portal" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>'<?php echo Linker::tooltip( 'p-' . $name ) ?>>
  <h5<?php $this->html( 'userlangattributes' ) ?>><?php $msgObj = wfMessage( $msg ); echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg ); ?></h5>
  <div class="body">
<?php
    if ( is_array( $content ) ): ?>
    <ul>
<?php
      foreach( $content as $key => $val ): ?>
      <?php echo $this->makeListItem( $key, $val ); ?>

<?php
      endforeach;
      if ( $hook !== null ) {
        wfRunHooks( $hook, array( &$this, true ) );
      }
      ?>
    </ul>
<?php
    else: ?>
    <?php echo $content; /* Allow raw HTML block to be defined by extensions */ ?>
<?php
    endif; ?>
  </div>
</div>
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
          $navTemp = $this->data['content_actions']['edit'];

          if ($navTemp) { ?>
            <div class="actions pull-left nav">
                <a href="<?php echo $navTemp['href']; ?>" class="btn"><i class="icon-edit"></i> <?php echo $navTemp['text']; ?></a>
            </div>
          <?php } 
        break;


        case 'PAGE':
          $theMsg = 'thispage';
          $theData = array_merge($this->data['namespace_urls'], $this->data['view_urls']);
          ?>
          <ul class="nav" role="navigation">
            <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
              <a data-toggle="dropdown" class="dropdown-toggle brand" role="menu"><?php $this->html($theMsg) ?> <b class="caret"></b></a>
              <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>

                <?php 
                foreach ( $theData as $link ) {
                  # Skip a few redundant links
                  if (preg_match('/^ca-(view|edit)$/', $link['id'])) { continue; }

                  ?><li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?> tabindex="-1"><?php echo htmlspecialchars( $link['text'] ) ?></a></li><?php
                }

          ?></ul></li></ul><?php

        break;


        case 'NAMESPACES':

          $theMsg = 'namespaces';
          $theData = $this->data['namespace_urls'];

          ?>
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

          <?php
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
                <a data-toggle="dropdown" class="dropdown-toggle" role="button"><?php echo $this->data['content_actions']['nstab-main']['text'] ?> <b class="caret"></b></a>
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

          ?>
          <ul class="nav pull-right" role="navigation">
            <li class="dropdown" id="p-<?php echo $theMsg; ?>" class="vectorMenu<?php if ( count($theData) == 0 ) echo ' emptyPortlet'; ?>">
              <a data-toggle="dropdown" class="dropdown-toggle" role="button">
                <i class="icon-user"></i>
                <?php echo $theImg . $theTitle; ?> <b class="caret"></b></a>
              <ul aria-labelledby="<?php echo $this->msg($theMsg); ?>" role="menu" class="dropdown-menu" <?php $this->html( 'userlangattributes' ) ?>>
              <?php foreach( $this->getPersonalTools() as $key => $item ) {

                if (preg_match('/preferences|logout/', $key)) {
                  echo '<li class="divider"></li>';
                }

                echo $this->makeListItem( $key, $item );
              } ?>
              </ul>
            </li>
          </ul>
          <?php
        break;


        case 'SEARCH':
          ?>
            <form class="navbar-search" action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
              <input id="searchInput" class="search-query" type="search" accesskey="f" title="<?php $this->text('searchtitle'); ?>" placeholder="<?php $this->msg('search'); ?>" name="search" value="<?php echo $this->data['search']; ?>">
              <?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton btn hidden' ) ); ?>
            </form>

          <?php
        break;


        case 'SEARCHNAV':
          ?>
        <li>
          <a id="n-Search" class="search-link"><i class="icon-search"></i>Search</a>
          <form class="navbar-search" action="<?php $this->text( 'wgScript' ) ?>" id="nav-searchform">
                        <input id="nav-searchInput" class="search-query" type="search" accesskey="f" title="<?php $this->text('searchtitle'); ?>" placeholder="<?php $this->msg('search'); ?>" name="search" value="<?php echo $this->data['search']; ?>">
                        <?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton btn hidden' ) ); ?>
          </form>
        </li>

          <?php
        break;


        case 'SEARCHFOOTER':
          ?>
            <form class="" action="<?php $this->text( 'wgScript' ) ?>" id="footer-search">
              <i class="icon-search"></i><b class="border"></b><input id="footer-searchInput" class="search-query" type="search" accesskey="f" title="<?php $this->text('searchtitle'); ?>" placeholder="<?php $this->msg('search'); ?>" name="search" value="<?php echo $this->data['search']; ?>">
              <?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton btn hidden' ) ); ?>
            </form>

          <?php
        break;

      }
      echo "\n<!-- /{$name} -->\n";
    }
  }
}
