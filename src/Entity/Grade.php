<?php

namespace App\Entity;

use App\Repository\GradeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GradeRepository::class)]
#[ORM\Table(name: 'grades')]
#[ORM\HasLifecycleCallbacks]
class Grade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'grades')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Student is required')]
    private ?Student $student = null;

    #[ORM\ManyToOne(targetEntity: Assignment::class, inversedBy: 'grades')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Assignment $assignment = null;

    #[ORM\OneToOne(targetEntity: Enrollment::class, inversedBy: 'finalGrade')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Enrollment $enrollment = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Points are required')]
    #[Assert\PositiveOrZero(message: 'Points must be zero or positive')]
    private ?string $points = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: 'Maximum points is required')]
    #[Assert\Positive(message: 'Maximum points must be positive')]
    private ?int $maxPoints = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $percentage = null;

    #[ORM\Column(type: Types::STRING, length: 2)]
    private ?string $letterGrade = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $gradedAt = null;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'gradedAssignments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Graded by teacher is required')]
    private ?Teacher $gradedBy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comments = null;

    public function __construct()
    {
        $this->gradedAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculateGrade(): void
    {
        if ($this->points !== null && $this->maxPoints !== null && $this->maxPoints > 0) {
            $this->percentage = number_format(((float)$this->points / $this->maxPoints) * 100, 2);
            $this->letterGrade = $this->calculateLetterGrade((float)$this->percentage);
        }
    }

    private function calculateLetterGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;
        return $this;
    }

    public function getAssignment(): ?Assignment
    {
        return $this->assignment;
    }

    public function setAssignment(?Assignment $assignment): static
    {
        $this->assignment = $assignment;
        return $this;
    }

    public function getEnrollment(): ?Enrollment
    {
        return $this->enrollment;
    }

    public function setEnrollment(?Enrollment $enrollment): static
    {
        $this->enrollment = $enrollment;
        return $this;
    }

    public function getPoints(): ?string
    {
        return $this->points;
    }

    public function setPoints(string $points): static
    {
        $this->points = $points;
        return $this;
    }

    public function getMaxPoints(): ?int
    {
        return $this->maxPoints;
    }

    public function setMaxPoints(int $maxPoints): static
    {
        $this->maxPoints = $maxPoints;
        return $this;
    }

    public function getPercentage(): ?string
    {
        return $this->percentage;
    }

    public function setPercentage(string $percentage): static
    {
        $this->percentage = $percentage;
        return $this;
    }

    public function getLetterGrade(): ?string
    {
        return $this->letterGrade;
    }

    public function setLetterGrade(string $letterGrade): static
    {
        $this->letterGrade = $letterGrade;
        return $this;
    }

    public function getGradedAt(): ?\DateTimeImmutable
    {
        return $this->gradedAt;
    }

    public function setGradedAt(\DateTimeImmutable $gradedAt): static
    {
        $this->gradedAt = $gradedAt;
        return $this;
    }

    public function getGradedBy(): ?Teacher
    {
        return $this->gradedBy;
    }

    public function setGradedBy(?Teacher $gradedBy): static
    {
        $this->gradedBy = $gradedBy;
        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): static
    {
        $this->comments = $comments;
        return $this;
    }
}
