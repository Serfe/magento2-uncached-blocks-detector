# Serfe Uncached Blocks Detector - Magento 2

[![Latest Stable Version](https://poser.pugx.org/serfe/module-uncacheableblockdetector/v)](https://packagist.org/packages/serfe/module-uncacheableblockdetector) [![Total Downloads](https://poser.pugx.org/serfe/module-uncacheableblockdetector/downloads)](https://packagist.org/packages/serfe/module-uncacheableblockdetector) [![License](https://poser.pugx.org/serfe/module-uncacheableblockdetector/license)](https://packagist.org/packages/serfe/module-uncacheableblockdetector) [![PHP Version Require](https://poser.pugx.org/serfe/module-uncacheableblockdetector/require/php)](https://packagist.org/packages/serfe/module-uncacheableblockdetector)

The propouse of this module is to intercept the normal workflow of page rendering of Magento 2 and force the exit the rendering of the page if a block that is uncacheable is reached. If the block is uncacheable, the wholepage get it's cache disabled and cannot be handled by file cache or Varnish cache.

This is usefull to debug any module that might contain the following entry inside one of it's layout definitions (catalog_product_view.xml for example):

```xml
  ....
  <block name="thridpartblock" template="Vendor_Modulename::confirm.phtml" cacheable="false">
  ....
```

## In which cases you will see this module required?

This module is good to help troubleshoot when your any page of the system is not having it's cache generated by Magento (in Varnish or in Disk Cache) and you don't know which is the block that is making this behaviour to happen.

Just install this module as developer dependency and configure it from the console or inside the developer section in the backend admin, refresh and see the block name popup as an error on your frontend section.

This only works on development mode. On production mode, this module auto-disable itself to avoid possible issues.

## Install

```bash
composer install serfe/module-uncheableblockdetector --dev
```

```bash
php bin/magento setup:upgrade
```

## Configuration

The module allow to enable or disable the feature on the backend section when in developer mode.

Go into Backend > Stores > Configuration > Advanced >  Developer > 

### Using the console

If you want to have the setup been done using the console, run the following commands:

Enable the module detection with:

```bash
php bin/magento config:set --lock-env dev/cache_detector/enabled 1
```

To disable the exception throwing on the first block detected:

```bash
php bin/magento config:set --lock-env dev/cache_detector/die 0
```

When using this last option, you will see the detected entries into the debug.log file.

## About Us

[Serfe](https://www.serfe.com/?utm_medium=referral_profile&utm_source=github&utm_campaign=115959) develops complete e-commerce solutions based on Magento 2 system and other platforms. Feel free to check it on the website.

### Contributing

We welcome any contribution to this module. Fork the repo, make your changes and create a pull request with your changes.
