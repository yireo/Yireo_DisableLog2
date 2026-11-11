# DisableLog2 for Magento 2

<!-- badges.specs.start -->
![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/yireo/magento2-disablelog2)
<!-- badges.specs.end -->

This module disables various log attempts of the Magento core, improving performance slightly.

## Usage
Just install the module and it will disable some logging.

The following logs will be **disabled** when the `Yireo_DisableLog` module is installed:

- In the customer module the `LogLastLoginAtObserver` class logs a succesfull login, responding to the event `customer_login`.
- In the customer module the `LogLastLogoutAtObserver` class logs a succesfull logout, responding to the event `customer_logout`.

The following tables are supposed to be free from new insertions:
- `customer_log`
- `customer_visitor`

Note that this disables the feature of **Customers -> Now Online**.

Under Magento 1, this module also prevented information to be logged to `report_*` tables. This has changed in Magento 2: You can disable the setting `reports/options/enabled` (**Reports > General Options > Enable Reports**) for this.

## Installation
### Instructions for using composer
Use composer to install this extension. First make sure that Magento is installed via composer, and that there is a valid `composer.json` file present. Next, install our module using the following command:

    composer require yireo/magento2-disablelog2

Next, enable the new module in Magento:

    ./bin/magento module:enable Yireo_DisableLog2
    ./bin/magento setup:upgrade

Check whether the module is succesfully installed in **Admin > Stores >
Configuration > Advanced > Advanced**.

Done.

### Instructions for manual copy
We recommend `composer` to install this package. However, if you want a manual copy instead, these are the steps:
* Upload the files in the `source/` folder to the folder `app/code/Yireo/DisableLog2` of your site
* Run `php -f bin/magento module:enable Yireo_DisableLog2`
* Run `php -f bin/magento setup:upgrade`
* Flush the Magento cache
* Done

## Backgrounds
The Magento 2 core has an excellent logging system: Every time that a customer logs in or out, his/her details are logged. This might lead to a big database size over time. Not everybody wants this: There are
numerous threads discussing the periodic cleaning of those tables using the SQL statement `TRUNCATE`. But this is only part of the problem.

Besides filling up the database, this logging also causes various extra SQL `INSERT` statements to be made for every pageload. If you have a busy site, this certainly has an impact on database-performance.

Now, if you're relying on those Magento 2 log statistics, you'll just have to accept this as a fact. However, if you're using other external services like Piwik or GoogleAnalytics, this Magento logging is actually
an unwanted feature.

### Our extension
Our extension gives you the power to disable all this Magento 2 logging: Either for all requests being made, or only for search-robots. It disables logging, but also disables the autoincrement flag for certain
search patterns: The latter is interesting if you are interested in what your real-life customers are actually searching for, while by default search engines mess up these statistics.

### Technical specifications
This extension makes use of interceptors (aka plugins) to override various Magento 2 core classes. See the `etc/di.xml` file for details.

## PWA compatibility
This extension changes core behaviour and has no need to become compatible with PWA.

## FAQ
### Does this have anything to do with the logs being written to `var/log`?
No.

## Current status

<!-- badges.test.start -->
![Static Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_DisableLog2/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_DisableLog2/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_DisableLog2/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_DisableLog2/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_DisableLog2/compile.yml?label=compile)
<!-- badges.test.end -->
