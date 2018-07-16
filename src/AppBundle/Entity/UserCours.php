<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="user_cours")
 * @ORM\Entity()
 */
class UserCours
{
    /**
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    /**
     * @ORM\Column(name="tuteur", type="boolean")
     */
    protected $tuteur;



    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="cours")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cours", inversedBy="participants")
     * @ORM\JoinColumn(name="cours_id", referencedColumnName="id", nullable=false)
     */
    protected $cours;

    /**
     * UserCourses constructor.
     * @param $enabled
     * @param $user
     * @param $cours
     */
    public function __construct($enabled = false, $tuteur = false, $user, $cours)
    {
        $this->enabled = $enabled;
        $this->tuteur = $tuteur;
        $this->user = $user;
        $this->cours = $cours;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getCours()
    {
        return $this->cours;
    }

    /**
     * @param mixed $cours
     */
    public function setCours($cours)
    {
        $this->cours = $cours;
    }

    /**
     * @return mixed
     */
    public function getTuteur()
    {
        return $this->tuteur;
    }

    /**
     * @param mixed $tuteur
     */
    public function setTuteur($tuteur)
    {
        $this->tuteur = $tuteur;
    }


}

