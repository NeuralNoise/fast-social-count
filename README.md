# Fast Social Count #
*A WordPress Plugin adding sharing buttons without slow javascripts. Options provided to count how many times the url has been shared on specified social networks. If your host supports WP_Object_Cache, the count of shares will be cached at set intervals [Read more: codex.wordpress.org/Class_Reference/WP_Object_Cache](http://codex.wordpress.org/Class_Reference/WP_Object_Cache)*

Set globals on plugin settings page, override them per page/post with shortcode values.

Use in page or post with shortcode: **[fastsocial]**

Use in your theme with
```php
<?php echo do_shortcode('[fastsocial]'); ?>
```
### Available args in shortcode: ###
 * [fast-social-count **sharelabel**="Share with your friends"] *what to do with buttons...*
 * [fast-social-count **hassharedtext**="people already done it!"] *text after total count*
 * [fast-social-count **totalcount**="no"]  *if you don't want to show number of total likes*
 * [fast-social-count **googleplus**="yes" **pinterest**="yes" **facebook**="yes" **twitter**="yes" **linkedin**="yes"] *shows all networks, overrides globals, use ="no" for removing*
 * [fast-social-count **class**="col-sm12"] *add your own class to outer div*
 * [fast-social-count **iconclass**="fa-2x text-danger"] *add classes to the font icons*
 
#### Override Fast Social Count CSS ####

Change the hover for the buttons, and/or font/text-color in your own css:
```css
li.fsc_share_button {}
li.fsc_share_button:hover {}
```
Have a look default hover setting, and the rest of the css-file: [fast-social-count.css](fast-social-count.css)

Of course you could copy that css to your own css-file and then deregister the stylesheet in your functions.php:
```php
//Remove plugin styles
add_action( 'wp_print_styles', 'my_deregister_styles', 100 );
function my_deregister_styles() {
  wp_deregister_style( 'fast_social_count_css' );
}
```
