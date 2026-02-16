<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ORM\Table(name: 'students')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['studentId'], message: 'This student ID is already in use')]
#[UniqueEntity(fields: ['email'], message: 'This email is already in use')]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Student ID is required')]
    #[Assert\Length(max: 50, maxMessage: 'Student ID cannot exceed {{ limit }} characters')]
    private ?string $studentId = null;

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

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotBlank(message: 'Date of birth is required')]
    private ?\DateTimeImmutable $dateOfBirth = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotBlank(message: 'Enrollment date is required')]
    private ?\DateTimeImmutable $enrollmentDate = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'School is required')]
    private ?School $school = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'studentProfiles')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $status = 'accepted'; // Default to accepted for backward compatibility

    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $role = 'student'; // Roles: student, admin

    #[ORM\OneToMany(targetEntity: Enrollment::class, mappedBy: 'student', cascade: ['persist', 'remove'])]
    private Collection $enrollments;

    #[ORM\OneToMany(targetEntity: Grade::class, mappedBy: 'student', cascade: ['persist', 'remove'])]
    private Collection $grades;

    public function __construct()
    {
        $this->enrollments = new ArrayCollection();
        $this->grades = new ArrayCollection();
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

    public function getStudentId(): ?string
    {
        return $this->studentId;
    }

    public function setStudentId(string $studentId): static
    {
        $this->studentId = $studentId;
        return $this;
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

    public function getDateOfBirth(): ?\DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeImmutable $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    public function getEnrollmentDate(): ?\DateTimeImmutable
    {
        return $this->enrollmentDate;
    }

    public function setEnrollmentDate(\DateTimeImmutable $enrollmentDate): static
    {
        $this->enrollmentDate = $enrollmentDate;
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

    public function getEnrollments(): Collection
    {
        return $this->enrollments;
    }

    public function addEnrollment(Enrollment $enrollment): static
    {
        if (!$this->enrollments->contains($enrollment)) {
            $this->enrollments->add($enrollment);
            $enrollment->setStudent($this);
        }
        return $this;
    }

    public function removeEnrollment(Enrollment $enrollment): static
    {
        if ($this->enrollments->removeElement($enrollment)) {
            if ($enrollment->getStudent() === $this) {
                $enrollment->setStudent(null);
            }
        }
        return $this;
    }

    public function getActiveEnrollments(): Collection
    {
        return $this->enrollments->filter(function (Enrollment $enrollment) {
            return $enrollment->getStatus() === 'enrolled';
        });
    }

    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(Grade $grade): static
    {
        if (!$this->grades->contains($grade)) {
            $this->grades->add($grade);
            $grade->setStudent($this);
        }
        return $this;
    }

    public function removeGrade(Grade $grade): static
    {
        if ($this->grades->removeElement($grade)) {
            if ($grade->getStudent() === $this) {
                $grade->setStudent(null);
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }
}
