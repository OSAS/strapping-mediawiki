# Strapping, a friendly starter theme for MediaWiki

Strapping is an elegant, responsive, and friendly starter skin for MediaWiki.
Its purpose is to provide a good base to build upon, and was primarily created
to provide a great default for wiki-as-a-website — but it works well for
standard wikis too.

Strapping is built on top of a modified Vector theme from
**[MediaWiki](http://mediawiki.org/)** and utilizes Twitter's
**[Bootstrap](http://twitter.github.com/bootstrap/)** for base layout,
typography, and additional widgets.

Because Strapping uses Bootstrap with its responsive extension, any site using
this skin works well on desktop browsers and scales down to display beautifully
on hand-held devices like tablets and smartphones.

Strapping also has complete coverage for all of MediaWiki, including the user
preferences and admin pages. All of MediaWiki's features are included, too.


## Get started

1. Change to the "skins" subdirectory of your MediaWiki installation: `cd skins`
2. Clone the repository: `git clone https://github.com/OSAS/strapping-mediawiki strapping`
3. Link the php files to the base skins directory: `ln -s strapping/S*php .`
4. Edit `LocalSettings.php` to change the skin to "strapping":
  `$wgDefaultSkin = "strapping";`
5. While in `LocalSettings.php`, add the following to disable editing when not signed in: `$wgGroupPermissions['*']['edit'] = false;`
6. Edit the wiki page `MediaWiki:Sidebar` with your web browser to change your navigation links.
7. Customize the skin to make the site look how you'd want. (See below.)


## Customization

It's easy to make the theme look however desired, and there are several methods
on achieving results.


### Basic Bootstrap customization

Bootstrap has a customization page where you can change several aspects of the
Bootstrap theme. Simply:

1. Visit [the Bootstrap customizer page](http://twitter.github.com/bootstrap/customize.html)
2. change values
3. click the giant button at the bottom of the page
4. replace Strapping's `bootstrap` directory with the one in your ZIP file

Note: Since Bootstrap is based on the LESS CSS preprocessor, you can also
achieve similar results from a command line.


### Bootswatch

[Bootswatch](http://bootswatch.com/) is a project that provides drop-in
Bootstrap CSS replacements.

Visit the site and grab a theme to start using it immediately.


### theme.css

This method can be used without any other customizations, or in addition to
altering Bootstrap themes.

1. In the `screen.css` file, uncomment the `theme.css` import.
2. Add custom CSS to the `theme.css` file, including any colors and fonts you'd
like to use.


#### Font sources

Custom fonts can be found on [Google Web Fonts](http://google.com/webfonts)
and you can make your own `@font-face`-ready fonts (if you have the permission
to do so) with [FontSquirrel's generator](http://fontsquirrel.com/fontface/generator)


## Markup reference

While plain vanilla MediaWiki text can be used, sites using Strapping can also
utilize any of Bootstrap's CSS for more advanced layout and markup.


### Strapping-specific

There are a few Strapping-specific CSS classes you can use.

  **FIXME: Elaborate on strapping-specific CSS classes here**


### Layout

Do **NOT** use tables for layout. Instead, use Bootstrap's scaffolding to do
layout. 

Bootstrap scaffolding is based on having rows that are formed with 12 possible
columns. To span a column, you use a classname of `span` plus the number of
columns you'd like to span, such as `span2` to use up 2 columns. Everything
should add up to 12 (or less, if there's going to be space on the right side).

Make sure to wrap the spans into `row`s for everything to work correctly.

It looks something like this:

```html
  <div class="row">
    <div class="span6"></div>
    <div class="span6"></div>
  </div>
  <div class="row">
    <div class="span6 offset2"></div>
    <div class="span3"></div>
    <div class="span1"></div>
  </div>
```

For more information, please visit [Bootstrap's documentation](http://twitter.github.com/bootstrap/scaffolding.html).


### Documentation

Please consult [MediaWiki's formatting page](http://www.mediawiki.org/wiki/Help:Formatting) for help with writing wiki text.

If you're feeling adventurous and want to use some more advanced formatting,
you can attach any Bootstrap classes to `div`s.


### Licensing, Copying, Usage

Strapping is open source, and built on open source projects.

Please check out the [LICENSE file](https://github.com/OSAS/strapping-mediawiki/blob/master/LICENSE) for details.
