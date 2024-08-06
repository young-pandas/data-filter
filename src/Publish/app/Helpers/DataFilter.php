<?php

namespace App\Helpers;

use YoungPandas\DataFilter\Contracts\DataFilterContract;
use YoungPandas\DataFilter\Helpers\DataFilter as BaseDataFilter;

/**
 * Summary of DataFilter
 * 
 * This class extends the base DataFilter class to provide the filters available in our package.
 * 
 * It is recommended to use this class to filter data in your application using DataFilterContract.
 * 
 * @requires YoungPandas\DataFilter\Contracts\DataFilterContract
 * 
 * Binding this class to the DataFilterContract in the AppServiceProvider will allow you to use the methods in this class.
 * 
 * Use of traits is recommended for adding custom methods to expand the functionality of this class.
 * 
 * Hope you enjoy using this package. If you have any questions or need help, please feel free to reach out to us at https://youngpandas.com/contact
 */
class DataFilter extends BaseDataFilter implements DataFilterContract
{
    // Add your custom traits or methods for filtering data here
}
