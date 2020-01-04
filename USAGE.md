# Usage
Just install the module and it will disable some logging.

The following logs will be **disabled** when the `Yireo_DisableLog` module is installed:

- In the customer module the `LogLastLoginAtObserver` class logs a succesfull login, responding to the event `customer_login`.
- In the customer module the `LogLastLogoutAtObserver` class logs a succesfull logout, responding to the event `customer_logout`.

The following tables are supposed to be free from new insertions:
- `customer_log`
- `customer_visitor`

Note that this disables the feature of **Customers -> Now Online**.

Under Magento 1, this module also prevented information to be logged to `report_*` tables. This has changed in Magento 2: You can disable the setting `reports/options/enabled` (**Reports > General Options > Enable Reports**) for this.
