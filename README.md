# DisableLog2 for Magento 2

This module disables various log attempts of the Magento core, improving performance slightly.

Requirements:
* Magento 2.0.0 Stable or higher

# Instructions for using composer

Use composer to install this extension. First make sure that Magento is installed via composer, and that there is a valid `composer.json` file present.

Next, install our module using the following command:

    composer require yireo/magento2-disablelog2

Next, enable the new module in Magento:

    ./bin/magento module:enable Yireo_DisableLog2
    ./bin/magento setup:upgrade

Check whether the module is succesfully installed in **Admin > Stores >
Configuration > Advanced > Advanced**.

Done.

# Instructions for manual copy
We recommend `composer` to install this package. However, if you want a manual copy instead, these are the steps:
* Upload the files in the `source/` folder to the folder `app/code/Yireo/DisableLog2` of your site
* Run `php -f bin/magento module:enable Yireo_DisableLog2`
* Run `php -f bin/magento setup:upgrade`
* Flush the Magento cache
* Done

## Usage
Just install the module and it will disable some logging.

The following logs will be **disabled** when the `Yireo_DisableLog` module is installed:
- In the customer module the `LogLastLoginAtObserver` class logs a succesfull login, responding to the event `customer_login`.
- In the customer module the `LogLastLogoutAtObserver` class logs a succesfull logout, responding to the event `customer_logout`.

@todo: More events are disabled.

The following tables are supposed to be free from new insertions:
- `customer_log`
- `customer_visitor`


## FAQ
### Does this have anything to do with the logs being written to `var/log`?
No.

