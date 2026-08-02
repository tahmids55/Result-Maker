<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tabulation Sheet</title>
    @php
        $studentCount = $chunks->first() ? $chunks->first()->count() : 4;
        
        $fontSizes = [
            10 => ['cell' => '8pt', 'header' => '7pt', 'name' => '7pt', 'school' => '12pt', 'title' => '10pt', 'padMarks' => '7px', 'padGrade' => '6px'],
            8  => ['cell' => '8.5pt', 'header' => '7.5pt', 'name' => '8pt', 'school' => '13pt', 'title' => '11pt', 'padMarks' => '10px', 'padGrade' => '9px'],
        ];
        $fs = $fontSizes[$studentCount] ?? $fontSizes[10];
    @endphp
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 15mm;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: {{ $fs['cell'] }}; 
            margin: 0 3px;
            padding: 0;
        }
        
        .page-break { page-break-after: always; }

        .school-header { text-align: center; margin-bottom: 2mm; }
        .school-name { font-size: {{ $fs['school'] }}; font-weight: bold; text-transform: uppercase; }
        .school-address { font-size: {{ $fs['header'] }}; }
        .sheet-title { font-size: {{ $fs['title'] }}; font-weight: bold; text-decoration: underline; margin: 1mm 0 2mm; }

        .meta-table { width: 100%; border: none; margin-bottom: 1mm; font-size: {{ $fs['cell'] }}; font-weight: bold; }
        .meta-table td { border: none; padding: 1mm 0; }

        table.tab { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed;
        }
        table.tab th, 
        table.tab td { 
            border: 1px solid #000; 
            text-align: center; 
            vertical-align: middle;
            line-height: 1.2;
            font-size: {{ $fs['cell'] }};
            overflow: hidden;
            word-wrap: break-word;
        }
        table.tab thead th {
            background-color: #dde3ea;
            font-weight: bold;
            font-size: {{ $fs['header'] }};
            padding: 2px 0;
        }
        .sh {
            background-color: #c8d0da !important;
            font-weight: bold;
            font-size: {{ $fs['header'] }};
            overflow: hidden;
        }
        .ch { font-size: {{ $fs['header'] }}; padding: 0 !important; }

        .rm td { font-size: {{ $fs['cell'] }}; padding: {{ $fs['padMarks'] }} 0; }
        .rg td { 
            font-weight: bold;
            border-bottom: 2px solid #000;
            font-size: {{ $fs['cell'] }};
            padding: {{ $fs['padGrade'] }} 0;
        }
        .sn { 
            text-align: left !important; 
            font-weight: bold; 
            font-size: {{ $fs['name'] }};
            padding-left: 2mm !important;
            padding-right: 1mm !important;
            overflow: hidden;
            word-wrap: break-word;
            line-height: 1.1;
        }
        .rb { font-weight: bold; }
        .tb { font-weight: bold; }
        .ft { color: red; }

        .signature-table { width: 100%; margin-top: 8mm; font-size: {{ $fs['header'] }}; font-weight: bold; }
        .signature-table td { border: none; padding: 0; }
        .sig-box { width: 45mm; border-top: 1px dashed #000; text-align: center; padding-top: 2px; }

        .ftr { width: 100%; font-size: {{ $fs['header'] }}; margin-top: 2mm; }
        .ftr td { border: none; padding: 1mm; }
    </style>
</head>
<body>

@php
    $columnStructure = [];
    $dataColCount = 0;
    
    foreach ($subjects as $subject) {
        $cols = [];
        if ($subject->has_sub_subjects && $subject->subSubjects->count() > 0) {
            $subIdx = 1;
            $subjectInit = strtoupper(substr(trim($subject->name), 0, 1));
            foreach ($subject->subSubjects as $sub) {
                $comps = $sub->exam_components ?? [];
                foreach ($comps as $compName => $compConfig) {
                    $compPrefix = strtoupper(substr($compName, 0, 2));
                    $cols[] = ['type' => 'comp', 'sub_id' => $sub->id, 'comp' => $compName, 'label' => "{$compPrefix} ({$subjectInit}{$subIdx})"];
                }
                $subIdx++;
            }
        } else {
            $comps = $subject->exam_components ?? [];
            foreach ($comps as $compName => $compConfig) {
                $cols[] = ['type' => 'comp', 'sub_id' => 0, 'comp' => $compName, 'label' => strtoupper(substr($compName, 0, 2))];
            }
        }
        $cols[] = ['type' => 'total', 'label' => 'T'];
        $columnStructure[] = ['subject' => $subject, 'cols' => $cols];
        $dataColCount += count($cols);
    }
    
    // Use percentages for widths to ensure dompdf strictly respects the layout
    // Roll: 3%, Total: 3.5%, GPA: 3.5%, Pos: 3%, Name: 14%
    // Total fixed = 27%
    $fixedPct = 27;
    $remainingPct = 100 - $fixedPct;
    $colPct = $dataColCount > 0 ? ($remainingPct / $dataColCount) : 2;
@endphp

@foreach($chunks as $pageIndex => $pageStudents)
    @if($pageIndex > 0)
        <div class="page-break"></div>
    @endif

    <div class="school-header">
        <div class="school-name">{{ $school->name ?? 'School Name' }}</div>
        @if(!empty($school->address))
            <div class="school-address">{{ $school->address }}</div>
        @endif
        <div class="sheet-title">TABULATION SHEET</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="text-align:left;">EXAMINATION: {{ strtoupper($exam->name) }} {{ $exam->year }}</td>
            <td style="text-align:center;">CLASS: {{ strtoupper($class->name) }}</td>
            <td style="text-align:right;">SECTION: {{ strtoupper($section->name) }}</td>
        </tr>
    </table>

    <table class="tab">
        <thead>
            <tr>
                <th rowspan="3" style="width: 3%;">Sl</th>
                <th rowspan="3" style="width: 14%; text-align:left; padding-left:2mm;">Student Name</th>
                @foreach($columnStructure as $cs)
                    @php
                        $sn = $cs['subject']->name;
                        $words = preg_split('/[\s&\/]+/', $sn);
                        if (strlen($sn) > (count($cs['cols']) * 3)) {
                            if (count($words) >= 3) {
                                $sn = implode('', array_map(fn($w) => strtoupper(substr($w, 0, 1)), $words));
                            } elseif (count($words) == 2) {
                                $sn = strtoupper(substr($words[0], 0, 3)) . '.' . strtoupper(substr($words[1], 0, 3));
                            }
                        }
                        // The subject header spans multiple columns, its width is implicit by its children
                    @endphp
                    <th colspan="{{ count($cs['cols']) }}" class="sh">{{ strtoupper($sn) }}@if($cs['subject']->is_optional)<sup>*</sup>@endif</th>
                @endforeach
                <th rowspan="3" style="width: 3.5%;">TOT</th>
                <th rowspan="3" style="width: 3.5%;">GPA</th>
                <th rowspan="3" style="width: 3%;">POS</th>
            </tr>
            <tr>
                @foreach($columnStructure as $cs)
                    @foreach($cs['cols'] as $col)
                        <th class="ch" style="width: {{ $colPct }}%;">{{ $col['label'] }}</th>
                    @endforeach
                @endforeach
            </tr>
            <tr>
                @foreach($columnStructure as $cs)
                    <th colspan="{{ count($cs['cols']) }}" class="ch">G</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach($pageStudents as $student)
                @php
                    $result = $results->get($student->id);
                    $sdMap = [];
                    if ($result && $result->subject_details) {
                        foreach ($result->subject_details as $sd) {
                            $sdMap[$sd['subject_id']] = $sd;
                        }
                    }
                @endphp

                <tr class="rm">
                    <td class="rb" rowspan="2">{{ $student->roll }}</td>
                    <td class="sn" rowspan="2">{{ $student->name }}</td>
                    @foreach($columnStructure as $cs)
                        @php $sd = $sdMap[$cs['subject']->id] ?? null; @endphp
                        @foreach($cs['cols'] as $col)
                            @if($col['type'] === 'total')
                                <td class="tb">{{ $sd ? round($sd['obtained']) : '' }}</td>
                            @else
                                @php
                                    $val = '';
                                    if ($sd) {
                                        if (!empty($sd['sub_subjects'])) {
                                            foreach ($sd['sub_subjects'] as $ssD) {
                                                if (($ssD['sub_subject_id'] ?? null) == $col['sub_id'] && isset($ssD['components'][$col['comp']])) {
                                                    $cd = $ssD['components'][$col['comp']];
                                                    $val = is_array($cd) ? ($cd['obtained'] ?? '') : $cd;
                                                }
                                            }
                                        } elseif (isset($sd['components'][$col['comp']])) {
                                            $cd = $sd['components'][$col['comp']];
                                            $val = is_array($cd) ? ($cd['obtained'] ?? '') : $cd;
                                        }
                                    }
                                @endphp
                                <td>{{ $val }}</td>
                            @endif
                        @endforeach
                    @endforeach
                    <td class="tb" rowspan="2">{{ $result ? round($result->total_marks) : '' }}</td>
                    <td class="tb" rowspan="2">{{ $result ? number_format($result->gpa, 2) : '' }}</td>
                    <td class="tb" rowspan="2">{{ $result ? $result->rank : '' }}</td>
                </tr>
                <tr class="rg">
                    @foreach($columnStructure as $cs)
                        @php
                            $sd = $sdMap[$cs['subject']->id] ?? null;
                            $grade = $sd ? $sd['grade'] : '';
                            $isFail = $sd && !$sd['is_passed'] && !$cs['subject']->is_optional;
                        @endphp
                        <td colspan="{{ count($cs['cols']) }}" class="{{ $isFail ? 'ft' : '' }}">{{ $grade }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td style="text-align:left;">
                <div class="sig-box">
                    Class Teacher Signature
                </div>
            </td>
            <td style="text-align:right;">
                <div class="sig-box" style="float:right;">
                    Head Teacher Signature
                </div>
                <div style="clear:both;"></div>
            </td>
        </tr>
    </table>

    <table class="ftr">
        <tr>
            <td style="text-align:left;">Page: {{ $pageIndex + 1 }}({{ $chunks->count() }})</td>
            <td style="text-align:right;">Print Date: {{ now()->format('d.m.Y') }}</td>
        </tr>
    </table>

@endforeach

</body>
</html>
