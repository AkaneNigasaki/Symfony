<?php

namespace App\Entity;

use App\Repository\EnrollmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EnrollmentRepository::class)]
#[ORM\Table(name: 'enrollments')]
#[ORM\UniqueConstraint(name: 'unique_student_course', columns: ['student_id', 'course_id'])]
#[ORM\HasLifecycleCallbacks]
class Enrollment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'enrollments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Student is required')]
    private ?Student $student = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'enrollments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Course is required')]
    private ?Course $course = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $enrollmentDate = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: 'Status is required')]
    #[Assert\Choice(choices: ['enrolled', 'withdrawn', 'completed'], message: 'Status must be one of: enrolled, withdrawn, completed')]
    private ?string $status = 'enrolled';

    #[ORM\OneToOne(targetEntity: Grade::class, mappedBy: 'enrollment', cascade: ['persist', 'remove'])]
    private ?Grade $finalGrade = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->enrollmentDate = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
        $this->status = 'enrolled';
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

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getFinalGrade(): ?Grade
    {
        return $this->finalGrade;
    }

    public function setFinalGrade(?Grade $finalGrade): static
    {
        if ($finalGrade === null && $this->finalGrade !== null) {
            $this->finalGrade->setEnrollment(null);
        }

        if ($finalGrade !== null && $finalGrade->getEnrollment() !== $this) {
            $finalGrade->setEnrollment($this);
        }

        $this->finalGrade = $finalGrade;
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

    public function isActive(): bool
    {
        return $this->status === 'enrolled';
    }
}
