<div class="report-card">
    <div class="title">STUDENT EVALUATION</div>

    <table class="identity">
        <tr>
            <td><strong>NAME</strong></td>
            <td>{{ $studentName }}</td>
            <td><strong>CLASS</strong></td>
            <td>{{ $className }}</td>
        </tr>
        <tr>
            <td><strong>DAYS</strong></td>
            <td>{{ $classDays }}</td>
            <td><strong>TIME</strong></td>
            <td>{{ $classTime }}</td>
        </tr>
    </table>

    <div class="section-title">WRITTEN TEST</div>
    <table class="scores">
        <tr>
            <th>Item</th>
            <th>SCORE</th>
        </tr>
        <tr><td>Listening</td><td>{{ $reportCard->score_listening ?? '-' }}</td></tr>
        <tr><td>Vocabulary</td><td>{{ $reportCard->score_vocabulary ?? '-' }}</td></tr>
        <tr><td>Structure</td><td>{{ $reportCard->score_structure ?? '-' }}</td></tr>
        <tr><td>Reading</td><td>{{ $reportCard->score_reading ?? '-' }}</td></tr>
        <tr><td>Writing</td><td>{{ $reportCard->score_writing ?? '-' }}</td></tr>
    </table>

    <div class="section-title">OVERALL CLASS ASSESMENT</div>
    <table class="scores">
        <tr>
            <th>Item</th>
            <th>GRADE</th>
        </tr>
        <tr><td>Pronunciation Fluency</td><td>{{ $reportCard->grade_pronunciation ?? '-' }}</td></tr>
        <tr><td>Sentence and Word Arrangement</td><td>{{ $reportCard->grade_sentence_arrangement ?? '-' }}</td></tr>
        <tr><td>Class Participation</td><td>{{ $reportCard->grade_class_participation ?? '-' }}</td></tr>
        <tr><td>Questioning Skill</td><td>{{ $reportCard->grade_questioning_skill ?? '-' }}</td></tr>
        <tr><td>Analyzing Skill</td><td>{{ $reportCard->grade_analyzing_skill ?? '-' }}</td></tr>
    </table>

    <table class="identity">
        <tr>
            <td><strong>TOTAL SCORE</strong></td>
            <td>{{ $reportCard->total_score ?? '-' }}</td>
            <td><strong>FINAL GRADE</strong></td>
            <td>{{ $reportCard->final_grade ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>NEXT CLASS</strong></td>
            <td colspan="3">{{ $reportCard->next_class ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Comments and Suggestions</div>
    <div class="comments">{{ $reportCard->comments ?? '-' }}</div>

    <table class="signatures">
        <tr>
            <td>Managing Director</td>
            <td>Academic Director</td>
            <td>Talent</td>
        </tr>
        <tr>
            <td>{{ $managingDirectorName }}</td>
            <td>{{ $academicDirectorName }}</td>
            <td>{{ $instructorName }}</td>
        </tr>
    </table>
</div>
