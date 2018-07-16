<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 */
class Project
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="projects")
     * @ORM\JoinColumn(nullable=true)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var User
     */
    protected $user;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserSubscriptions", mappedBy="project", cascade={"remove","persist"})
     */
    protected $contributors;

    /** @ORM\OneToMany(targetEntity="AppBundle\Entity\ProjectSkills", mappedBy="projects", cascade={"remove","persist"})
     * @var Skill[]
     */
    protected $skills;

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

    /** @ORM\OneToMany(targetEntity="AppBundle\Entity\Task", mappedBy="user")
     * @var Task[]
     */
    protected $tasks;

    public function getBrochure()
    {
        return $this->brochure;
    }

    public function setBrochure($brochure)
    {
        $this->brochure = $brochure;

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
        $this->skills = new ArrayCollection();
        $this->contributors = new ArrayCollection();
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
    public function removeContributors(User $user)
    {
        if (!$this->contributors->contains($user)) {
            return;
        }
        $this->contributors->removeElement($user);
        $user->removeSubscriptions($this);
    }

    public function getContributors()
    {
        return $this->contributors;
    }

    public function addContributor(UserSubscriptions $user)
    {
        $this->contributors->add($user);
        $user->setProject($this);

        return $this;
    }

    public function addContributors(array $contributors)
    {
        foreach ($contributors as $contributor) {
            if (!$contributor instanceof UserSubscriptions) {
                throw new \Exception('$contributor should be instance of UserSubscriptions');
            }

            $this->addContributor($contributor);
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
        $skill->addProject($this);
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
        $skill->removeProject($this);
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
     * @return Task[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    /**
     * @param Task[] $tasks
     */
    public function setTasks(array $tasks)
    {
        $this->tasks = $tasks;
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'brochure' => $this->getBrochure(),
            'user' => $this->getUser(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated(),
            'contributors' => $this->getContributors(),
            'skills' => $this->getSkills(),
            'tasks' => $this->getTasks()
        );
    }
}

