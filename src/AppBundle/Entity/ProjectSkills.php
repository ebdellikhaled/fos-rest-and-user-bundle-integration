<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="project_skills")
 * @ORM\Entity()
 */
class ProjectSkills
{
    /**
     * @ORM\Column(name="score", type="integer")
     */
    protected $score;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project", inversedBy="skills")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $projects;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Skill", inversedBy="projects")
     * @ORM\JoinColumn(name="skill_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $skills;

    /**
     * UserSkills constructor.
     * @param $score
     * @param $projects
     * @param $skills
     */
    public function __construct($score = 0, $projects, $skills)
    {
        $this->score = $score;
        $this->projects = $projects;
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
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @param mixed $projects
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;
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

