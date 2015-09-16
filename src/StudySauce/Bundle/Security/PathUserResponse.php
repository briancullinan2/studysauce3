<?php

namespace StudySauce\Bundle\Security;

/**
 * Class PathUserResponse
 * @package StudySauce\Bundle\Security
 */
class PathUserResponse extends \HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse
{
    public $username;

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        if(!empty($this->username))
            return $this->username;
        else
            return parent::getUsername();
    }

    /**
     * @return null|string
     */
    public function getFirst()
    {
        return $this->getValueForPath('first');
    }

    /**
     * @return null|string
     */
    public function getLast()
    {
        return $this->getValueForPath('last');
    }
}