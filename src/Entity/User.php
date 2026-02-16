<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * @var list<string>
     */
    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Length(min: 6, minMessage: 'Password must be at least {{ limit }} characters', groups: ['registration'])]
    private ?string $plainPassword = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    private ?string $firstName = null;

    #[ORM\OneToMany(targetEntity: School::class, mappedBy: 'owner', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $schools;

    #[ORM\OneToMany(targetEntity: UserActivity::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $activities;

    #[ORM\OneToMany(targetEntity: Student::class, mappedBy: 'user')]
    private Collection $studentProfiles;

    public function __construct()
    {
        $this->schools = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->studentProfiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @return Collection<int, School>
     */
    public function getSchools(): Collection
    {
        return $this->schools;
    }

    public function addSchool(School $school): static
    {
        if (!$this->schools->contains($school)) {
            $this->schools->add($school);
            $school->setOwner($this);
        }
        return $this;
    }

    public function removeSchool(School $school): static
    {
        if ($this->schools->removeElement($school)) {
            if ($school->getOwner() === $this) {
                $school->setOwner(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, UserActivity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(UserActivity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setUser($this);
        }

        return $this;
    }

    public function removeActivity(UserActivity $activity): static
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getUser() === $this) {
                $activity->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudentProfiles(): Collection
    {
        return $this->studentProfiles;
    }

    public function addStudentProfile(Student $student): static
    {
        if (!$this->studentProfiles->contains($student)) {
            $this->studentProfiles->add($student);
            $student->setUser($this);
        }
        return $this;
    }

    public function removeStudentProfile(Student $student): static
    {
        if ($this->studentProfiles->removeElement($student)) {
            if ($student->getUser() === $this) {
                $student->setUser(null);
            }
        }
        return $this;
    }
}
