<?php namespace Wwrf\MobileApp\Models;

use Model;

/**
 * App Model
 */
class App extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $rules = [
        'name' => 'required',
        'description' => 'required',
        'maintenance_message' => 'required'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wwrf_mobile_apps';

    /**
     * @var array Guarded fields
     */
    protected $guarded = [];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'variants' => ['Wwrf\MobileApp\Models\Variant'],
    ];

}
