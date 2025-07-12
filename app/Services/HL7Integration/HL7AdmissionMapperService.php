<?php

namespace App\Services\HL7Integration;

use App\Models\Ward;
use App\Models\Consultant;
use App\Models\User;
use App\Models\Specialty;
use App\Models\Hospital;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HL7AdmissionMapperService
{
    /**
     * Map parsed HL7 data to admission format
     *
     * @param array $parsedData
     * @return array|null
     */
    public function mapToAdmission(array $parsedData): ?array
    {
        try {
            $mappedData = [
                'patient' => $this->mapPatientData($parsedData),
                'admission' => $this->mapAdmissionData($parsedData)
            ];

            // Validate required fields
            if (!$this->validateMappedData($mappedData)) {
                throw new Exception('Required fields missing in mapped data');
            }

            return $mappedData;

        } catch (Exception $e) {
            Log::error('HL7 Mapping Error', [
                'error' => $e->getMessage(),
                'parsed_data' => $parsedData,
            ]);

            return null;
        }
    }

    /**
     * Map patient data from HL7 to SmartWard format
     *
     * @param array $parsedData
     * @return array
     */
    protected function mapPatientData(array $parsedData): array
    {
        $patientData = $parsedData['patient'] ?? [];
        
        // Generate identity_number - use MRN if no identity_number or patient_id is provided
        $identityNumber = $patientData['identity_number'] ?? $patientData['patient_id'] ?? '';
        if (empty($identityNumber)) {
            $identityNumber = $patientData['mrn'] ?? '';
        }
        
        $mapped = [
            'mrn' => $patientData['mrn'] ?? '',
            'first_name' => $patientData['first_name'] ?? '',
            'last_name' => $patientData['last_name'] ?? '',
            'middle_name' => $patientData['middle_name'] ?? '',
            'full_name' => $patientData['full_name'] ?? '',
            'date_of_birth' => $patientData['date_of_birth'] ?? null,
            'gender' => $this->mapGender($patientData['gender'] ?? ''),
            'phone' => $patientData['phone'] ?? '',
            'address' => $patientData['address'] ?? '',
            'city' => $patientData['city'] ?? '',
            'state' => $patientData['state'] ?? '',
            'postal_code' => $patientData['postal_code'] ?? '',
            'race' => $patientData['race'] ?? '',
            'allergies' => $this->mapAllergies($patientData['allergies'] ?? []),
            'is_admitted' => true,
            'admission_date' => now(),
            // Add required database fields with defaults
            'identity_number' => $identityNumber,
            'emergency_contact_name' => $patientData['emergency_contact_name'] ?? '',
            'emergency_contact_phone' => $patientData['emergency_contact_phone'] ?? '',
            'emergency_contact_relationship' => $patientData['emergency_contact_relationship'] ?? '',
            'insurance_provider' => $patientData['insurance_provider'] ?? '',
            'insurance_number' => $patientData['insurance_number'] ?? '',
            'occupation' => $patientData['occupation'] ?? '',
            'marital_status' => $patientData['marital_status'] ?? '',
            'nationality' => $patientData['nationality'] ?? '',
            'religion' => $patientData['religion'] ?? '',
        ];

        // Generate full name if not provided
        if (empty($mapped['full_name'])) {
            $mapped['full_name'] = trim($mapped['first_name'] . ' ' . $mapped['middle_name'] . ' ' . $mapped['last_name']);
        }

        // Add the 'name' field for database compatibility
        $mapped['name'] = $mapped['full_name'];

        return $mapped;
    }

    /**
     * Map admission data from HL7 to SmartWard format
     *
     * @param array $parsedData
     * @return array
     */
    protected function mapAdmissionData(array $parsedData): array
    {
        $admissionData = $parsedData['admission'] ?? [];
        
        $mapped = [
            'ward_id' => $this->mapWardId($admissionData),
            'bed_id' => $this->mapBedId($admissionData),
            'consultant_id' => $this->mapConsultantId($admissionData),
            'nurse_id' => $this->mapNurseId($admissionData),
            'admission_date' => $this->mapAdmissionDate($admissionData),
            'patient_class' => $this->mapPatientClass($admissionData['patient_class'] ?? ''),
            'admission_type' => $admissionData['admission_type'] ?? '',
            'diet_type' => $this->mapDietType($admissionData),
            'fall_risk_alert' => $this->mapFallRiskAlert($admissionData),
            'isolation_precautions' => $this->mapIsolationPrecautions($admissionData),
            'clinical_alerts' => $this->mapClinicalAlerts($admissionData),
            'expected_discharge_date' => null,
            'expected_length_of_stay' => null,
        ];

        return $mapped;
    }

    /**
     * Map gender from HL7 to SmartWard format
     *
     * @param string $hl7Gender
     * @return string
     */
    protected function mapGender(string $hl7Gender): string
    {
        $genderMap = [
            'M' => 'Male',
            'F' => 'Female',
            'O' => 'Other',
            'U' => 'Unknown'
        ];

        return $genderMap[strtoupper($hl7Gender)] ?? 'Unknown';
    }

    /**
     * Map allergies from HL7 format
     *
     * @param array $hl7Allergies
     * @return array
     */
    protected function mapAllergies(array $hl7Allergies): array
    {
        $mappedAllergies = [];
        
        foreach ($hl7Allergies as $allergy) {
            $mappedAllergies[] = [
                'allergen' => $allergy['allergen'] ?? '',
                'description' => $allergy['description'] ?? '',
                'severity' => $allergy['severity'] ?? '',
                'reaction' => $allergy['reaction'] ?? '',
                'type' => $allergy['type'] ?? '',
            ];
        }

        return $mappedAllergies;
    }

    /**
     * Map ward ID from HL7 data
     *
     * @param array $admissionData
     * @return int|null
     */
    protected function mapWardId(array $admissionData): ?int
    {
        $wardName = $admissionData['ward'] ?? '';
        $facility = $admissionData['facility'] ?? '';
        
        if (empty($wardName)) {
            // Try to find a default ward
            $ward = Ward::where('is_active', true)->first();
            return $ward ? $ward->id : null;
        }

        // Try to find ward by name
        $ward = Ward::where('name', 'like', "%{$wardName}%")
            ->where('is_active', true)
            ->first();

        if ($ward) {
            return $ward->id;
        }

        // Try to find ward by facility if provided
        if (!empty($facility)) {
            $hospital = Hospital::where('name', 'like', "%{$facility}%")->first();
            if ($hospital) {
                $ward = Ward::where('hospital_id', $hospital->id)
                    ->where('is_active', true)
                    ->first();
                if ($ward) {
                    return $ward->id;
                }
            }
        }

        // Return default ward if no match found
        $defaultWard = Ward::where('is_active', true)->first();
        return $defaultWard ? $defaultWard->id : null;
    }

    /**
     * Map bed ID from HL7 data
     *
     * @param array $admissionData
     * @return int|null
     */
    protected function mapBedId(array $admissionData): ?int
    {
        $bedNumber = $admissionData['bed'] ?? '';
        $wardName = $admissionData['ward'] ?? '';
        $room = $admissionData['room'] ?? '';
        
        if (empty($bedNumber)) {
            // Try to find any available bed
            $bed = \App\Models\Bed::where('status', 'available')
                ->where('is_active', true)
                ->first();
            return $bed ? $bed->id : null;
        }

        // Try to find bed by number
        $bed = \App\Models\Bed::where('bed_number', $bedNumber)
            ->where('is_active', true)
            ->first();

        if ($bed) {
            return $bed->id;
        }

        // Try to find bed by number with ward context
        if (!empty($wardName)) {
            $ward = Ward::where('name', 'like', "%{$wardName}%")
                ->where('is_active', true)
                ->first();
            
            if ($ward) {
                $bed = \App\Models\Bed::where('bed_number', $bedNumber)
                    ->where('ward_id', $ward->id)
                    ->where('is_active', true)
                    ->first();
                
                if ($bed) {
                    return $bed->id;
                }
                
                // Try to find any available bed in the ward
                $bed = \App\Models\Bed::where('ward_id', $ward->id)
                    ->where('status', 'available')
                    ->where('is_active', true)
                    ->first();
                
                if ($bed) {
                    return $bed->id;
                }
            }
        }

        // Return any available bed as fallback
        $fallbackBed = \App\Models\Bed::where('status', 'available')
            ->where('is_active', true)
            ->first();
        return $fallbackBed ? $fallbackBed->id : null;
    }

    /**
     * Map consultant ID from HL7 data
     *
     * @param array $admissionData
     * @return int|null
     */
    protected function mapConsultantId(array $admissionData): ?int
    {
        $doctorId = $admissionData['attending_doctor_id'] ?? $admissionData['admitting_doctor_id'] ?? '';
        $doctorName = $admissionData['attending_doctor_name'] ?? $admissionData['admitting_doctor_name'] ?? '';
        
        // Try to find consultant by name if provided
        if (!empty($doctorName)) {
            $consultant = Consultant::where('name', 'like', "%{$doctorName}%")
                ->where('is_active', true)
                ->first();
            if ($consultant) {
                return $consultant->id;
            }
        }

        // If doctor ID is provided, try to find by name similarity
        if (!empty($doctorId)) {
            $consultant = Consultant::where('name', 'like', "%{$doctorId}%")
                ->where('is_active', true)
                ->first();
            if ($consultant) {
                return $consultant->id;
            }
        }

        // Return default consultant if no match found
        $defaultConsultant = Consultant::where('is_active', true)->first();
        return $defaultConsultant ? $defaultConsultant->id : null;
    }

    /**
     * Map nurse ID from HL7 data
     *
     * @param array $admissionData
     * @return int|null
     */
    protected function mapNurseId(array $admissionData): ?int
    {
        $operatorId = $admissionData['operator_id'] ?? '';
        $operatorName = $admissionData['operator_name'] ?? '';
        
        // Try to find nurse by name if provided
        if (!empty($operatorName)) {
            $nurse = User::whereHas('roles', function ($query) {
                $query->where('name', 'nurse');
            })->where('name', 'like', "%{$operatorName}%")->first();
            
            if ($nurse) {
                return $nurse->id;
            }
        }

        // If operator ID is provided, try to find by name similarity
        if (!empty($operatorId)) {
            $nurse = User::whereHas('roles', function ($query) {
                $query->where('name', 'nurse');
            })->where('name', 'like', "%{$operatorId}%")->first();
            
            if ($nurse) {
                return $nurse->id;
            }
        }

        // Return default nurse if no match found
        $defaultNurse = User::whereHas('roles', function ($query) {
            $query->where('name', 'nurse');
        })->first();
        
        return $defaultNurse ? $defaultNurse->id : null;
    }

    /**
     * Map admission date from HL7 data
     *
     * @param array $admissionData
     * @return string
     */
    protected function mapAdmissionDate(array $admissionData): string
    {
        $admissionDate = $admissionData['admission_date'] ?? $admissionData['recorded_date'] ?? '';
        
        if (!empty($admissionDate)) {
            try {
                return Carbon::parse($admissionDate)->setTimezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                Log::warning('Invalid admission date format', ['date' => $admissionDate]);
            }
        }

        return now()->setTimezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
    }

    /**
     * Map patient class from HL7 to SmartWard format
     *
     * @param string $hl7PatientClass
     * @return string
     */
    protected function mapPatientClass(string $hl7PatientClass): string
    {
        $classMap = [
            'I' => 'I',  // Inpatient
            'O' => 'O',  // Outpatient
            'A' => 'A',  // Ambulatory
            'E' => 'E',  // Emergency
            'N' => 'N',  // Not applicable
            'R' => 'R',  // Recurring patient
            'B' => 'B',  // Obstetrics
            'C' => 'C',  // Commercial Account
            'U' => 'U',  // Unknown
        ];

        return $classMap[strtoupper($hl7PatientClass)] ?? 'I';
    }

    /**
     * Map diet type from HL7 data
     *
     * @param array $admissionData
     * @return string|null
     */
    protected function mapDietType(array $admissionData): ?string
    {
        // This would typically come from OBX segments or other clinical data
        // For now, return default
        return 'REG'; // Regular diet
    }

    /**
     * Map fall risk alert from HL7 data
     *
     * @param array $admissionData
     * @return string
     */
    protected function mapFallRiskAlert(array $admissionData): string
    {
        // This would typically come from clinical assessment data
        // For now, return default
        return 'NO';
    }

    /**
     * Map isolation precautions from HL7 data
     *
     * @param array $admissionData
     * @return string
     */
    protected function mapIsolationPrecautions(array $admissionData): string
    {
        // This would typically come from clinical data
        // For now, return default
        return 'NONE';
    }

    /**
     * Map clinical alerts from HL7 data
     *
     * @param array $admissionData
     * @return string|null
     */
    protected function mapClinicalAlerts(array $admissionData): ?string
    {
        $alerts = [];
        
        // Check for any clinical alerts in the data
        if (!empty($admissionData['admission_type'])) {
            $alerts[] = "Admission Type: " . $admissionData['admission_type'];
        }
        
        if (!empty($admissionData['attending_doctor_name'])) {
            $alerts[] = "Attending Doctor: " . $admissionData['attending_doctor_name'];
        }

        return !empty($alerts) ? implode('; ', $alerts) : null;
    }

    /**
     * Validate mapped data for required fields
     *
     * @param array $mappedData
     * @return bool
     */
    protected function validateMappedData(array $mappedData): bool
    {
        $requiredPatientFields = ['mrn', 'first_name', 'last_name'];
        $requiredAdmissionFields = ['ward_id', 'admission_date'];

        // Check required patient fields
        foreach ($requiredPatientFields as $field) {
            if (empty($mappedData['patient'][$field])) {
                Log::error('Missing required patient field', ['field' => $field]);
                return false;
            }
        }

        // Check required admission fields
        foreach ($requiredAdmissionFields as $field) {
            if (empty($mappedData['admission'][$field])) {
                Log::error('Missing required admission field', ['field' => $field]);
                return false;
            }
        }

        // Log warnings for missing optional but important fields
        if (empty($mappedData['admission']['consultant_id'])) {
            Log::warning('No consultant assigned for admission', ['patient_mrn' => $mappedData['patient']['mrn']]);
        }

        if (empty($mappedData['admission']['bed_id'])) {
            Log::warning('No bed assigned for admission', ['patient_mrn' => $mappedData['patient']['mrn']]);
        }

        return true;
    }

    /**
     * Get mapping summary for logging
     *
     * @param array $parsedData
     * @param array $mappedData
     * @return array
     */
    public function getMappingSummary(array $parsedData, array $mappedData): array
    {
        return [
            'source' => [
                'patient_name' => $parsedData['patient']['full_name'] ?? 'Unknown',
                'mrn' => $parsedData['patient']['mrn'] ?? 'Unknown',
                'ward' => $parsedData['admission']['ward'] ?? 'Unknown',
                'bed' => $parsedData['admission']['bed'] ?? 'Unknown',
                'room' => $parsedData['admission']['room'] ?? 'Unknown',
                'facility' => $parsedData['admission']['facility'] ?? 'Unknown',
            ],
            'mapped' => [
                'patient_name' => $mappedData['patient']['full_name'] ?? 'Unknown',
                'mrn' => $mappedData['patient']['mrn'] ?? 'Unknown',
                'ward_id' => $mappedData['admission']['ward_id'] ?? 'Unknown',
                'bed_id' => $mappedData['admission']['bed_id'] ?? 'Unknown',
                'consultant_id' => $mappedData['admission']['consultant_id'] ?? 'Unknown',
                'nurse_id' => $mappedData['admission']['nurse_id'] ?? 'Unknown',
                'admission_date' => $mappedData['admission']['admission_date'] ?? 'Unknown',
            ],
        ];
    }

    /**
     * Get available mapping options for configuration
     *
     * @return array
     */
    public function getMappingOptions(): array
    {
        return [
            'wards' => Ward::where('is_active', true)->pluck('name', 'id')->toArray(),
            'consultants' => Consultant::where('is_active', true)->pluck('name', 'id')->toArray(),
            'nurses' => User::whereHas('roles', function ($query) {
                $query->where('name', 'nurse');
            })->pluck('name', 'id')->toArray(),
            'hospitals' => Hospital::pluck('name', 'id')->toArray(),
            'specialties' => Specialty::pluck('name', 'id')->toArray(),
        ];
    }
} 