# DisableLog2 for Magento 2

<!-- badges.specs.start -->
![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/yireo/magento2-disablelog2)
<!-- badges.specs.end -->

This module disables the visitor and customer logging of the Magento core.

## Usage
Just install the module and it will disable some logging.

The following logs will be **disabled** when the `Yireo_DisableLog2` module is installed:

- The customer logger `Magento\Customer\Model\Logger`, which is used by the observers `LogLastLoginAtObserver` (event `customer_login`) and `LogLastLogoutAtObserver` (event `customer_logout`) to store the last login and logout time. No rows are added to the table `customer_log`.
- The saving of visitors via `Magento\Customer\Model\ResourceModel\Visitor`. No rows are added to the table `customer_visitor`.

Note that this disables the feature of **Customers > Now Online**, which is removed from the Admin Panel menu. The page itself is only hidden from the menu, not removed: admins with access to it can still open it via its URL. It then shows the visitors that were logged before this module was installed, until the daily cron job `visitor_clean` removes them.

Also, the **Last Logged In** information of customers is no longer updated.

## Security: lost audit trail
The last login and logout times in `customer_log` are also useful when investigating security issues, like a hijacked customer account ("did this customer really log in last night?"). Without this module you get that information for free. If you need it, you can keep this logging:

**Stores > Configuration > Customers > Customer Configuration > Yireo DisableLog2 > Keep Last Login and Logout Times** (config path `customer/disable_log2/keep_customer_log`, default **No**)

With this setting enabled, only the visitor logging (`customer_visitor`) is disabled.

Under Magento 1, this module also prevented information to be logged to `report_*` tables. This has changed in Magento 2: You can disable the setting `reports/options/enabled` (**Reports > General Options > Enable Reports**) for this.

## Does this improve performance?
A little bit. The gain depends on your shop:

- Writes to `customer_log` only happen when a customer logs in or logs out. Skipping these will hardly be noticeable.
- Since Magento 2.3, the core already skips saving the visitor for safe requests (like `GET`), unless the session ID has changed. So, writes to `customer_visitor` are only skipped for `POST` requests (adding to the cart, logging in, AJAX calls), for new sessions and for logins.
- Both tables do not grow anymore, so there is no need to clean them regularly.

If you are not using the **Now Online** feature, there is no need to keep these logs. But do not expect a huge speed boost.

## Visitor ID
Because the visitor is not saved, there is no auto-increment visitor ID anymore. Instead, a visitor ID (between 1 and 4294967295) is derived from an HMAC-SHA256 hash of the session ID, keyed with the encryption key of your shop (`crypt/key` in `app/etc/env.php`). Because of this key, the visitor ID of a session can not be calculated from outside of your shop. If the encryption key changes, the visitor IDs of existing guest sessions change as well.

Some features rely on this ID for guests, like the product compare list and the recently viewed products. Because of the limited range of 32 bits (the size of the `visitor_id` columns in the Magento core), two guest sessions might get the same visitor ID. The chance grows with the number of guests: With 100,000 guest sessions per day, roughly one pair of sessions per day shares the same ID. These guests then share their compare list and recently viewed products, and when one of them logs in, these items are copied to the customer account. Because the visitor table remains empty, the daily cron job `visitor_clean` removes all guest data of this kind every night, which limits this to a single day.

We also recommend to enable `session.use_strict_mode = 1` in your PHP configuration, so that a browser can not choose its own session ID.

If a visitor does not have a session ID (which normally does not happen in the storefront), the visitor is saved as usual, so that it gets a unique visitor ID.

## Installation
### Instructions for using composer
Use composer to install this extension. First make sure that Magento is installed via composer, and that there is a valid `composer.json` file present. Next, install our module using the following command:

    composer require yireo/magento2-disablelog2

Next, enable the new module in Magento:

    ./bin/magento module:enable Yireo_DisableLog2
    ./bin/magento setup:upgrade

Check whether the module is successfully installed:

    ./bin/magento module:status Yireo_DisableLog2

Done.

### Instructions for manual copy
We recommend `composer` to install this package. However, if you want a manual copy instead, these are the steps:
* Copy all files from the root of this repository (or of a downloaded release) to the folder `app/code/Yireo/DisableLog2` of your site, so that the file `app/code/Yireo/DisableLog2/registration.php` exists. The folders `Test/` and `.github/` are not needed.
* Run `bin/magento module:enable Yireo_DisableLog2`
* Run `bin/magento setup:upgrade`
* Run `bin/magento cache:flush`
* Check the installation with `bin/magento module:status Yireo_DisableLog2`
* Done

If you get the error `Unknown module(s): 'Yireo_DisableLog2'`, the files are copied to the wrong location: Make sure that the file `registration.php` is placed directly in the folder `app/code/Yireo/DisableLog2/` and not in a subfolder.

## Backgrounds
The Magento 2 core has an excellent logging system: Every time that a customer logs in or out, his/her details are logged. This might lead to a big database size over time. Not everybody wants this: There are
numerous threads discussing the periodic cleaning of those tables using the SQL statement `TRUNCATE`. But this is only part of the problem.

Besides filling up the database, this logging also causes extra SQL `INSERT` and `UPDATE` statements. In Magento 2, this mostly happens for `POST` requests and logins (see above).

Now, if you're relying on those Magento 2 log statistics, you'll just have to accept this as a fact. However, if you're using other external services like Piwik or GoogleAnalytics, this Magento logging is actually
an unwanted feature.

### Our extension
Our extension disables this logging, by using DI plugins to skip the database writes.

### Technical specifications
This extension makes use of interceptors (aka plugins) to override various Magento 2 core classes. The customer logger plugin is configured globally in `etc/di.xml`. The visitor plugin is only configured for the storefront in `etc/frontend/di.xml`, so that visitors saved in other areas (like the Admin Panel, the API or the CLI) are still saved.

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
