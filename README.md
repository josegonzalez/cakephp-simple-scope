[![Build Status](https://img.shields.io/travis/josegonzalez/cakephp-simple-scope/master.svg?style=flat-square)](https://travis-ci.org/josegonzalez/cakephp-simple-scope) 
[![Coverage Status](https://img.shields.io/coveralls/josegonzalez/cakephp-simple-scope.svg?style=flat-square)](https://coveralls.io/r/josegonzalez/cakephp-simple-scope?branch=master) 
[![Total Downloads](https://img.shields.io/packagist/dt/josegonzalez/cakephp-simple-scope.svg?style=flat-square)](https://packagist.org/packages/josegonzalez/cakephp-simple-scope) 
[![Latest Stable Version](https://img.shields.io/packagist/v/josegonzalez/cakephp-simple-scope.svg?style=flat-square)](https://packagist.org/packages/josegonzalez/cakephp-simple-scope) 
[![Documentation Status](https://readthedocs.org/projects/cakephp-simple-scope/badge/?version=latest&style=flat-square)](https://readthedocs.org/projects/cakephp-simple-scope/?badge=latest)
[![Gratipay](https://img.shields.io/gratipay/josegonzalez.svg?style=flat-square)](https://gratipay.com/~josegonzalez/)

# cakephp-simple-scope

A simple cakephp behavior for scoping finds

## Background

I didn't want to write a bunch of custom finds so I just defined an array and made a behavior to interact with that array. Yep.

## Requirements

* CakePHP 2.x

## Installation

_[Using [Composer](http://getcomposer.org/)]_

Add the plugin to your project's `composer.json` - something like this:

	{
		"require": {
			"josegonzalez/cakephp-simple-scope": "dev-master"
		}
	}

Because this plugin has the type `cakephp-plugin` set in it's own `composer.json`, composer knows to install it inside your `/Plugins` directory, rather than in the usual vendors file. It is recommended that you add `/Plugins/SimpleScope` to your .gitignore file. (Why? [read this](http://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md).)

_[Manual]_

* Download this: [https://github.com/josegonzalez/cakephp-simple-scope/zipball/master](https://github.com/josegonzalez/cakephp-simple-scope/zipball/master)
* Unzip that download.
* Copy the resulting folder to `app/Plugin`
* Rename the folder you just copied to `SimpleScope`

_[GIT Submodule]_

In your app directory type:

	git submodule add git://github.com/josegonzalez/cakephp-simple-scope.git Plugin/SimpleScope
	git submodule init
	git submodule update

_[GIT Clone]_

In your plugin directory type

	git clone git://github.com/josegonzalez/cakephp-simple-scope.git SancSimpleScopetion

### Enable plugin

In 2.0 you need to enable the plugin your `app/Config/bootstrap.php` file:

		CakePlugin::load('SimpleScope');

If you are already using `CakePlugin::loadAll();`, then this is not necessary.

## Usage

Attach the behavior to your AppModel:

```php
<?php
App::uses('Model', 'Model');

class AppModel extends Model
{
    public $actsAs = array('SimpleScope.Scope');
}
?>
```

Then define some scopes in your model:

```php
<?php
App::uses('AppModel', 'Model');

class User extends AppModel
{
    public $scopes = array(
        'active_admin' => array(
            'name' => 'Active admin users',
            'find' => array(
                'type' => 'list',
                'virtualFields' => array(
                    'fullname' => "CONCAT(User.firstname, ' ', User.lastname)"
                ),
                'options' => array(
                    'fields' => array('User.id', 'User.fullname'),
                    'conditions' => array('User.role LIKE' => '%admin%'),
                    'order' => array('User.fullname'),
                ),
            ),
        ),
    );
}
?>
```

And then execute it:

```php
<?php
$activeUsers = $this->User->scopedFind('active_admin');
?>
```

You can alternatively use it as a custom model find:

```php
<?php
$activeUsers = $this->User->find('active_admin');
?>
```

You can also get a list of scopes:

```php
<?php
scopes = $this->User->scopes();
?>
```

Scopes:

- *require* a `name` string
- *optionally* use a `find` array

Scope `find` fields:

- *require* a `type` string
- *require* an `options` array
- *optionally* use a `virtualFields` field

## Todo

* Unit Tests

## License

The MIT License (MIT)

Copyright (c) 2014 Jose Diaz-Gonzalez

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
