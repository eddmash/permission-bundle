# Symfony 4 Permission Bundle

Installation
============
```
composer require eddmash/permission-bundle
```

Configuration
=============
```
eddmash_permission:
    user_entity: 'App\Entity\User' # the user entity used by the app
    fetch_admin_callback: 'fetchAdmin' # the method in the specified `user_entity` repositoy
                                       # to use when get the application root admin whose 
                                       # granted all permissions.
```

Setting entity permission
=========================
Use the `@AccessRights` to add permission the will be available to specific models

```
<?php
        
namespace App\Entity;

use Eddmash\PermissionBundle\Entity\Annotations\AccessRights;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @AccessRights(label="Account", tag="account")
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{
    /**
     * @ORM\Column(type="string", length=32,nullable=true)
     */
    private $account_number;

    /**
     * @ORM\Column(type="datetime")
     *
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $opening_date;
}
```

Create permission
=================
Run the following command to create the permissions in the database
```
php bin/console eddmash:permission
```

for the `Account` entity above, it will have 4 permission on the database
  
    - account-can_add
    - account-can_update
    - account-can_view
    - account-can_delete

Checking permission
===================
This permission can be used with `is_granted` by replacing the hypen to underscore,so 
`account-can_view` becomes `account_can_view`
    
Checking if user has permission
-------------------------------
on the controller you can use `is_granted`
```
<?php

class Account extends AbstractController
{
    /**
     * @Route("/account/{id}", name="account_information", methods={"GET"})
     * @Security("is_granted('account_can_view')")
     */
    public function detail(Request $request, Account $account)
    {
    }
}
```

checking on twig
----------------
```
{% if is_granted('account_can_view') %}

    <li>
        <a href="{{ url('account_information', {'id':account.id}) }}">Account Information</a>
    </li>
{% endif %}
```