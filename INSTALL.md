# Installation
## Instructions for using composer
Use composer to install this extension. First make sure that Magento is installed via composer, and that there is a valid `composer.json` file present. Next, install our module using the following command:

    composer require yireo/magento2-disablelog2

Next, enable the new module in Magento:

    ./bin/magento module:enable Yireo_DisableLog2
    ./bin/magento setup:upgrade

Check whether the module is succesfully installed in **Admin > Stores >
Configuration > Advanced > Advanced**.

Done.

## Instructions for manual copy
We recommend `composer` to install this package. However, if you want a manual copy instead, these are the steps:
* Upload the files in the `source/` folder to the folder `app/code/Yireo/DisableLog2` of your site
* Run `php -f bin/magento module:enable Yireo_DisableLog2`
* Run `php -f bin/magento setup:upgrade`
* Flush the Magento cache
* Done

