<?php

namespace App\DTOs;

final readonly class MarkEntryDTO
{
    public function __construct(
        public int $studentId,
        public int $subjectId,
        public ?int $subSubjectId,
        public int $examId,
        public string $component,
        public float $obtained,
        public float $full,
        public float $pass,
    ) {}
}
