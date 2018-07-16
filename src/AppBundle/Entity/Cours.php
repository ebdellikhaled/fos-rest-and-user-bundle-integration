<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="cours")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 */
class Cours
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="description", type="text", length=1000)
     */
    private $description;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="cours")
     * @ORM\JoinColumn(nullable=true)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var User
     */
    protected $user;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserCours", mappedBy="cours", cascade={"remove","persist"})
     */
    protected $participants;


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

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\File(maxSize="5M")
     */
    private $brochure;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\File(maxSize="20M")
     */
    private $video;

    /** @ORM\OneToMany(targetEntity="AppBundle\Entity\CoursSkills", mappedBy="cours", cascade={"persist"})
     * @var Skill[]
     */
    protected $skills;


    /** @ORM\OneToMany(targetEntity="AppBundle\Entity\CoursQuiz", mappedBy="cours") */
    protected $quizs;


    public function getBrochure()
    {
        return $this->brochure;
    }

    public function setBrochure($brochure)
    {
        $this->brochure = $brochure;

        return $this;
    }

    public function getVideo()
    {
        return $this->video;
    }

    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
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
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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


    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->skills = new ArrayCollection();
        $this->quizs = new ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->updated = new \DateTime();
    }

    /**
     * @param User $user
     */
    public function removeParticipants(User $user)
    {
        if (!$this->participants->contains($user)) {
            return;
        }
        $this->participants->removeElement($user);
        $user->removeCours($this);
    }

    public function getParticipants()
    {
        return $this->participants;
    }

    public function addParticipant(UserCours $user)
    {
        $this->participants->add($user);
        $user->setCours($this);

        return $this;
    }

    public function addParticipants(array $participants)
    {
        foreach ($participants as $participant) {
            if (! $participant instanceof UserCours) {
               throw new \Exception('$participant should be instance of UserCours');
            }

            $this->addParticipant($participant);
        }

        return $this;
    }


    /**
     * @param Skill $skill
     */
    public function addSkills(Skill $skill)
    {
        if ($this->skills->contains($skill)) {
            return;
        }
        $this->skills->add($skill);
        $skill->addCours($this);
    }
    /**
     * @param Skill $skill
     */
    public function removeSkills(Skill $skill)
    {
        if (!$this->skills->contains($skill)) {
            return;
        }
        $this->skills->removeElement($skill);
        $skill->removeCours($this);
    }

    /**
     * @return Skill[]|Collection
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * @param Skill[]|Collection $skills
     */
    public function setSkills($skills)
    {
        $this->skills = $skills;
    }

    /**
     * @return []
     */
    public function getQuizs()
    {
        return $this->quizs;
    }

    /**
     * @param Quiz[] $quizs
     */
    public function setQuizs(array $quizs)
    {
        $this->quizs = $quizs;
    }




    /**
     * @param Quiz $quiz
     */
    public function addQuizs(Quiz $quiz)
    {
        if ($this->quizs->contains($quiz)) {
            return;
        }
        $this->quizs->add($quiz);
        $quiz->setCours($this);
    }
    /**
     * @param Quiz $quiz
     */
    public function removeQuizs(Quiz $quiz)
    {
        if (!$this->quizs->contains($quiz)) {
            return;
        }
        $this->quizs->removeElement($quiz);
    }


    public function toArray()
    {
        return array(
            'id'    => $this->getId(),
            'title'  => $this->getTitle(),
            'description' => $this->getDescription(),
            'brochure' => $this->getBrochure(),
            'video' => $this->getVideo(),
            'user' => $this->getUser(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated(),
            'participants' => $this->getParticipants(),
            'skills' => $this->getSkills(),
            'quizs' => $this->getQuizs(),
        );
    }


}

