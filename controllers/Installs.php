<?php namespace Wwrf\MobileApp\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Wwrf\MobileApp\Models\Variant;
use Wwrf\MobileApp\Widgets\Dropdown;

/**
 * Installs Back-end Controller
 */
class Installs extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    protected $dropdownWidget;

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Wwrf.MobileApp', 'mobileapp', 'installs');

        $this->dropdownWidget = new Dropdown($this);
        $this->dropdownWidget->alias = 'variantsDropdown';
        $this->dropdownWidget->setListItems(Variant::lists('description','id'));
        $this->dropdownWidget->setErrorMessage('App list empty. First add apps from the settings.');
        $this->dropdownWidget->bindToController();
    }

    public function listExtendQuery($query)
    {
        $query->withVariant($this->dropdownWidget->getActiveIndex());
    }
}
