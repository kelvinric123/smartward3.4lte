<?php

namespace App\Services\HL7Integration;

use App\Models\HL7MessageLog;
use App\Services\HL7Integration\HL7ParserService;
use App\Services\HL7Integration\HL7AdmissionMapperService;
use Exception;
use Illuminate\Support\Facades\Log;

class HL7ListenerService
{
    protected $parser;
    protected $mapper;

    public function __construct(HL7ParserService $parser, HL7AdmissionMapperService $mapper)
    {
        $this->parser = $parser;
        $this->mapper = $mapper;
    }

    /**
     * Handle incoming HL7 message via HTTP
     *
     * @param string $rawMessage
     * @param array $headers
     * @return array
     */
    public function handleIncomingMessage(string $rawMessage, array $headers = []): array
    {
        $messageId = $this->generateMessageId();
        
        // Log the incoming message
        $messageLog = HL7MessageLog::create([
            'message_id' => $messageId,
            'raw_message' => $rawMessage,
            'headers' => $headers,
            'message_type' => 'ADT^A01', // Admission message type
            'status' => 'received',
            'received_at' => now(),
        ]);

        try {
            // Parse the HL7 message
            $parsedData = $this->parser->parse($rawMessage);
            
            if (!$parsedData) {
                throw new Exception('Failed to parse HL7 message');
            }

            // Update log with parsed data
            $messageLog->update([
                'parsed_data' => $parsedData,
                'status' => 'parsed',
                'processed_at' => now(),
            ]);

            // Map to admission data
            $admissionData = $this->mapper->mapToAdmission($parsedData);
            
            if (!$admissionData) {
                throw new Exception('Failed to map HL7 data to admission format');
            }

            // Update log with mapped data
            $messageLog->update([
                'mapped_data' => $admissionData,
                'status' => 'mapped',
            ]);

            // Process the admission
            $result = $this->processAdmission($admissionData, $messageLog);

            // Update final status
            $messageLog->update([
                'status' => $result['success'] ? 'processed' : 'failed',
                'error_message' => $result['success'] ? null : $result['error'],
                'admission_id' => $result['admission_id'] ?? null,
                'patient_mrn' => $admissionData['patient']['mrn'] ?? null,
                'completed_at' => now(),
            ]);

            return [
                'success' => true,
                'message_id' => $messageId,
                'admission_id' => $result['admission_id'] ?? null,
                'message' => $result['success'] ? 'HL7 message processed successfully' : $result['error'],
            ];

        } catch (Exception $e) {
            // Log error
            Log::error('HL7 Message Processing Error', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
                'raw_message' => $rawMessage,
            ]);

            // Update log with error
            $messageLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            return [
                'success' => false,
                'message_id' => $messageId,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process the admission based on mapped data
     *
     * @param array $admissionData
     * @param HL7MessageLog $messageLog
     * @return array
     */
    protected function processAdmission(array $admissionData, HL7MessageLog $messageLog): array
    {
        try {
            // Check if patient exists by MRN first
            $patient = \App\Models\Patient::where('mrn', $admissionData['patient']['mrn'])->first();
            
            if (!$patient) {
                // Check if patient exists by identity_number to avoid duplicate constraint violation
                $identityNumber = $admissionData['patient']['identity_number'];
                if (!empty($identityNumber)) {
                    $existingPatient = \App\Models\Patient::where('identity_number', $identityNumber)->first();
                    if ($existingPatient) {
                        // Update existing patient with new MRN and data
                        $existingPatient->update($admissionData['patient']);
                        $patient = $existingPatient;
                    }
                }
                
                // Create new patient if still not found
                if (!$patient) {
                    $patient = \App\Models\Patient::create($admissionData['patient']);
                }
            }

            // Check if patient is already admitted
            if ($patient->is_admitted) {
                return [
                    'success' => false,
                    'error' => 'Patient is already admitted',
                ];
            }

            // Find the specific bed or use mapped bed_id
            $bed = null;
            if (!empty($admissionData['admission']['bed_id'])) {
                $bed = \App\Models\Bed::where('id', $admissionData['admission']['bed_id'])
                    ->where('is_active', true)
                    ->first();
            }
            
            // If specific bed not found, find available bed in ward
            if (!$bed) {
                $bed = \App\Models\Bed::where('ward_id', $admissionData['admission']['ward_id'])
                    ->where('status', 'available')
                    ->where('is_active', true)
                    ->first();
            }

            if (!$bed) {
                return [
                    'success' => false,
                    'error' => 'No available beds in specified ward',
                ];
            }

            // Update bed status
            $bed->update([
                'status' => 'occupied',
                'patient_id' => $patient->id,
                'consultant_id' => $admissionData['admission']['consultant_id'],
                'nurse_id' => $admissionData['admission']['nurse_id'],
                'notes' => 'Auto-admitted via HL7 message',
            ]);

            // Create admission record
            $admission = \App\Models\PatientAdmission::create([
                'patient_id' => $patient->id,
                'ward_id' => $admissionData['admission']['ward_id'],
                'bed_id' => $bed->id,
                'bed_number' => $bed->bed_number,
                'admission_date' => $admissionData['admission']['admission_date'],
                'consultant_id' => $admissionData['admission']['consultant_id'],
                'nurse_id' => $admissionData['admission']['nurse_id'],
                'admitted_by' => 1, // System user
                'admission_notes' => 'Auto-admitted via HL7 integration',
                'is_active' => true,
                'diet_type' => $admissionData['admission']['diet_type'] ?? null,
                'patient_class' => $admissionData['admission']['patient_class'] ?? 'I',
                'fall_risk_alert' => $admissionData['admission']['fall_risk_alert'] ?? 'NO',
                'isolation_precautions' => $admissionData['admission']['isolation_precautions'] ?? 'NONE',
                'clinical_alerts' => $admissionData['admission']['clinical_alerts'] ?? null,
            ]);

            // Update patient admission status
            $patient->update([
                'is_admitted' => true,
                'admission_date' => $admissionData['admission']['admission_date'],
            ]);

            return [
                'success' => true,
                'admission_id' => $admission->id,
            ];

        } catch (Exception $e) {
            Log::error('Admission Processing Error', [
                'message_id' => $messageLog->message_id,
                'error' => $e->getMessage(),
                'admission_data' => $admissionData,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate unique message ID
     *
     * @return string
     */
    protected function generateMessageId(): string
    {
        return 'HL7-' . time() . '-' . uniqid();
    }

    /**
     * Get message status
     *
     * @param string $messageId
     * @return array|null
     */
    public function getMessageStatus(string $messageId): ?array
    {
        $messageLog = HL7MessageLog::where('message_id', $messageId)->first();
        
        if (!$messageLog) {
            return null;
        }

        return [
            'message_id' => $messageLog->message_id,
            'status' => $messageLog->status,
            'received_at' => $messageLog->received_at,
            'processed_at' => $messageLog->processed_at,
            'completed_at' => $messageLog->completed_at,
            'error_message' => $messageLog->error_message,
            'admission_id' => $messageLog->admission_id,
        ];
    }

    /**
     * Get all message logs with pagination
     *
     * @param int $perPage
     * @return mixed
     */
    public function getMessageHistory(int $perPage = 50)
    {
        return HL7MessageLog::with(['admission.patient', 'admission.ward', 'admission.bed'])
            ->orderBy('received_at', 'desc')
            ->paginate($perPage);
    }
} 