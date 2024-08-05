# young-pandas/data-filter

This package helps to filter data using json files.

## Installation

To install the package, run the following command:

```sh
composer require young-pandas/data-filter
```

## Publish Vendor

```sh
php artisan vendor:publish --provider="YoungPandas\DataFilter\Providers\FilterServiceProvider"
```

## Usage

Helps to define filters in json files and filter data using those filters.

Project managers can define filters and developers can use those filters to manage requests and responses.

It makes a perfect separation of concerns between project managers and developers.

This package enable the support for End to End encyption.
