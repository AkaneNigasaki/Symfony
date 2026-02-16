<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
#[ORM\Table(name: 'teachers')]
#[ORM\HasLifecycleCallbacks]
class Teacher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: 'First name is required')]
    #[Assert\Length(max: 100, maxMessage: 'First name cannot exceed {{ limit }} characters')]
    private ?string $firstName = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank(message: 'Last name is required')]
    #[Assert\Length(max: 100, maxMessage: 'Last name cannot exceed {{ limit }} characters')]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::STRING, length: 191, unique: true)]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Please provide a valid email address')]
    #[Assert\Length(max: 191, maxMessage: 'Email cannot exceed {{ limit }} characters')]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    #[Assert\Length(max: 20, maxMessage: 'Phone number cannot exceed {{ limit }} characters')]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Specialization cannot exceed {{ limit }} characters')]
    private ?string $specialization = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotBlank(message: 'Hire date is required')]
    private ?\DateTimeImmutable $hireDate = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'teachers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'School is required')]
    private ?School $school = null;

    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'teacher')]
    private Collection $courses;

    #[ORM\OneToMany(targetEntity: Grade::class, mappedBy: 'gradedBy')]
    private Collection $gradedAssignments;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->gradedAssignments = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    public function setSpecialization(?string $specialization): static
    {
        $this->specialization = $specialization;
        return $this;
    }

    public function getHireDate(): ?\DateTimeImmutable
    {
        return $this->hireDate;
    }

    public function setHireDate(\DateTimeImmutable $hireDate): static
    {
        $this->hireDate = $hireDate;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setTeacher($this);
        }
        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            if ($course->getTeacher() === $this) {
                $course->setTeacher(null);
            }
        }
        return $this;
    }

    public function getGradedAssignments(): Collection
    {
        return $this->gradedAssignments;
    }

    public function addGradedAssignment(Grade $grade): static
    {
        if (!$this->gradedAssignments->contains($grade)) {
            $this->gradedAssignments->add($grade);
            $grade->setGradedBy($this);
        }
        return $this;
    }

    public function removeGradedAssignment(Grade $grade): static
    {
        if ($this->gradedAssignments->removeElement($grade)) {
            if ($grade->getGradedBy() === $this) {
                $grade->setGradedBy(null);
            }
        }
        return $this;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): static
    {
        $this->school = $school;
        return $this;
    }
}
