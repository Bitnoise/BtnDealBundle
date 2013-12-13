BtnDealBundle
=============

=============

### Step 1: Add BtnDealBundle in your composer.json (private repo)

```js
{
    "require": {
        "bitnoise/deal-bundle": "dev-master",
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:Bitnoise/BtnDealBundle.git"
        }
    ],
}
```

### Step 2: Enable the bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Btn\DealBundle\BtnDealBundle(),
    );
}
```

### Setp 3: Update parameters
``` yml
// app/config/parameters.yml
parameters:
    web_root: '%kernel.root_dir%/../web/'
    deal.seller:
        company:    'Company'
        street:     'Street'
        postCode:   '00-000'
        city:       'Poznań'
        nip:        '000-000-00-00'
```

### Step 4: Update your database schema

``` bash
$ php app/console doctrine:schema:update --force
```
