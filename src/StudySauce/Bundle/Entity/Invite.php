<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Interface Invite
 * @package StudySauce\Bundle\Entity
 */
interface Invite
{
    function getEmail();

    function getFirst();

    function getCode();

    function getLast();

    /** @return User */
    function getUser();

    /**
     * @param $bool
     * @return mixed
     */
    function setActivated($bool);
}