<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="user_skills")
 * @ORM\Entity()
 */
class UserSkills
{
    /**
     * @ORM\Column(name="score", type="integer")
     */
    protected $score;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="skills")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $users;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Skill", inversedBy="users")
     * @ORM\JoinColumn(name="skill_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $skills;

    /**
     * UserSkills constructor.
     * @param $score
     * @param $users
     * @param $skills
     */
    public function __construct($score = 0, $users, $skills)
    {
        $this->score = $score;
        $this->users = $users;
        $this->skills = $skills;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return mixed
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * @param mixed $skills
     */
    public function setSkills($skills)
    {
        $this->skills = $skills;
    }

}

