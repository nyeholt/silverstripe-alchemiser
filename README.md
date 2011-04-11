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

## Maintainer Contacts
* Marcus Nyeholt (<marcus@silverstripe.com.au>)

## Requirements
* SilverStripe 2.4+

## Project Links
* [GitHub Project Page](https://github.com/nyeholt/silverstripe-alchemiser)
* [Issue Tracker](https://github.com/nyeholt/silverstripe-alchemiser/issues)