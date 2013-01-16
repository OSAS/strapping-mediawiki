/*
 * Strapping-specific scripts
 */
jQuery(function($) {
  var $nav = $('#page-header ul.navigation'),
      $searchLink = $nav.find('a.search-link'),
      $search = $('#nav-searchform .search-query');

  var nav = {
    focus: function(ev) {
      nav.toggle(true);
    },
    blur: function(ev) {
      nav.toggle(false);
    },
    toggle: function(bool) {
      $nav.toggleClass('searchform-enabled', bool).toggleClass('searchform-disabled', !bool);
    }
  };

  $searchLink.on({
    'click': function() {
      nav.focus();
      setTimeout(function(){
        $search.focus().select();
      }, 100);
    }
  });

  $search.on({
    'blur': nav.blur,
    'keypress': function(ev) {
      // Convert <esc> into a blur
      if (ev.keyCode === 27) { this.blur(); }
    }
  });
});

