<?php

namespace Gists\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="gists")
 */
class Gist
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="gists")
     */
    protected $user;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $starred = 0;

    public function getRepresentation()
    {
        $repr = new \StdClass;
        $repr->id = $this->getId();
        $repr->user = '/users/' . $this->getUser()->getId();
        $repr->description = $this->getDescription();
        $repr->content = $this->getContent();
        $repr->starred = $this->getStarred();
        return $repr;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set content
     *
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return text
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set user
     *
     * @param Gists\Entity\User $user
     */
    public function setUser(\Gists\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Gists\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set starred
     *
     * @param boolean $starred
     */
    public function setStarred($starred)
    {
        $this->starred = $starred;
    }

    /**
     * Get starred
     *
     * @return boolean
     */
    public function getStarred()
    {
        return $this->starred;
    }
}
