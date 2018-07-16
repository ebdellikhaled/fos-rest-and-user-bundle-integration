<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="cours_skills")
 * @ORM\Entity()
 */
class CoursSkills
{
    /**
     * @ORM\Column(name="score", type="integer")
     */
    protected $score;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cours", inversedBy="skills")
     * @ORM\JoinColumn(name="cours_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $cours;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Skill", inversedBy="cours")
     * @ORM\JoinColumn(name="skill_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $skills;

    /**
     * UserSkills constructor.
     * @param $score
     * @param $cours
     * @param $skills
     */
    public function __construct($score = 0, $cours, $skills)
    {
        $this->score = $score;
        $this->cours = $cours;
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

