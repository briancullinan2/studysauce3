<?php

/*
 * This file is part of the FOSJsRoutingBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StudySauce\Bundle\DependencyInjection;

class ExtractedRoute
{
    private $tokens;
    private $defaults;
    private $requirements;
    private $hosttokens;
    private $regexpr;

    public function __construct(array $tokens, array $defaults, array $requirements, array $hosttokens = array(), $regexpr)
    {
        $this->tokens = $tokens;
        $this->defaults = $defaults;
        $this->requirements = $requirements;
        $this->hosttokens = $hosttokens;
        $this->regexpr = $regexpr;
    }

    public function getTokens()
    {
        return $this->tokens;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function getRequirements()
    {
        return $this->requirements;
    }

    public function getHosttokens()
    {
        return $this->hosttokens;
    }

    public function getRegexpr() {
        return $this->regexpr;
    }
}
