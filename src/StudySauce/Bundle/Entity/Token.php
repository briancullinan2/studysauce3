<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rememberme_token")
 */
class Token
{
    /**
     * @ORM\Column(type="string", length=88, name="series")
     * @ORM\Id
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=88, name="value")
     */
    protected $value;

    /**
     * @ORM\Column(type="datetime", name="lastUsed")
     */
    protected $lastUsed;

    /**
     * @ORM\Column(type="string", length=100, name="class")
     */
    protected $class;

    /**
     * @ORM\Column(type="string", length=200, name="username")
     */
    protected $username;

}
