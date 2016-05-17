<?php

namespace  {

    use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
    use Symfony\Bundle\FrameworkBundle\Templating\PhpEngine;
    use Wa72\HtmlPageDom\HtmlPage;
    use Wa72\HtmlPageDom\HtmlPageCrawler;

    if (!function_exists('jQuery')) {
        /**
         * @param $context
         * @return HtmlPageCrawler
         */
        function jQuery($context)
        {
            if ($context instanceof PhpEngine) {
                return HtmlPageCrawler::create('<div/>')->find('div');
            }
            return HtmlPageCrawler::create($context);
        }
    }

}

namespace Admin\Bundle {

    use Symfony\Component\HttpKernel\Bundle\Bundle;

    /**
     * Class StudySauceBundle
     * @package StudySauce\Bundle
     */
    class AdminBundle extends Bundle
    {

    }

}