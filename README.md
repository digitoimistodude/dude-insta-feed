| :bangbang: | **This repository is no longer actively maintained. The plugin still works, but does not receive any further updates other than community contributed fixes.**  |
|:------------:|:------------------------------------------------------------------------------------------------------------------------------------------------------------------------|

# Dude insta feed
WordPress plugin to get latest images from Instagram user feeds.

Basically this plugin fetches images from Instagram, saves those to transient and next requests will be served from there. After transient has expired and deleted, new fetch from Instagram will be made and saved to transient. This implements very simple cache.

Handcrafted with love at [Digitoimisto Dude Oy](http://dude.fi), a Finnish boutique digital agency in the center of Jyväskylä.

## NOTE!

**DEPRECATED! This plugin does not work so well after Instagram has tightened their API usage. Better plugin to use is [ig-graph-feed](https://github.com/jake-101/ig-graph-feed) by Jake Peterson or our [image-user-feed](https://github.com/digitoimistodude/image-user-feed) which bypass the official API.**

## Table of contents
1. [Please note before using](#please-note-before-using)
2. [License](#license)
  1. [Legal](#legal)
3. [Usage](#usage)
  1. [Usage example for displaying user feed](#usage-example-for-displaying-user-feed)
  2. [Limiting feed items](#limiting-feed-items)
4. [Hooks](#hooks)
5. [Composer](#composer)
6. [Contributing](#contributing)

## Please note before using
This plugin is not meant to be "plugin for everyone", it needs at least some basic knowledge about php and css to add it to your site and making it look beautiful.

This is a plugin in development for now, so it may update very often.

## License
Dude insta feed is released under the GNU GPL 2 or later.

#### Legal
Please read Instagram's [TOC](https://help.instagram.com/478745558852511) to determine can you use images and what restrictions there may be.

## Usage
This plugin does not have settings page or provide anything visible on front-end. So it's basically dumb plugin if you don't use any filters listed below.

Only mandatory filter to use is `dude-insta-feed/access_token/user=$userid`.

Get images by calling function `dude_insta_feed()->get_user_images()`, pass user id as a only argument. User's id can be obtained with [this tool](http://www.otzberg.net/iguserid/).

### Usage example for displaying user feed

1. Go to [instagram.com/developer](https://www.instagram.com/developer) and register an application for your WordPress site. **Please note** that application must be registered with that exact user account you want to pull images from!
2. Disable Implicit oAuth2 from security for next step to work.
3. Generate access token by going to [this url](https://instagram.com/oauth/authorize/?client_id=[CLIENT_ID_HERE]&redirect_uri=http://localhost&response_type=token) and adding your CLIENT ID and REDIRECT URIs to their appropriate places and click **Authorize**.
5. Enable Implicit oAuth2 back.
4. Get the code after `#access_token=` in the following URL and add this snippet to **functions.php**. Your user ID is in the beginning of access token, just before tge dot.

```php
/**
 * Dude Instagram hashtag feed.
 */
add_filter( 'dude-insta-feed/access_token/user=USERID_HERE', function() { return 'ACCESS_TOKEN_HERE'; } );
```

6. Add this loop to wherever you want to display your images:

```php
<?php
$instagram_feed = dude_insta_feed()->get_user_images( 'USERID_HERE' );
foreach ($instagram_feed['data'] as $item) : ?>
    <img src="<?php echo $item['images']['standard_resolution']['url']; ?>" alt="" />
<?php endforeach; ?>
```

### Limiting feed items 

```php
/**
 * Limit Instagram feed items
 */
add_filter( 'dude-insta-feed/user_images_parameters', function( $parameters ) {
  $parameters['count'] = '6';
  return $parameters;
} );
```

## Hooks

All the settings are set with filters, and there is also few filters to change basic functionality and manipulate data before caching.

##### `dude-insta-feed/access_token/user=$userid`
Because Instagram's API restrictions, every user needs their own access token. Access token can be generated following [this instruction](https://www.instagram.com/developer/authentication/).

Set the access token for specific user. Passed arguments are default access_token and user id.

Defaults to empty string.

##### `dude-insta-feed/user_images_transient`
Change name of the transient for user images, must be unique for every user. Passed arguments are default name and user id.

Defaults to `dude-insta-user-$userid`.

##### `dude-insta-feed/user_images_parameters`
Modify api call parameters, example count of images. Only passed argument is array of default parameters.

Defaults to access_token defined for user and image count of five.

##### `dude-insta-feed/user_images`
Manipulate or use data before it's cached to transient. Only passed argument is array of images.

##### `dude-insta-feed/user_images_lifetime`
Change image cache lifetime. Only passed argument is default lifetime in seconds.

Defaults to 600 (= ten minutes).

## Composer

To use with composer, run `composer require digitoimistodude/dude-insta-feed dev-master` in your project directory or add `"digitoimistodude/dude-insta-feed":"dev-master"` to your composer.json require.

## Contributing
If you have ideas about the plugin or spot an issue, please let us know. Before contributing ideas or reporting an issue about "missing" features or things regarding to the nature of that matter, please read [Please note section](#please-note-before-using). Thank you very much.
