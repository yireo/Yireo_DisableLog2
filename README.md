# DisableLog2 for Magento 2

This module disables various log attempts of the Magento core, improving performance slightly.

Requirements:
* Magento 2.0.0 Stable or higher

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

