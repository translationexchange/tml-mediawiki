<p align="center">
  <img src="https://raw.github.com/tr8n/tr8n/master/doc/screenshots/tr8nlogo.png">
</p>

Tr8n Plugin for MediaWiki
=====================

This plugin allows you to translate MediaWiki sites using Tr8n for PHP and manage translations on Tr8nHub service.

[![Latest Stable Version](https://poser.pugx.org/tr8n/tr8n-mediawiki-plugin/v/stable.png)](https://packagist.org/packages/tr8n/tr8n-mediawiki-plugin)
[![Dependency Status](https://www.versioneye.com/user/projects/52e4b4a3ec1375b57600000c/badge.png)](https://www.versioneye.com/user/projects/52e4b4a3ec1375b57600000c)


Installation
==================

Tr8n WordPress Plugin can be installed using the composer dependency manager. If you don't already have composer installed on your system, you can get it using the following command:

        $ cd YOUR_APPLICATION_FOLDER
        $ curl -s http://getcomposer.org/installer | php


Create composer.json in the root folder of your application, and add the following content:

        {
            "minimum-stability": "dev",
            "require": {
                "composer/installers": "v1.0.6",
                "tr8n/tr8n-mediawiki-plugin": "dev-master"
            }
        }

This tells composer that your application requires tr8n-mediawiki-plugin to be installed.

Now install Tr8n MediaWiki plugin by executing the following command:


        $ php composer.phar install


Integration
==================

To enable Tr8n plugin inside MediaWiki, you first need to visit https://tr8nhub.com and register your application.

Once you have created a new application, go to the security tab in the application administration section and copy your application key and secret.

<img src="http://wiki.tr8nhub.com/images/thumb/f/f7/Application_Settings.png/800px-Application_Settings.png">


Now open your LocalSettings.php file and add the following lines:

        require_once( "$IP/extensions/tr8n-mediawiki-plugin/Tr8n.php" );

        $wgTr8nServerUrl =  "https://tr8nhub.com";
        $wgTr8nApplicationKey =  "YOUR_APPLICATION_KEY";
        $wgTr8nApplicationSecret =  "YOUR_APPLICATION_SECRET";


Now you are ready to invite translators and start translating your MediaWiki site.


To learn more about how to integrate and use this plugin, please visit:

http://wiki.tr8nhub.com/index.php?title=Tr8n_MediaWiki_Plugin


Where can I get more information?
==================

* Register on Tr8nHub.com: https://tr8nhub.com

* Read Tr8nHub's documentation: http://wiki.tr8nhub.com

* Visit Tr8nHub's blog: http://blog.tr8nhub.com

* Follow Tr8nHub on Twitter: https://twitter.com/Tr8nHub

* Connect with Tr8nHub on Facebook: https://www.facebook.com/pages/tr8nhubcom/138407706218622

* If you have any questions or suggestions, contact us: feedback@tr8nhub.com

