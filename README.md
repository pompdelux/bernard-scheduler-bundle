# BernardSchedulerBundle

This bundle brings scheduling to your [BernardPHP](http://bernardphp.com/) powered [Symfony2](http://symfony.com/) app.

Please note that this bundle relies on redis as backend.

## Install:

1. Add BernardSchedulerBundle to your dependencies:

        // composer.json
        {
            // ...
            "require": {
                // ...
                "pompdelux/bernard-scheduler-bundle": "1.*"
            }
        }
2. Use Composer to download and install the bundle:

        $ php composer.phar update pompdelux/bernard-scheduler-bundle

3. Register the bundle in your application:

        // app/AppKernel.php
        class AppKernel extends Kernel
        {
            // ...
            public function registerBundles()
            {
                $bundles = array(
                    // ...
                    new Pompdelux\BernardSchedulerBundle\BernardSchedulerBundle(),
                );
            }
        }

4. Add `php_redis` section to `config.yml`

        // app/config.yml
        php_resque:
            class:
                bernard:
                    host:     %redis_host%
                    port:     %redis_port%
                    prefix:   %redis_prefix%
                    skip_env: %redis_skip_env%
                    database: %redis_database%
                    auth:     %redis_password%


## Usage:

```php
use Pompdelux\BernardSchedulerBundle\Job;

// Adds 'DoStuff' job to 'some-bernard-queue' for execution in 30 seconds.
$job = new Job('some-bernard-queue', 'DoStuff', [
    'any' => 'job data',
]);

$container->get('pdl.bernard_scheduler')->enqueueIn(30, $job);
```
