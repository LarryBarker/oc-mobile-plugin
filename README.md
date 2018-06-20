# [RESTful-Plugin](https://github.com/WWRFresource/MobilePlugin) #
RESTful API for Transition Center Website

## Introduction ##

This plugin allows you to create RESTful controllers.

In a nutshell, it adds the following scaffold command.
```
php artisan create:apicontroller Acme.Plugin ControllerName
```

However, it does more than just that, you have the ability to override the api actions (verbs) with your own logic. The first version of the plugin is minimal and only provides default behavior for `index` verb. I will add default behavior logic for the more common verbs (create, store, show, edit, update, destroy) in the next update. Most RESTful APIs use custom logic and hide several fields so usually the behavior should be overridden than using the default behavior.

Feel free to make PRs to the github repository should you want to contribute. If you have feature requests use the issue tracker.

## Adding RESTful API to an exisiting controller (w/o scaffolding) ##

RESTful API logic can be combined with the controlled created by create:controller. All you need to do is add the RESTful API behavior to the $implement property of your controller class. Be sure to define a $restConfig property, and the value set to the YAML file for configuring the behavior.
```
namespace Acme\Blog\Controllers;

class Categories extends \Backend\Classes\Controller
{
    public $implement = ['Lmbdev.WwrfApi.Behaviors.ApiController'];

    public $restConfig = 'config_rest.yaml';
}
```

The behavior is configured using the config_rest.yaml file. Place this file in the controller's views directory. A basic example is below:
```
# ===================================
#  Rest Behavior Config
# ===================================

# Allowed Rest Actions
allowedActions:
  - store

# Model Class name
modelClass: Acme\Blog\Models\Post

# Verb Method Prefix
prefix: api
```

The prefix prevents controller actions from conflicting. Typical actions include: index, update, and preview (behaviors found in the ListController and FormController). RESTful methods should not conflict with these methods. You need to override the default controller logic by writing a method with the prefix signature, followed by the camel case verb name. For example, **public function apiStore()**.

Finally, create a routes.php file. Here’s the general template:
```
<?php

Route::group(['prefix' => 'api/v1'], function () {
    Route::resource('posts', 'Acme\Blog\Controller\Posts');
});
```
Although this method will work, it is generally recommended to use the scaffolding command.

## Props ##

#### October CMS ####
[OctoberCMS](http://octobercms.com) for OctoberCMS.

## Introduction ##

This plugin enables you to manage mobile applications. You can add your iOS, Android and other store app listing into the backend. It also lets you monitor individual app installs by adding some code on the client-side (see video or documentation).

TODO: Add tutorial and demonstration of the plugin.

You can manually create **Android** using Platforms settings directly.

## Features ##
* Add multiple variants and platforms.

## Coming Soon ##
* Add custom fields and track those values.

## API Details ##

### Client-side Integration ###

Once you have created the plugin entry in the backend, you can do the client side integration. On the mobile app, you will have to make a call to the backend using the REST API that has been provided. This is how it works:

#### POST /installs ####

**Resource URL:** [/api/v1/installs](/api/v1/installs) [![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/1f9efcfa94c93810e739)

| Parameters | Description
------------- | -------------
instance_id  | A unique ID, such as device ID or an ID generated using Google’s Instance ID API. Eg. 573b61d82b4e46e3
package  | The package name of the application. Must match the name specified in the variants. Eg. com.acme.myapp

Mobile Front-end user management for WWRF Transition Center.

# About Plugin #

This plugin is similar to the RainLab.User plugin except it’s built to work mainly with mobile front-ends. It exposes RESTful API nodes that enable interaction with the backend thereby allowing users to sign up and login as front-end users.

TODO: Add tutorial and demonstration of the plugin.

#### Features ####
* Completely extensible.
* Compatible with the multiple mobile app types.

#### Coming Soon ####
* Dashboard widget to provide user signup and register analytics.
* Forgot password and signing out nodes (Currently, you can do it through website only).
* Multiple Auth Schemes for better security.

### Requirements

* [Rainlab.User plugin](http://octobercms.com/plugin/rainlab-user).

Most of the [RainLab User Documentation](https://octobercms.com/plugin/rainlab-user#documentation) applies to this as well, since it's extended over that.

### Client-side Integration ###

**Note:** Make sure you have finished the client side integration for the mobile plugin first. Any route calls to this plugin’s nodes MUST happen AFTER the **installs** route request of the mobile plugin. Otherwise, an error may be thrown.
On the mobile app, you will have to make a call to the backend using the REST API that has been provided. Right now, the API works like this:

#### POST /account/signin ####

**Resource URL:** [/api/v1/account/signin](/api/v1/account/signin) [![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/997ae8398f934757e196)

 | Parameters | Description
------------- | -------------
instance_id  | A unique ID, such as device ID or an ID generated using Google’s Instance ID API. Eg. 573b61d82b4e46e3
package  | The package name of the application. Must match the name specified in the variants. Eg. com.acme.myapp
email (or) username | The login attribute for the user attempting to sign in.
password | The password for the user attempting to sign in.

#### POST /account/register ####

**Resource URL:** [/api/v1/account/register](/api/v1/account/register) [![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/997ae8398f934757e196)

 | Parameters | Description
------------- | -------------
name | The name of the user attempting registration.
email | The email for the user attempting registration.
username (optional) | The username for the user attempting registration.
password | The password for the user attempting registration.
