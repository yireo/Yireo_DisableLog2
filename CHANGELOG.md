# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 25 November 2026
### Added
- New setting "Keep customer login and logout times" (Customers > Customer Configuration > Yireo DisableLog2, default No) to keep the login audit trail while visitor logging stays disabled
- PHP 8.5 support

### Changed
- The visitor plugin now only applies to the frontend, so the Admin Panel, API, cron and CLI save visitors as in the core
- README now explains what is disabled, the realistic performance gain, the visitor ID risks and the correct manual installation

### Removed
- PHP 8.1 support

### Fixed
- Guest data (compare list, recently viewed products) of another visitor could be read or planted by choosing a session ID that maps to that visitor ID; the visitor ID is now keyed with the encryption key
- Deprecation notices and a visitor ID of 0 for session IDs containing '-' or ','
- Visitors without a session ID all shared visitor ID 0 and thereby one compare list
- Undefined method error when chaining calls on the result of the customer logger log()
- Missing module dependencies on Magento_Backend, Magento_Config and Magento_Store

## [0.0.7] - 8 March 2022
### Added
- Removing menu item to prevent client confusion (@edward-simpson)

## [0.0.6] - 29 July 2020
### Added
- Magento 2.4 compatibility

## [0.0.5] - Undocumented
