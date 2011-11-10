<?php

namespace Gists\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length="40")
     */
    protected $username;

    /**
     * @ORM\OneToMany(targetEntity="Gist", mappedBy="user", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     * @ORM\OrderBy({"dateCreated" = "DESC"})
     */
    protected $gists;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    public function __construct()
    {
        $this->gists = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Add gists
     *
     * @param Gists\Entity\Gist $gists
     */
    public function addGist(\Gists\Entity\Gist $gists)
    {
        $this->gists[] = $gists;
    }

    /**
     * Get gists
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getGists()
    {
        return $this->gists;
    }
}
