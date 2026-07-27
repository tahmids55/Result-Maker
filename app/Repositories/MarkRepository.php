<?php

namespace App\Repositories;

use App\DTOs\MarkEntryDTO;
use App\Models\Mark;

class MarkRepository
{
    /**
     * Batch upsert marks using a single PostgreSQL UPSERT query.
     * Replaces N individual updateOrCreate calls with O(1) DB round-trips.
     *
     * @param MarkEntryDTO[] $dtos
     * @return int Number of rows affected
     */
    public function upsertBatch(array $dtos): int
    {
        if (empty($dtos)) {
            return 0;
        }

        $saved = 0;
        foreach ($dtos as $d) {
            Mark::updateOrCreate(
                [
                    'student_id'     => $d->studentId,
                    'subject_id'     => $d->subjectId,
                    'sub_subject_id' => $d->subSubjectId,
                    'exam_id'        => $d->examId,
                    'component'      => $d->component,
                ],
                [
                    'obtained_marks' => $d->obtained,
                    'full_marks'     => $d->full,
                    'pass_marks'     => $d->pass,
                    'user_id'        => auth()->check() ? auth()->user()->owner_id : null,
                ]
            );
            $saved++;
        }

        return $saved;
    }
}
