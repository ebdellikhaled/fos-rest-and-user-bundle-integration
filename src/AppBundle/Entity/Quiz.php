<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="quiz")
 * @ORM\Entity()
 *
 */
class Quiz
{
    /**
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="question", type="string", length=255)
     */
    private $question;

    /**
     * @ORM\Column(name="response", type="text", length=1000)
     */
    private $response;

    /**
     * @ORM\Column(name="choices", type="text")
     */
    private $choices;

    /**
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     *
     * @ORM\Column(type="datetime")
     */
    private $updated;


    /** @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cours", inversedBy="quizs") */
    protected $cours;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }




    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->updated = new \DateTime();
    }




    public function getCours()
    {
        return $this->cours;
    }

    public function setCours(Cours $cours)
    {
        $this->cours = $cours;

        return $this;
    }

    /**
     * @return []
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param [] $choices
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
    }


    public function toArray()
    {
        return array(
            'id'    => $this->getId(),
            'question' => $this->getQuestion(),
            'choices' => $this->getChoices(),
            'response' => $this->getResponse(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated(),
            'cours' => $this->getCours()
        );
    }
}

