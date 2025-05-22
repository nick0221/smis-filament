<?php

namespace Database\Factories;

use App\Models\Requirement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Requirement>
 */
class RequirementFactory extends Factory
{
    protected $model = Requirement::class;

    public function definition(): array
    {
        static $step = 0;

        $requirements = [
            // ✅ Basic Requirements
            ['document_name' => 'Birth Certificate (PSA or Local Civil Registrar)', 'document_description' => 'Original and photocopy for age and identity verification.'],
            ['document_name' => 'Certificate of Completion (Grade 6)', 'document_description' => 'Issued by the elementary school attended.'],
            ['document_name' => 'Form 138 (Report Card)', 'document_description' => 'Academic performance for the last completed grade level.'],
            ['document_name' => 'Form 137 (Permanent Record)', 'document_description' => 'Requested from the previous school.'],

            // 📎 Other Supporting Documents
            ['document_name' => '2x2 ID Photos', 'document_description' => 'At least 2 copies of recent ID photos.'],
            ['document_name' => 'Medical or Dental Record', 'document_description' => 'Recent check-up or school health clearance.'],
            ['document_name' => 'Barangay Clearance or Certificate of Residency', 'document_description' => 'Proof of residence for public school admission.'],
            ['document_name' => 'ESC or QVR Certificate', 'document_description' => 'For Grade 7 enrollees under government voucher programs.'],

            // 🔄 For Transferees
            ['document_name' => 'Transcript of Records (TOR)', 'document_description' => 'Official transcript from previous school.'],
            ['document_name' => 'Letter of Recommendation / Honorable Dismissal', 'document_description' => 'Issued by the school previously attended.'],
            ['document_name' => 'Authenticated School Records', 'document_description' => 'For students transferring from schools abroad.'],
            ['document_name' => 'Alien Certificate of Registration (ACR)', 'document_description' => 'Required for foreign students.'],

            // 🎓 For ALS Passers
            ['document_name' => 'ALS Certificate of Completion', 'document_description' => 'Issued after completion of the ALS program.'],
            ['document_name' => 'Certificate of Rating (PEPT or A&E)', 'document_description' => 'Test result certifying grade level equivalency.'],

            // 🏫 Optional School-Specific
            ['document_name' => 'Entrance Exam Result', 'document_description' => 'Result of the school-administered exam.'],
            ['document_name' => 'Interview Assessment', 'document_description' => 'Assessment based on interview with student or guardian.'],
            ['document_name' => 'Reservation Fee Receipt', 'document_description' => 'Proof of enrollment reservation payment.'],
            ['document_name' => 'Online Pre-registration Form', 'document_description' => 'Filled out via the school’s online portal.'],
        ];

        return $requirements[$step++];

    }
}
