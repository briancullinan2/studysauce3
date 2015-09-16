<?php

namespace Admin\Bundle\Helpers;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Templating\PhpEngine;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\Storage\Storage;
use Symfony\Component\Templating\Storage\StringStorage;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * Class CustomPhpEngine
 * @package Admin\Bundle\Helpers
 */
class StringLoader implements LoaderInterface
{
    private $template;
    /**
     */
    public function __construct($template)
    {
        $this->template = $template;
    }

    /**
     * Loads a template.
     *
     * @param TemplateReferenceInterface $template A template
     *
     * @return Storage|bool false if the template cannot be loaded, a Storage instance otherwise
     *
     * @api
     */
    public function load(TemplateReferenceInterface $template)
    {
        return new StringStorage($this->template);
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param TemplateReferenceInterface $template A template
     * @param int $time The last modification time of the cached template (timestamp)
     *
     * @return bool
     *
     * @api
     */
    public function isFresh(TemplateReferenceInterface $template, $time)
    {
        return true;
    }
}