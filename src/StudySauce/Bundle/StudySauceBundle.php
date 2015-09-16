<?php

namespace StudySauce\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class StudySauceBundle
 * @package StudySauce\Bundle
 */
class StudySauceBundle extends Bundle
{
    public static $institutions_path = '';

    public function boot()
    {
        $kernel = $this->container->get('kernel');
        self::$institutions_path = $kernel->locateResource('@StudySauceBundle/Resources/public/js/institutions.json');
    }
}
