<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="cours_quiz")
 * @ORM\Entity()
 */
class CoursQuiz
{
    /**
     * @ORM\Column(name="ok", type="boolean")
     */
    protected $ok;

    /**
     * @ORM\Id()
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $userId;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cours", inversedBy="quizs")
     * @ORM\JoinColumn(name="cours_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $cours;

    /**
     * @ORM\Column(name="choice_selected", type="string")
     */
    protected $choiceSelected;


    /**
     * @return mixed
     */
    public function getOk()
    {
        return $this->ok;
    }

    /**
     * @param mixed $ok
     */
    public function setOk($ok)
    {
        $this->ok = $ok;
    }

    /**
     * @return []
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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getChoiceSelected()
    {
        return $this->choiceSelected;
    }

    /**
     * @param mixed $choiceSelected
     */
    public function setChoiceSelected($choiceSelected)
    {
        $this->choiceSelected = $choiceSelected;
    }


}

