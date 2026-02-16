<?php

namespace App\Entity;

use App\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SchoolRepository::class)]
#[ORM\Table(name: 'schools')]
#[ORM\HasLifecycleCallbacks]
class School
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'School name is required')]
    #[Assert\Length(max: 255, maxMessage: 'School name cannot exceed {{ limit }} characters')]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Address cannot exceed {{ limit }} characters')]
    private ?string $address = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    #[Assert\Length(max: 20, maxMessage: 'Phone number cannot exceed {{ limit }} characters')]
    private ?string $phone = null;

    #[ORM\Column(type: Types::STRING, length: 191, nullable: true)]
    #[Assert\Email(message: 'Please provide a valid email address')]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 64, unique: true, nullable: true)]
    private ?string $joinToken = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Classroom::class, mappedBy: 'school', cascade: ['persist', 'remove'])]
    private Collection $classrooms;

    #[ORM\OneToMany(targetEntity: Teacher::class, mappedBy: 'school', cascade: ['persist', 'remove'])]
    private Collection $teachers;

    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'school', cascade: ['persist', 'remove'])]
    private Collection $courses;

    #[ORM\OneToMany(targetEntity: Student::class, mappedBy: 'school', cascade: ['persist', 'remove'])]
    private Collection $students;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'schools')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $owner = null;

    public function __construct()
    {
        $this->classrooms = new ArrayCollection();
        $this->teachers = new ArrayCollection();
        $this->courses = new ArrayCollection();
        $this->students = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
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

    public function getJoinToken(): ?string
    {
        return $this->joinToken;
    }

    public function setJoinToken(?string $joinToken): static
    {
        $this->joinToken = $joinToken;
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

    public function getClassrooms(): Collection
    {
        return $this->classrooms;
    }

    public function addClassroom(Classroom $classroom): static
    {
        if (!$this->classrooms->contains($classroom)) {
            $this->classrooms->add($classroom);
            $classroom->setSchool($this);
        }
        return $this;
    }

    public function removeClassroom(Classroom $classroom): static
    {
        if ($this->classrooms->removeElement($classroom)) {
            if ($classroom->getSchool() === $this) {
                $classroom->setSchool(null);
            }
        }
        return $this;
    }

    public function getTeachers(): Collection
    {
        return $this->teachers;
    }

    public function addTeacher(Teacher $teacher): static
    {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers->add($teacher);
            $teacher->setSchool($this);
        }
        return $this;
    }

    public function removeTeacher(Teacher $teacher): static
    {
        if ($this->teachers->removeElement($teacher)) {
            if ($teacher->getSchool() === $this) {
                $teacher->setSchool(null);
            }
        }
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
            $course->setSchool($this);
        }
        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            if ($course->getSchool() === $this) {
                $course->setSchool(null);
            }
        }
        return $this;
    }

    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->setSchool($this);
        }
        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            if ($student->getSchool() === $this) {
                $student->setSchool(null);
            }
        }
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }
}
