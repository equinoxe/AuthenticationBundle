Offers a simple implementation of the Symfony Authentication.

## Installation

### Requirements

    https://github.com/equinoxe/SimpleOutputBundle

### Add AuthenticationBundle to your Bundle dir

    git submodule add git://github.com/equinoxe/AuthenticationBundle.git src/Equinoxe/AuthenticationBundle

### Add the Equinoxe namespace to your autoload.php

    // app/autoload.php
    $loader->registerNamespaces(array(
        'Equinoxe' => __DIR__ . '/../src',
        // your other namespaces
    );

### Add AuthenticationBundle to your application kernel

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Equinoxe\AuthenticationBundle\AuthenticationBundle(),
            // ...
        );
    }

### Add the routing configuration.

    // app/config/routing.yml
    authentication:
        resource: @AuthenticationBundle/Resources/config/routing.yml