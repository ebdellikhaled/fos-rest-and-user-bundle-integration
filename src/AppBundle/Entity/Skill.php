<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="skill")
 * @ORM\Entity()
 */
class Skill
{
    /**
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="projects")
     * @ORM\JoinColumn(nullable=true)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var User
     */
    protected $user;


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
     * @ORM\Column(type="boolean", options={"default" : 0})
     *
     */
    private $state;

    /** @ORM\OneToMany(targetEntity="AppBundle\Entity\UserSkills", mappedBy="skills") */
    protected $users;

    /** @ORM\OneToMany(targetEntity="AppBundle\Entity\ProjectSkills", mappedBy="skills") */
    protected $projects;

    /** @ORM\OneToMany(targetEntity="AppBundle\Entity\CoursSkills", mappedBy="skills") */
    protected $cours;


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
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created)
    {
        $this->created = $created;
    }

    /**
     * @return DateTime
     */
    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     */
    public function setUpdated(DateTime $updated)
    {
        $this->updated = $updated;
    }


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->cours = new ArrayCollection();
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
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @param User $user
     */
    public function addUser(UserSkills $user)
    {
        if ($this->users->contains($user)) {
            return;
        }
        $this->users->add($user);
        $user->addSkills($this);
    }
    /**
     * @param User $user
     */
    public function removeUser(UserSkills $user)
    {
        if (!$this->users->contains($user)) {
            return;
        }
        $this->users->removeElement($user);
        $user->removeSkills($this);
    }

    /**
     * @param User $user
     */
    public function removeUsers(User $user)
    {
        if (!$this->users->contains($user)) {
            return;
        }
        $this->users->removeElement($user);
        $user->removeSkills($this);
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function addContributors(array $contributors)
    {
        foreach ($contributors as $contributor) {
            if (! $contributor instanceof UserSubscriptions) {
                throw new \Exception('$contributor should be instance of UserSubscriptions');
            }

            $this->addUser($contributor);
        }

        return $this;
    }
    /**
     * @param User $user
     */
    public function addProject(Project $project)
    {
        if ($this->projects->contains($project)) {
            return;
        }
        $this->projects->add($project);
        $project->addSkills($this);
    }
    /**
     * @param User $user
     */
    public function removeProject(Project $project)
    {
        if (!$this->projects->contains($project)) {
            return;
        }
        $this->projects->removeElement($project);
        $project->removeSkills($this);
    }

    /**
     * @param User $user
     */
    public function removeProjects(Project $project)
    {
        if (!$this->projects->contains($project)) {
            return;
        }
        $this->projects->removeElement($project);
        $project->removeSkills($this);
    }

    public function getProjects()
    {
        return $this->projects;
    }






    /**
     * @param User $user
     */
    public function addCours(Cours $cours)
    {
        if ($this->cours->contains($cours)) {
            return;
        }
        $this->cours->add($cours);
        $cours->addSkills($this);
    }
    /**
     * @param User $user
     */
    public function removeCours(Cours $cours)
    {
        if (!$this->cours->contains($cours)) {
            return;
        }
        $this->cours->removeElement($cours);
        $cours->removeSkills($this);
    }

    /**
     * @param User $user
     */
    public function removeCourses(Cours $cours)
    {
        if (!$this->cours->contains($cours)) {
            return;
        }
        $this->cours->removeElement($cours);
        $cours->removeSkills($this);
    }

    public function getCours()
    {
        return $this->cours;
    }

    public function toArray()
    {
        return array(
            'id'    => $this->getId(),
            'title'  => $this->getTitle(),
            'user' => $this->getUser(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated(),
            'state' => $this->getState(),
            'users' => $this->getUsers(),
            'projects' => $this->getProjects(),
            'cours' => $this->getCours(),
        );
    }
}

