<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
#[ORM\Table(name: 'schedules')]
#[ORM\HasLifecycleCallbacks]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'schedules')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Course is required')]
    private ?Course $course = null;

    #[ORM\ManyToOne(targetEntity: Classroom::class, inversedBy: 'schedules')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Classroom is required')]
    private ?Classroom $classroom = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotBlank(message: 'Day of week is required')]
    #[Assert\Range(min: 1, max: 7, notInRangeMessage: 'Day of week must be between {{ min }} and {{ max }}')]
    private ?int $dayOfWeek = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    #[Assert\NotBlank(message: 'Start time is required')]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    #[Assert\NotBlank(message: 'End time is required')]
    private ?\DateTimeImmutable $endTime = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotBlank(message: 'Effective from date is required')]
    private ?\DateTimeImmutable $effectiveFrom = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $effectiveTo = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getClassroom(): ?Classroom
    {
        return $this->classroom;
    }

    public function setClassroom(?Classroom $classroom): static
    {
        $this->classroom = $classroom;
        return $this;
    }

    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): static
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeImmutable $endTime): static
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getEffectiveFrom(): ?\DateTimeImmutable
    {
        return $this->effectiveFrom;
    }

    public function setEffectiveFrom(\DateTimeImmutable $effectiveFrom): static
    {
        $this->effectiveFrom = $effectiveFrom;
        return $this;
    }

    public function getEffectiveTo(): ?\DateTimeImmutable
    {
        return $this->effectiveTo;
    }

    public function setEffectiveTo(?\DateTimeImmutable $effectiveTo): static
    {
        $this->effectiveTo = $effectiveTo;
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

    public function isActive(\DateTimeImmutable $date = null): bool
    {
        $date = $date ?? new \DateTimeImmutable();
        
        if ($date < $this->effectiveFrom) {
            return false;
        }
        
        if ($this->effectiveTo !== null && $date > $this->effectiveTo) {
            return false;
        }
        
        return true;
    }
}
