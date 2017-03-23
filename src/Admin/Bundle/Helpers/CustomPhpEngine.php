<?php

namespace Admin\Bundle\Helpers;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Templating\PhpEngine;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CustomPhpEngine
 * @package Admin\Bundle\Helpers
 */
class CustomPhpEngine extends PhpEngine
{

    /**
     * @param TemplateNameParserInterface $parser
     * @param ContainerInterface $container
     * @param LoaderInterface $loader
     * @param GlobalVariables $globals
     */
    public function __construct(TemplateNameParserInterface $parser, ContainerInterface $container, LoaderInterface $loader, GlobalVariables $globals = null)
    {
        parent::__construct($parser, $container, $loader, $globals);
    }

}