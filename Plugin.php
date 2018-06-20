<?php namespace Wwrf\MobileApp;


use App;
use Lang;
use Event;
use Backend;
use System\Classes\PluginBase;
use Wwrf\MobileApp\Models\App as AppModel;
use Felixkiss\UniqueWithValidator\ValidatorExtension;
use Wwrf\MobileApp\Classes\ProviderManager;
use RainLab\User\Models\User as UserModel;
use Wwrf\MobileApp\Models\Install as InstallModel;
use Wwrf\MobileApp\Models\Variant as VariantModel;
use Wwrf\MobileApp\Models\Settings as SettingsModel;
use RainLab\User\Controllers\Users as UsersController;
use Wwrf\MobileApp\Controllers\Apps as AppsController;
use Wwrf\MobileApp\Controllers\Installs as InstallsController;
/**
 * MobileApp Plugin Information File
 */
class Plugin extends PluginBase
{
    // plugin dependencies
    public $require = ['RainLab.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'MobileApp',
            'description' => 'Mobile App package for WWRF Transition Center',
            'author'      => 'Wwrf',
            'icon'        => 'icon-rocket'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConsoleCommand('create.restcontroller', 'Wwrf\MobileApp\Console\CreateRestController');
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        // Register ServiceProviders
        App::register('\Felixkiss\UniqueWithValidator\UniqueWithValidatorServiceProvider');
        // Registering the validator extension with the validator factory
        $this->app['validator']->resolver(function($translator, $data, $rules, $messages)
        {
            // Set custom validation error messages
            $messages['unique_with'] = $translator->get('uniquewith-validator::validation.unique_with');

            return new ValidatorExtension($translator, $data, $rules, $messages);
        });

        UserModel::extend(function($model){
            $model->hasMany['mobileuser_installs'] = ['Wwrf\MobileApp\Models\Install'];
        });

        InstallModel::extend(function($model){
            $model->belongsTo['user'] = ['RainLab\User\Models\User'];
        });

        InstallsController::extendListColumns(function($list, $model){
            if (!$model instanceof InstallModel)
                return;

            $list->addColumns([
                'user' => [
                    'label' => 'rainlab.user::lang.plugin.name',
                    'relation' => 'user',
                    'valueFrom' => 'id',
                    'default' => Lang::get('wwrf.mobileapp::lang.installs.unregistered')
                ]
            ]);

        });

        UsersController::extend(function($controller){
            $controller->addCss('/plugins/wwrf/mobileapp/assets/css/custom.css');

            if(!isset($controller->implement['Backend.Behaviors.RelationController']))
                $controller->implement[] = 'Backend.Behaviors.RelationController';
            $controller->relationConfig  =  '$/wwrf/mobileapp/models/relation.yaml';
        });

        UsersController::extendFormFields(function($form, $model, $context){
            if(!$model instanceof UserModel)
                return;

            if(!$model->exists)
              return;

            $form->addTabFields([
                'mobileuser_installs' => [
                    'label' => 'wwrf.mobileapp::lang.users.mobileuser_installs_label',
                    'tab' => 'Mobile',
                    'type' => 'partial',
                    'path' => '$/wwrf/mobileapp/assets/partials/_field_mobileuser_installs.htm',
                  ],

              ]);
        });

        AppsController::extendListColumns(function($list, $model){
            if (!$model instanceof VariantModel)
                return;

            $list->addColumns([
                'disable_registration' => [
                    'label' => 'wwrf.mobileapp::lang.variants.allow_registration_label',
                    'type' => 'switch'
                ]
            ]);

        });

        AppsController::extendFormFields(function($form, $model, $context) {
          if(!$model instanceof VariantModel)
              return;

          $form->getField('is_maintenance')->span = 'left';

          $form->addFields([
              'disable_registration' => [
                  'label' => 'wwrf.mobileapp::lang.variants.allow_registration_label',
                  'comment' => 'wwrf.mobileapp::lang.variants.allow_registration_comment',
                  'type' => 'checkbox',
                  'span' => 'right'
                ],
            ]);
        });

        Event::listen('backend.form.extendFields', function ($form) {
          
           if (!$form->model instanceof SettingsModel)
                return;

            $providers = ProviderManager::instance()->listProviderObjects();
            foreach($providers as $provider)
              {
                  $config = $provider->getFieldConfig();
                  if(!is_null($config))
                      $form->addFields($config);
              }
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Wwrf\MobileApp\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'wwrf.mobileapp.view_installs' => [
                'tab' => 'wwrf.mobileapp::lang.plugin.name',
                'label' => 'wwrf.mobileapp::lang.install.view_installs'
            ],
            'wwrf.mobileapp.manage_apps' => [
                'tab' => 'Mobile',
                'label' => 'Manage apps'
            ],
            'wwrf.mobileapp.access_users' => [
                'tab' => 'Mobile', 'label' => 'rainlab.user::lang.plugin.access_users'
            ],
            'wwrf.mobileapp.access_settings' => [
                'tab' => 'Mobile', 'label' => 'rainlab.user::lang.plugin.access_settings'
            ]
        ];
    }

    /**
     * Registers back-end settings for this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'apps' => [
                'label'       => 'Apps',
                'description' => 'Manage the apps and their variants.',
                'category'    => 'Mobile',
                'icon'        => 'icon-mobile',
                'url'         => Backend::url('wwrf/mobileapp/apps'),
                'order'       => 500,
                'keywords'    => 'apps builds variants',
                'permissions' => ['wwrf.mobileapp.manage_apps']
            ],
            'platforms' => [
                'label'       => 'Platforms',
                'description' => 'Manage the available platforms.',
                'category'    => 'Mobile',
                'icon'        => 'icon-th-large',
                'url'         => Backend::url('wwrf/mobileapp/platforms'),
                'order'       => 501,
                'keywords'    => 'apps builds variants',
                'permissions' => ['wwrf.mobileapp.manage_apps']
            ],
            'settings' => [
                'label'       => 'wwrf.mobileapp::lang.settings.name',
                'description' => 'wwrf.mobileapp::lang.settings.description',
                'category'    => 'Mobile',
                'icon'        => 'icon-user-plus',
                'class'       => 'Wwrf\MobileApp\Models\Settings',
                'order'       => 502,
                'permissions' => ['wwrf.mobileapp.access_settings'],
            ]
        ];
    }
    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'mobile' => [
                'label'       => 'wwrf.mobileapp::lang.plugin.name',
                'url'         => Backend::url('wwrf/mobileapp/installs'),
                'icon'        => 'icon-mobile',
                'permissions' => ['wwrf.mobile.*'],
                'order'       => 500,

                'sideMenu' => [
                    'installs' => [
                        'label'       => 'wwrf.mobileapp::lang.plugin.name',
                        'icon'        => 'icon-cloud-download',
                        'url'         => Backend::url('wwrf/mobileapp/installs'),
                        'order'       => 100,
                        'permissions' => ['wwrf.mobileapp.view_installs']
                    ]
                ]
            ],
        ];
    }

    public function registerReportWidgets()
    {
        if (AppModel::count() > 0) {
          return [
              'Wwrf\MobileApp\ReportWidgets\InstallsOverview'=>[
                  'label'   => 'App Installs Overview',
                  'context' => 'dashboard'
              ]
          ];
        } else return [];
    }

    /**
     * Registers mobile login providers implemented by this plugin.
     * The providers must be returned in the following format:
     * 
     * @return array    ['className' => 'alias']
     */
    public function registerMobileLoginProviders()
    {
        // note: the DefaultProvider does not require a provider suffix
        // it's there due to PHP class naming restriction to use the reserved Default keyword.
        return [
            'Wwrf\MobileApp\Providers\DefaultProvider' => 'default'
        ];
    }
}
