# [Additional Content](https://github.com/keesiemeijer/additional-content) [![Build Status](https://travis-ci.org/keesiemeijer/additional-content.svg?branch=master)](https://travis-ci.org/keesiemeijer/additional-content) #

Version:           1.3.0  
Requires at least: 4.0  
Tested up to:      4.8  

Display additional content before or after post content in single post pages. Add content (for example shortcodes) on a post per post basis in the edit or publish Post screen.

![Additional content metabox](/../screenshots/assets/img/metabox.png?raw=true)

## Installation
Install via Composer into your WordPress plugins directory:
```bash
composer create-project keesiemeijer/additional-content
```

Or clone the GitHub repository in the plugins directory: 
```bash
git clone https://github.com/keesiemeijer/additional-content.git
```

Or download it directly as a [ZIP file](https://github.com/keesiemeijer/additional-content/archive/master.zip)

## PHP requirements

This plugin requires the [WordPress recommended PHP version](https://wordpress.org/about/requirements/) of **5.4.0** or greater. 

WordPress has PHP 5.2.4 as the minimum required version. This is a version that has been unsupported since early 2011. The PHP 5.3.* versions have been unsupported since August 2014 as well. This means that these versions don't receive any updates, which leaves them potentially insecure. [http://www.wpupdatephp.com](http://www.wpupdatephp.com)

**Note**: The plugin shows a notice when activated on the older PHP versions.
![Admin notice](/../screenshots/assets/img/admin-notice.png?raw=true)

## What is the priority option?
This plugin is basically a user interface for [the_content filter](https://codex.wordpress.org/Plugin_API/Filter_Reference/the_content). This filter allows themes and plugins to change (or add content to) post content before it's displayed. The default priority for filters is 10. Higher numbers correspond with later execution of adding additional content. This option allows you to display the additional content before or after other plugins that add content with this filter.

## HTML in additional content
The same html tags that are allowed in the post (text) editor can be used in additional content. If you want to use `<script>` tags in the content the user has to have the [unfiltered_html](https://codex.wordpress.org/Roles_and_Capabilities#unfiltered_html) capability (superadmin, admin and editor role). 

## Changing the metabox text strings
Let's say you want to use this plugin for authors to add shortcodes after the content on single post pages. All metabox text strings can be changed with the `additional_content_metabox_text` filter. Put this in your (child) theme's functions.php file or use it in a plugin.

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

## Options Display
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

## Additional content on other pages than single posts

Use the `additional_content_add_content` filter if you want additional content to display on other pages as singular post pages. Put this in your (child) theme's functions.php file or use it in a plugin. This example adds the additional content to home page posts.

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
