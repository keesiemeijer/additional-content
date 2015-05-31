# [Additional Content](https://github.com/keesiemeijer/additional-content) [![Build Status](https://travis-ci.org/keesiemeijer/additional-content.svg?branch=master)](https://travis-ci.org/keesiemeijer/additional-content) #

version:           1.1  
Requires at least: 4.0  
Tested up to:      4.2  

Add additional content before or after post content in single post pages. Additional content can be added or edited in the edit or publish Post screen.

![Additional content metabox](/../screenshots/assets/img/metabox.png?raw=true)

## Requirements

This plugin requires the Wordpress [recommended php version](https://wordpress.org/about/requirements/) 5.4 or greater.

WordPress has PHP 5.2.4 as the minimum required version. This is a version that has been unsupported since early 2011. The PHP 5.3.* versions have been unsupported since August 2014 as well. This means that these versions don't receive any updates, which leaves them potentially insecure. [http://www.wpupdatephp.com](http://www.wpupdatephp.com)

## Changing the text in the metaboxes
Let's say you want to use this plugin for users to add shortcodes after the content on the single post pages. The text string can be changed with the `additional_content_metabox_text` filter. Put this in your (child) theme's functions.php file or use it in a plugin.

```php
add_filter( 'additional_content_metabox_text', 'change_additional_content_text_strings' );

function change_additional_content_text_strings( $text ) {

	$text['title']                  = __( 'Add Shortcodes', 'additional-content' );
	$text['content']                = __( 'Shortcode', 'additional-content' );
	$text['prepend_content']        = __( 'Prepend Shortcode', 'additional-content' );
	$text['append_content']         = __( 'Append Shortcode', 'additional-content' );
	$text['prepend_append_content'] = __( 'Prepend and Append Shortcode', 'additional-content' );
	$text['prepend']                = __( 'Prepend shortcode', 'additional-content' );
	$text['append']                 = __( 'Append shortcode', 'additional-content' );
	$text['priority']               = __( 'Priority', 'additional-content' );
	$text['add_row']                = __( 'Add shortcode', 'additional-content' );
	$text['add_more_row']           = __( 'Add more shortcodes', 'additional-content' );
	$text['remove_row']             = __( 'Remove', 'additional-content' );
	$text['header_info']            = __( 'Add shortcodes to the post content on single post pages. ', 'additional-content' );
	$text['priority_info']          = '';

	return $text;
}
```

![Metabox with changed text strings](/../screenshots/assets/img/metabox_shortcode_example.png?raw=true)

## Dissalow Options
The prepend, append and priority fields can be removed with the `additional_content_metabox_options` filter. Put this in your (child) theme's functions.php file or use it in a plugin.

```php
add_filter( 'additional_content_metabox_options', 'remove_additional_content_options' );

function remove_additional_content_options( $options ) {
	$options['append_prepend'] = false;
	$options['priority'] = false;

	return $options;
}
```

![Metabox without options](/../screenshots/assets/img/metabox_options_example.png?raw=true)

## Allowing additional content on other pages

Use the `additional_content_add_content` filter if you want additional content to display on other pages as singular post pages. Put this in your (child) theme's functions.php file or use it in a plugin. Exampe adds the additional content to home page posts.

```php
add_filter( 'the_content', 'additional_content_home_page' );

function additional_content_home_page( $content ) {

	// Check if we're on the home page
	if ( !is_home() ) {
		return $content;
	}

	// Check if this is the main query and inside the loop.
	if ( in_the_loop() && is_main_query() ) {

		// Check if the plugin function get_content() exists.
		if ( function_exists( 'keesiemeijer\Additional_Content\\get_content' ) ) {
			// Add the additional content to the post content with the get_content() function.

			// Content and post id are required.
			// Post id is available inside the loop.
			$content = keesiemeijer\Additional_Content\get_content( $content, get_the_ID() );
		}
	}
	return $content;
}
```
