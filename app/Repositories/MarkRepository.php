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

        $records = array_map(fn(MarkEntryDTO $d) => [
            'student_id'     => $d->studentId,
            'subject_id'     => $d->subjectId,
            'sub_subject_id' => $d->subSubjectId,
            'exam_id'        => $d->examId,
            'component'      => $d->component,
            'obtained_marks' => $d->obtained,
            'full_marks'     => $d->full,
            'pass_marks'     => $d->pass,
            'user_id'        => auth()->user()->owner_id,
            'updated_at'     => now(),
            'created_at'     => now(),
        ], $dtos);

        return Mark::upsert(
            $records,
            // Unique constraint columns
            ['student_id', 'subject_id', 'sub_subject_id', 'exam_id', 'component'],
            // Columns to update on conflict
            ['obtained_marks', 'full_marks', 'pass_marks', 'updated_at']
        );
    }
}
