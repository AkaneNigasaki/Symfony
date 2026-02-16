<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ORM\Table(name: 'courses')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['code'], message: 'This course code is already in use')]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Course code is required')]
    #[Assert\Length(max: 50, maxMessage: 'Course code cannot exceed {{ limit }} characters')]
    private ?string $code = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'Course name is required')]
    #[Assert\Length(max: 255, maxMessage: 'Course name cannot exceed {{ limit }} characters')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: 'Credits are required')]
    #[Assert\Positive(message: 'Credits must be a positive number')]
    private ?int $credits = null;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'A teacher must be assigned to the course')]
    private ?Teacher $teacher = null;

    #[ORM\ManyToOne(targetEntity: Classroom::class, inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Classroom $classroom = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: 'Maximum students limit is required')]
    #[Assert\Positive(message: 'Maximum students must be a positive number')]
    private ?int $maxStudents = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: 'Semester is required')]
    #[Assert\Length(max: 50, maxMessage: 'Semester cannot exceed {{ limit }} characters')]
    private ?string $semester = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'School is required')]
    private ?School $school = null;

    #[ORM\OneToMany(targetEntity: Schedule::class, mappedBy: 'course', cascade: ['persist', 'remove'])]
    private Collection $schedules;

    #[ORM\OneToMany(targetEntity: Enrollment::class, mappedBy: 'course', cascade: ['persist', 'remove'])]
    private Collection $enrollments;

    #[ORM\OneToMany(targetEntity: Assignment::class, mappedBy: 'course', cascade: ['persist', 'remove'])]
    private Collection $assignments;

    public function __construct()
    {
        $this->schedules = new ArrayCollection();
        $this->enrollments = new ArrayCollection();
        $this->assignments = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCredits(): ?int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): static
    {
        $this->credits = $credits;
        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): static
    {
        $this->teacher = $teacher;
        return $this;
    }

    public function getClassroom(): ?Classroom
    {
        return $this->classroom;
    }

    public function setClassroom(?Classroom $classroom): static
    {
        $this->classroom = $classroom;
        return $this;
    }

    public function getMaxStudents(): ?int
    {
        return $this->maxStudents;
    }

    public function setMaxStudents(int $maxStudents): static
    {
        $this->maxStudents = $maxStudents;
        return $this;
    }

    public function getSemester(): ?string
    {
        return $this->semester;
    }

    public function setSemester(string $semester): static
    {
        $this->semester = $semester;
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

    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): static
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules->add($schedule);
            $schedule->setCourse($this);
        }
        return $this;
    }

    public function removeSchedule(Schedule $schedule): static
    {
        if ($this->schedules->removeElement($schedule)) {
            if ($schedule->getCourse() === $this) {
                $schedule->setCourse(null);
            }
        }
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
            $enrollment->setCourse($this);
        }
        return $this;
    }

    public function removeEnrollment(Enrollment $enrollment): static
    {
        if ($this->enrollments->removeElement($enrollment)) {
            if ($enrollment->getCourse() === $this) {
                $enrollment->setCourse(null);
            }
        }
        return $this;
    }

    public function getEnrolledStudentsCount(): int
    {
        return $this->enrollments->filter(function (Enrollment $enrollment) {
            return $enrollment->getStatus() === 'enrolled';
        })->count();
    }

    public function hasAvailableSeats(): bool
    {
        return $this->getEnrolledStudentsCount() < $this->maxStudents;
    }

    public function getAssignments(): Collection
    {
        return $this->assignments;
    }

    public function addAssignment(Assignment $assignment): static
    {
        if (!$this->assignments->contains($assignment)) {
            $this->assignments->add($assignment);
            $assignment->setCourse($this);
        }
        return $this;
    }

    public function removeAssignment(Assignment $assignment): static
    {
        if ($this->assignments->removeElement($assignment)) {
            if ($assignment->getCourse() === $this) {
                $assignment->setCourse(null);
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
