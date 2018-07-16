<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as JMSSerializer;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 *
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 * @JMSSerializer\ExclusionPolicy("all")
 * @JMSSerializer\AccessorOrder("custom", custom = {"id", "username", "email", "accounts"})
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     * @JMSSerializer\Groups({"users_all","users_summary"})
     */
    protected $id;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     * @JMSSerializer\Groups({"users_all","users_summary"})
     */
    protected $username;


    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     * @JMSSerializer\Groups({"users_all","users_summary"})
     * @ORM\Column(name="firstname", type="string", nullable=true)
     */
    protected $firstname;


    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     * @JMSSerializer\Groups({"users_all","users_summary"})
     * @ORM\Column(name="lastname", type="string", nullable=true)
     */
    protected $lastname;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     * @JMSSerializer\Groups({"users_all","users_summary"})
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    protected $phone;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     * @JMSSerializer\Groups({"users_all","users_summary"})
     * @ORM\Column(name="role", type="string", nullable=true)
     */
    protected $role;

    /**
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     * @JMSSerializer\Groups({"users_all","users_summary"})
     * @ORM\Column(name="department", type="string", nullable=true)
     */
    protected $department;

    /**
     * @var string The email of the user.
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     * @JMSSerializer\Groups({"users_all","users_summary"})
     */
    protected $email;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Project", mappedBy="user")
     * @var Project[]
     */
    protected $projects;


    /** @ORM\OneToMany(targetEntity="AppBundle\Entity\UserSkills", mappedBy="users")
     * @var Skill[]
     */
    protected $skills;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserSubscriptions", mappedBy="user")
     * @var Project[]
     */
    protected $subscriptions;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserCours", mappedBy="user")
     * @var Cours[]
     */
    protected $cours;

    /** @ORM\OneToMany(targetEntity="AppBundle\Entity\Task", mappedBy="user")
     * @var Task[]
     */
    protected $tasks;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->projects = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->skills = new ArrayCollection();
        $this->cours = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }


    /**
     * @return Project[]
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @param Project[] $projects
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;
    }


    /**
     * Add project
     *
     * @param \AppBundle\Entity\Project $project
     *
     * @return User
     */
    public function addProject(\AppBundle\Entity\Project $project)
    {
        $this->projects[] = $project;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param mixed $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }


    /**
     * Remove project
     *
     * @param \AppBundle\Entity\Project $project
     */
    public function removeProject(\AppBundle\Entity\Project $project)
    {
        $this->projects->removeElement($project);
    }

    /**
     * @param Project $project
     */
    public function addSubscriptions(Project $project)
    {
        if ($this->subscriptions->contains($project)) {
            return;
        }
        $this->subscriptions->add($project);
        $project->addContributors($this);
    }

    /**
     * @param Project $project
     */
    public function removeSubscriptions(Project $project)
    {
        if (!$this->subscriptions->contains($project)) {
            return;
        }
        $this->subscriptions->removeElement($project);
        $project->removeContributors($this);
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
        $skill->addUser($this);
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
        $skill->removeUser($this);
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
     * @return Project[]|Collection
     */
    public function getSubscriptions(): array
    {
        return $this->subscriptions;
    }

    /**
     * @param Project[]|Collection $subscriptions
     */
    public function setSubscriptions(array $subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }

    /**
     * @param Cours $cours
     */
    public function addCours(Cours $cours)
    {
        if ($this->cours->contains($cours)) {
            return;
        }
        $this->cours->add($cours);
        $cours->addParticipant($this);
    }

    /**
     * @param Cours $cours
     */
    public function removeCours(Cours $cours)
    {
        if (!$this->subscriptions->contains($cours)) {
            return;
        }
        $this->subscriptions->removeElement($cours);
        $cours->removeParticipants($this);
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



}
