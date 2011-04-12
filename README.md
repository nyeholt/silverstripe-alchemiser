# SilverStripe Alchemisable Module

Integrates with the [Alchemy API](http://alchemyapi.com/) to provide automatic
category, keyword and entity extraction for SilverStripe data objects.

## Basic Usage
To use the Alchemiser module you must first sign up for an Alchemy API key. Once
you have this you set it in your `_config.php` file, and apply the `Alchemisable`
extension to any objects you wish to perform metadata extraction on:

	// Sets the Alchemy API key.
	AlchemyService::set_api_key('<your key here>');
	
	// Applies entity extraction to SiteTree objects.
	Object::add_extension('SiteTree', 'Alchemisable');

If you wish metadata annotation to be fully automatic, all you need to do is set
the first parameter of the `Alchemisable` construction to `true`:

	Object::add_extension('SiteTree', 'Alchemisable(true)');

Once configured, an Alchemy tab will appear for content that can be indexed. 
Add some content to the page, and save it. Go to the Alchemy tab, and 
click the Analyze Content link. After a few seconds, a window will appear
with a list of identified keywords and other metadata. Simply check the box
next to those you wish to keep. 

## Maintainer Contacts

* Marcus Nyeholt <marcus@silverstripe.com.au>
* Andrew Short <andrew@silverstripe.com.au>

## Requirements

* SilverStripe 2.4+

## License

This module is licensed under the BSD license at http://silverstripe.org/BSD-license

It interfaces with the AlchemyAPI which is made available  under the terms at
http://www.alchemyapi.com/company/terms.html

Be aware that the Alchemy license requires the Alchemy logo and link remain in 
place. 

## Project Links
* [GitHub Project Page](https://github.com/nyeholt/silverstripe-alchemiser)
* [Issue Tracker](https://github.com/nyeholt/silverstripe-alchemiser/issues)
