<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientAdmission;
use App\Models\Bed;
use App\Models\Ward;
use App\Models\Consultant;
use App\Models\Hospital;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class IntegrationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Apply auth middleware to all methods except the HL7 integrator endpoint
        $this->middleware(['auth', 'role:super-admin'])->except(['processHL7IntegratorAdmission']);
    }

    /**
     * Display the admission integration page.
     *
     * @return \Illuminate\Http\Response
     */
    public function admission()
    {
        return view('admin.integration.admission');
    }

    /**
     * Process HL7 admission message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processAdmission(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'hl7_message' => 'required|string',
            ]);

            // Parse the HL7 JSON message
            $hl7Data = json_decode($request->hl7_message, true);

            if (!$hl7Data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON format. Please provide valid HL7 JSON message.',
                ], 400);
            }

            // Validate HL7 message structure
            $validationResult = $this->validateHl7Structure($hl7Data);
            if (!$validationResult['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validationResult['message'],
                ], 400);
            }

            // Process the admission
            DB::beginTransaction();

            $result = $this->processHl7Admission($hl7Data);

            if ($result['success']) {
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'patient_info' => $result['patient_info'] ?? null,
                    'admission_info' => $result['admission_info'] ?? null,
                ]);
            } else {
                DB::rollBack();
                
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 422);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('HL7 Admission Processing Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the admission. Please check the logs and try again.',
            ], 500);
        }
    }

    /**
     * Process HL7 admission message from ADT Integrator.
     * This method handles the JSON format sent directly from the HL7 ADT frontend.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processHL7IntegratorAdmission(Request $request)
    {
        $logEntries = [];
        $startTime = microtime(true);
        
        try {
            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'INFO', 'message' => 'HL7 ADT Integration Request Started'];
            
            // Log the incoming request for debugging
            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'DEBUG', 'message' => 'Request Headers: ' . json_encode($request->headers->all())];
            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'DEBUG', 'message' => 'Request Size: ' . strlen($request->getContent()) . ' bytes'];
            
            Log::info('HL7 ADT Integration Request', [
                'headers' => $request->headers->all(),
                'content_length' => strlen($request->getContent()),
                'all_data' => $request->all(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'INFO', 'message' => 'Validating HL7 data structure...'];

            // Validate the input from HL7 ADT integrator
            $request->validate([
                'message_id' => 'required|string',
                'message_type' => 'required|string',
                'trigger_event' => 'required|string',
                'message_control_id' => 'required|string',
                'parsed_data' => 'required|array',
                'parsed_data.segments' => 'required|array',
                'raw_message' => 'sometimes|string',
                'timestamp' => 'sometimes|string',
                'source' => 'sometimes|string'
            ]);

            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'SUCCESS', 'message' => 'Validation passed'];
            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'INFO', 'message' => 'Message ID: ' . $request->input('message_id')];
            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'INFO', 'message' => 'Message Type: ' . $request->input('message_type') . '-' . $request->input('trigger_event')];
            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'INFO', 'message' => 'Control ID: ' . $request->input('message_control_id')];

            // Extract HL7 data from the request
            $hl7Data = [
                'message_type' => $request->input('message_type'),
                'trigger_event' => $request->input('trigger_event'),
                'message_control_id' => $request->input('message_control_id'),
                'segments' => $request->input('parsed_data.segments'),
                'raw_message' => $request->input('raw_message', ''),
                'source' => $request->input('source', 'HL7_ADT_Integrator'),
                'timestamp' => $request->input('timestamp', now()->toISOString())
            ];

            // Log segments found
            $segments = array_keys($hl7Data['segments']);
            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'INFO', 'message' => 'Segments found: ' . implode(', ', $segments)];

            // Validate HL7 message structure
            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'INFO', 'message' => 'Validating HL7 message structure...'];
            $validationResult = $this->validateHL7IntegratorStructure($hl7Data);
            if (!$validationResult['valid']) {
                $logEntries[] = ['time' => now()->toISOString(), 'level' => 'ERROR', 'message' => 'HL7 validation failed: ' . $validationResult['message']];
                
                return response()->json([
                    'success' => false,
                    'message' => $validationResult['message'],
                    'logs' => $logEntries,
                    'processing_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms'
                ], 400);
            }

            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'SUCCESS', 'message' => 'HL7 structure validation passed'];

            // Extract patient information
            if (isset($hl7Data['segments']['PID'])) {
                $pidData = $hl7Data['segments']['PID'];
                $logEntries[] = ['time' => now()->toISOString(), 'level' => 'INFO', 'message' => 'Patient Name: ' . ($pidData['patient_name'] ?? 'Unknown')];
                $logEntries[] = ['time' => now()->toISOString(), 'level' => 'INFO', 'message' => 'Patient ID: ' . ($pidData['patient_id'] ?? 'Unknown')];
            }

            // Process the admission
            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'INFO', 'message' => 'Starting database transaction...'];
            DB::beginTransaction();

            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'INFO', 'message' => 'Processing HL7 admission data...'];
            $result = $this->processHl7Admission($hl7Data);

            if ($result['success']) {
                DB::commit();
                $logEntries[] = ['time' => now()->toISOString(), 'level' => 'SUCCESS', 'message' => 'Database transaction committed'];
                $logEntries[] = ['time' => now()->toISOString(), 'level' => 'SUCCESS', 'message' => $result['message']];
                
                Log::info('HL7 ADT Integration Success', [
                    'message_id' => $request->input('message_id'),
                    'patient_info' => $result['patient_info'] ?? null,
                    'admission_info' => $result['admission_info'] ?? null,
                    'processing_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms'
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'patient_info' => $result['patient_info'] ?? null,
                    'admission_info' => $result['admission_info'] ?? null,
                    'integration_status' => 'success',
                    'processed_at' => now()->toISOString(),
                    'logs' => $logEntries,
                    'processing_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms'
                ]);
            } else {
                DB::rollBack();
                $logEntries[] = ['time' => now()->toISOString(), 'level' => 'ERROR', 'message' => 'Database transaction rolled back'];
                $logEntries[] = ['time' => now()->toISOString(), 'level' => 'ERROR', 'message' => 'Processing failed: ' . $result['message']];
                
                Log::warning('HL7 ADT Integration Failed', [
                    'message_id' => $request->input('message_id'),
                    'error' => $result['message'],
                    'processing_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms'
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'integration_status' => 'failed',
                    'processed_at' => now()->toISOString(),
                    'logs' => $logEntries,
                    'processing_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms'
                ], 422);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'ERROR', 'message' => 'Validation Error: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors()))];
            
            Log::error('HL7 ADT Integration Validation Error', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'processing_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors())),
                'integration_status' => 'failed',
                'validation_errors' => $e->errors(),
                'logs' => $logEntries,
                'processing_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms'
            ], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'ERROR', 'message' => 'Exception: ' . $e->getMessage()];
            $logEntries[] = ['time' => now()->toISOString(), 'level' => 'ERROR', 'message' => 'File: ' . $e->getFile() . ':' . $e->getLine()];
            
            Log::error('HL7 ADT Integration Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'processing_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the HL7 message: ' . $e->getMessage(),
                'integration_status' => 'failed',
                'processed_at' => now()->toISOString(),
                'logs' => $logEntries,
                'processing_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Validate HL7 message structure.
     *
     * @param  array  $hl7Data
     * @return array
     */
    private function validateHl7Structure($hl7Data)
    {
        // Check for required top-level fields
        $requiredFields = ['message_type', 'trigger_event', 'segments'];
        foreach ($requiredFields as $field) {
            if (!isset($hl7Data[$field])) {
                return [
                    'valid' => false,
                    'message' => "Missing required field: {$field}"
                ];
            }
        }

        // Check message type and trigger event for admission
        if ($hl7Data['message_type'] !== 'ADT') {
            return [
                'valid' => false,
                'message' => 'Invalid message type. Expected ADT (Admission/Discharge/Transfer).'
            ];
        }

        if (!in_array($hl7Data['trigger_event'], ['A01', 'A02', 'A03', 'A04', 'A05'])) {
            return [
                'valid' => false,
                'message' => 'Invalid trigger event. Expected admission events (A01-A05).'
            ];
        }

        // Check for required segments
        $requiredSegments = ['MSH', 'PID', 'PV1'];
        foreach ($requiredSegments as $segment) {
            if (!isset($hl7Data['segments'][$segment])) {
                return [
                    'valid' => false,
                    'message' => "Missing required segment: {$segment}"
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Validate HL7 message structure from ADT integrator.
     *
     * @param  array  $hl7Data
     * @return array
     */
    private function validateHL7IntegratorStructure($hl7Data)
    {
        // Check for required top-level fields
        $requiredFields = ['message_type', 'trigger_event', 'segments'];
        foreach ($requiredFields as $field) {
            if (!isset($hl7Data[$field])) {
                return [
                    'valid' => false,
                    'message' => "Missing required field: {$field}"
                ];
            }
        }

        // Check message type and trigger event for admission
        if ($hl7Data['message_type'] !== 'ADT') {
            return [
                'valid' => false,
                'message' => 'Invalid message type. Expected ADT (Admission/Discharge/Transfer).'
            ];
        }

        if (!in_array($hl7Data['trigger_event'], ['A01', 'A02', 'A03', 'A04', 'A05', 'A08'])) {
            return [
                'valid' => false,
                'message' => 'Invalid trigger event. Expected admission/update events (A01-A05, A08).'
            ];
        }

        // Check for required segments
        $requiredSegments = ['MSH', 'PID'];
        foreach ($requiredSegments as $segment) {
            if (!isset($hl7Data['segments'][$segment])) {
                return [
                    'valid' => false,
                    'message' => "Missing required segment: {$segment}"
                ];
            }
        }

        // PV1 is optional for some message types but preferred
        if (!isset($hl7Data['segments']['PV1'])) {
            Log::info('HL7 message without PV1 segment - limited admission processing available');
        }

        return ['valid' => true];
    }

    /**
     * Process HL7 admission data.
     *
     * @param  array  $hl7Data
     * @return array
     */
    private function processHl7Admission($hl7Data)
    {
        $segments = $hl7Data['segments'];

        // Extract patient information from PID segment
        $pidData = $segments['PID'];
        $patientResult = $this->processPatientData($pidData);

        if (!$patientResult['success']) {
            return $patientResult;
        }

        $patient = $patientResult['patient'];

        // Check if patient is already admitted
        if ($patient->is_admitted) {
            $currentBed = $patient->bed;
            $currentWard = $currentBed ? $currentBed->ward : null;
            
            $errorMessage = "Patient {$patient->name} (MRN: {$patient->mrn}) is already admitted";
            if ($currentBed && $currentWard) {
                $errorMessage .= " to {$currentWard->name} (Bed {$currentBed->bed_number})";
            }
            
            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }

        // Extract admission information from PV1 segment
        $pv1Data = $segments['PV1'];
        $admissionResult = $this->processAdmissionData($patient, $pv1Data, $segments);

        if (!$admissionResult['success']) {
            return $admissionResult;
        }

        // Process allergies if AL1 segments exist
        if (isset($segments['AL1'])) {
            $this->processAllergies($patient, $segments['AL1']);
        }

        // Process custom Z segments for alerts
        $alerts = $this->processCustomAlerts($segments);

        return [
            'success' => true,
            'message' => "Patient {$patient->name} successfully admitted to {$admissionResult['ward_name']} (Bed {$admissionResult['bed_number']})",
            'patient_info' => [
                'name' => $patient->name,
                'mrn' => $patient->mrn,
                'id' => $patient->id
            ],
            'admission_info' => [
                'ward' => $admissionResult['ward_name'],
                'bed_number' => $admissionResult['bed_number'],
                'admission_date' => $admissionResult['admission_date'],
                'alerts' => $alerts
            ]
        ];
    }

    /**
     * Process patient data from PID segment.
     *
     * @param  array  $pidData
     * @return array
     */
    private function processPatientData($pidData)
    {
        try {
            // Extract MRN from external_patient_id or internal_patient_id
            $mrn = $pidData['internal_patient_id'] ?? null;
            if (!$mrn && isset($pidData['external_patient_id'])) {
                // Extract MRN from external ID format (e.g., "123456789^^^MYS^MR")
                $externalParts = explode('^', $pidData['external_patient_id']);
                $mrn = $externalParts[0] ?? null;
            }

            if (!$mrn) {
                return [
                    'success' => false,
                    'message' => 'Patient MRN not found in PID segment.'
                ];
            }

            // Parse patient name
            $patientName = $this->parsePatientName($pidData['patient_name'] ?? '');
            if (!$patientName) {
                return [
                    'success' => false,
                    'message' => 'Patient name not found or invalid format.'
                ];
            }

            // Parse date of birth
            $dateOfBirth = $this->parseHl7Date($pidData['date_of_birth'] ?? '');

            // Parse gender
            $gender = $this->parseGender($pidData['gender'] ?? '');

            // Parse address
            $address = $this->parseAddress($pidData['patient_address'] ?? '');

            // Find or create patient
            $patient = Patient::where('mrn', $mrn)->first();

            if ($patient) {
                // Update existing patient
                $patient->update([
                    'name' => $patientName,
                    'date_of_birth' => $dateOfBirth,
                    'gender' => $gender,
                    'phone' => $pidData['phone_number_ext'] ?? $patient->phone,
                    'address' => $address['full'] ?? $patient->address,
                    'city' => $address['city'] ?? $patient->city,
                    'state' => $address['state'] ?? $patient->state,
                    'postal_code' => $address['postal_code'] ?? $patient->postal_code,
                    'country' => $address['country'] ?? $patient->country,
                    'religion' => $pidData['religion'] ?? $patient->religion,
                    'race' => $this->cleanRaceField($pidData['race'] ?? '') ?: $patient->race,
                ]);
            } else {
                // Create new patient
                $patient = Patient::create([
                    'mrn' => $mrn,
                    'name' => $patientName,
                    'date_of_birth' => $dateOfBirth,
                    'gender' => $gender,
                    'phone' => $pidData['phone_number_ext'] ?? null,
                    'address' => $address['full'] ?? null,
                    'city' => $address['city'] ?? null,
                    'state' => $address['state'] ?? null,
                    'postal_code' => $address['postal_code'] ?? null,
                    'country' => $address['country'] ?? 'Malaysia',
                    'religion' => $pidData['religion'] ?? null,
                    'race' => $this->cleanRaceField($pidData['race'] ?? ''),
                    'identity_type' => 'IC',
                    'identity_number' => $mrn, // Using MRN as identity for HL7 patients
                ]);
            }

            return [
                'success' => true,
                'patient' => $patient
            ];

        } catch (\Exception $e) {
            Log::error('Patient Data Processing Error', [
                'error' => $e->getMessage(),
                'pid_data' => $pidData
            ]);

            return [
                'success' => false,
                'message' => 'Error processing patient data: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process admission data from PV1 and PV2 segments.
     *
     * @param  Patient  $patient
     * @param  array  $pv1Data
     * @param  array  $segments
     * @return array
     */
    private function processAdmissionData($patient, $pv1Data, $segments)
    {
        try {
            // Extract bed information
            $bedCode = $pv1Data['assigned_location_bed_code'] ?? null;
            if (!$bedCode) {
                return [
                    'success' => false,
                    'message' => 'Bed assignment not found in PV1 segment.'
                ];
            }

            // Find the bed - try by code first, then by bed_number
            $bed = Bed::where('code', $bedCode)->first();
            if (!$bed) {
                $bed = Bed::where('bed_number', $bedCode)->first();
            }
            if (!$bed) {
                return [
                    'success' => false,
                    'message' => "Bed {$bedCode} not found in the system."
                ];
            }

            // Check if bed is available
            if ($bed->status !== 'available') {
                return [
                    'success' => false,
                    'message' => "Bed {$bedCode} is not available (current status: {$bed->status})."
                ];
            }

            // Parse admission date
            $admissionDate = $this->parseHl7DateTime($pv1Data['admit_date_time'] ?? '');
            if (!$admissionDate) {
                $admissionDate = now();
            }

            // Find consultant
            $consultant = null;
            if (isset($pv1Data['attending_doctor_code'])) {
                // First try to find by code
                $consultant = Consultant::where('code', $pv1Data['attending_doctor_code'])->first();
                
                // If not found by code and we have a doctor name, try searching by name
                if (!$consultant && isset($pv1Data['attending_doctor']) && $pv1Data['attending_doctor']) {
                    $consultant = Consultant::where('name', 'like', '%' . $pv1Data['attending_doctor'] . '%')->first();
                }
            }

            // Extract clinical data from PV2 if available
            $pv2Data = $segments['PV2'] ?? [];
            $expectedDischargeDate = null;
            $expectedLengthOfStay = null;

            if (isset($pv2Data['expected_discharge_date'])) {
                $expectedDischargeDate = $this->parseHl7DateTime($pv2Data['expected_discharge_date']);
            }

            if (isset($pv2Data['estimated_length_of_inpatient_stay'])) {
                $expectedLengthOfStay = (int) $pv2Data['estimated_length_of_inpatient_stay'];
            }

            // Update bed status
            $bed->update([
                'status' => 'occupied',
                'patient_id' => $patient->id,
                'consultant_id' => $consultant ? $consultant->id : null,
                'notes' => 'Admitted via HL7 integration',
            ]);

            // Create admission record
            $admission = PatientAdmission::create([
                'patient_id' => $patient->id,
                'ward_id' => $bed->ward_id,
                'bed_id' => $bed->id,
                'bed_number' => $bed->bed_number,
                'admission_date' => $admissionDate,
                'consultant_id' => $consultant ? $consultant->id : null,
                'admitted_by' => auth()->id(),
                'admission_notes' => 'Admitted via HL7 ADT integration',
                'is_active' => true,
                'diet_type' => $this->mapDietType($pv1Data['diet_type'] ?? null),
                'patient_class' => $pv1Data['patient_class'] ?? 'I',
                'expected_discharge_date' => $expectedDischargeDate,
                'expected_length_of_stay' => $expectedLengthOfStay,
                'fall_risk_alert' => $this->extractFallRisk($segments),
                'isolation_precautions' => $this->extractIsolationPrecautions($segments),
                'clinical_alerts' => $this->extractClinicalAlerts($segments),
            ]);

            return [
                'success' => true,
                'admission' => $admission,
                'ward_name' => $bed->ward->name,
                'bed_number' => $bed->bed_number,
                'admission_date' => $admissionDate->format('Y-m-d H:i:s')
            ];

        } catch (\Exception $e) {
            Log::error('Admission Data Processing Error', [
                'error' => $e->getMessage(),
                'pv1_data' => $pv1Data
            ]);

            return [
                'success' => false,
                'message' => 'Error processing admission data: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Parse patient name from HL7 format.
     *
     * @param  string  $nameString
     * @return string|null
     */
    private function parsePatientName($nameString)
    {
        if (!$nameString) {
            return null;
        }

        // HL7 format: "Family^Given^Middle^Suffix^Prefix"
        $nameParts = explode('^', $nameString);
        
        $family = $nameParts[0] ?? '';
        $given = $nameParts[1] ?? '';
        $middle = $nameParts[2] ?? '';
        $prefix = $nameParts[4] ?? '';

        // Construct full name
        $fullName = trim(implode(' ', array_filter([
            $prefix,
            $given,
            $middle,
            $family
        ])));

        return $fullName ?: null;
    }

    /**
     * Parse HL7 date format (YYYYMMDD).
     *
     * @param  string  $dateString
     * @return \Carbon\Carbon|null
     */
    private function parseHl7Date($dateString)
    {
        if (!$dateString || strlen($dateString) < 8) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Ymd', substr($dateString, 0, 8));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse HL7 datetime format (YYYYMMDDHHMMSS).
     *
     * @param  string  $datetimeString
     * @return \Carbon\Carbon|null
     */
    private function parseHl7DateTime($datetimeString)
    {
        if (!$datetimeString) {
            return null;
        }

        try {
            // Handle different lengths of datetime strings
            if (strlen($datetimeString) >= 14) {
                return Carbon::createFromFormat('YmdHis', substr($datetimeString, 0, 14));
            } elseif (strlen($datetimeString) >= 8) {
                return Carbon::createFromFormat('Ymd', substr($datetimeString, 0, 8));
            }
        } catch (\Exception $e) {
            Log::warning('Failed to parse HL7 datetime', [
                'datetime_string' => $datetimeString,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Parse gender from HL7 format.
     *
     * @param  string  $genderCode
     * @return string|null
     */
    private function parseGender($genderCode)
    {
        $genderMap = [
            'M' => 'male',
            'F' => 'female',
            'O' => 'other',
            'U' => 'unknown'
        ];

        return $genderMap[strtoupper($genderCode)] ?? null;
    }

    /**
     * Parse address from HL7 format.
     *
     * @param  string  $addressString
     * @return array
     */
    private function parseAddress($addressString)
    {
        if (!$addressString) {
            return [];
        }

        // HL7 format: "Street^City^State^Postal^Country^Type"
        $addressParts = explode('^', $addressString);

        return [
            'street' => $addressParts[0] ?? '',
            'city' => $addressParts[1] ?? '',
            'state' => $addressParts[2] ?? '',
            'postal_code' => $addressParts[3] ?? '',
            'country' => $addressParts[4] ?? '',
            'full' => trim(implode(', ', array_filter([
                $addressParts[0] ?? '',
                $addressParts[1] ?? '',
                $addressParts[2] ?? '',
                $addressParts[3] ?? ''
            ])))
        ];
    }

    /**
     * Clean race field by removing HL7 formatting.
     *
     * @param  string  $raceString
     * @return string|null
     */
    private function cleanRaceField($raceString)
    {
        if (!$raceString) {
            return null;
        }

        // Remove HL7 separator and get the readable part
        $raceParts = explode('^', $raceString);
        return trim($raceParts[1] ?? $raceParts[0]);
    }

    /**
     * Map diet type from HL7 to system values.
     *
     * @param  string|null  $dietType
     * @return string|null
     */
    private function mapDietType($dietType)
    {
        if (!$dietType) {
            return null;
        }

        $dietMap = [
            'VEGETARIAN' => 'VEG',
            'REGULAR' => 'REG',
            'DIABETIC' => 'DBT',
            'HALAL' => 'HSL',
            'KOSHER' => 'KSH',
        ];

        return $dietMap[strtoupper($dietType)] ?? null;
    }

    /**
     * Extract fall risk from custom segments.
     *
     * @param  array  $segments
     * @return string
     */
    private function extractFallRisk($segments)
    {
        if (isset($segments['ZAT']['fields'])) {
            foreach ($segments['ZAT']['fields'] as $field) {
                if ($field['field_value'] === 'FR' || 
                    stripos($field['field_value'], 'FALL') !== false) {
                    return 'FR';
                }
            }
        }

        return 'NO';
    }

    /**
     * Extract isolation precautions from custom segments.
     *
     * @param  array  $segments
     * @return string
     */
    private function extractIsolationPrecautions($segments)
    {
        if (isset($segments['ZIT']['fields'])) {
            foreach ($segments['ZIT']['fields'] as $field) {
                $value = strtoupper($field['field_value']);
                if (in_array($value, ['DAC', 'DC', 'AC', 'AD', 'DROP', 'AIR', 'CON'])) {
                    return $value;
                }
            }
        }

        return 'NONE';
    }

    /**
     * Extract clinical alerts from all segments.
     *
     * @param  array  $segments
     * @return string|null
     */
    private function extractClinicalAlerts($segments)
    {
        $alerts = [];

        // Check ZAT segment for alerts
        if (isset($segments['ZAT']['fields'])) {
            foreach ($segments['ZAT']['fields'] as $field) {
                if (isset($field['field_number']) && $field['field_number'] == 2) {
                    $alerts[] = $field['field_value'];
                }
            }
        }

        // Check ZIT segment for isolation alerts
        if (isset($segments['ZIT']['fields'])) {
            foreach ($segments['ZIT']['fields'] as $field) {
                if (isset($field['field_number']) && $field['field_number'] == 2) {
                    $alerts[] = $field['field_value'];
                }
            }
        }

        return !empty($alerts) ? implode('; ', $alerts) : null;
    }

    /**
     * Process allergies from AL1 segments.
     *
     * @param  Patient  $patient
     * @param  array  $allergyData
     * @return void
     */
    private function processAllergies($patient, $allergyData)
    {
        // This is a placeholder for allergy processing
        // You may want to create an Allergy model and store this data
        Log::info('Processing allergies for patient', [
            'patient_id' => $patient->id,
            'allergies' => $allergyData
        ]);
    }

    /**
     * Process custom alerts from Z segments.
     *
     * @param  array  $segments
     * @return array
     */
    private function processCustomAlerts($segments)
    {
        $alerts = [];

        // Process ZAT segment
        if (isset($segments['ZAT'])) {
            $alerts[] = [
                'type' => 'Alert',
                'code' => $segments['ZAT']['fields'][0]['field_value'] ?? '',
                'description' => $segments['ZAT']['fields'][1]['field_value'] ?? ''
            ];
        }

        // Process ZIT segment
        if (isset($segments['ZIT'])) {
            $alerts[] = [
                'type' => 'Isolation',
                'code' => $segments['ZIT']['fields'][0]['field_value'] ?? '',
                'description' => $segments['ZIT']['fields'][1]['field_value'] ?? ''
            ];
        }

        // Process ZFR segment
        if (isset($segments['ZFR'])) {
            $alerts[] = [
                'type' => 'Fall Risk',
                'level' => $segments['ZFR']['fields'][0]['field_value'] ?? ''
            ];
        }

        return $alerts;
    }
} 