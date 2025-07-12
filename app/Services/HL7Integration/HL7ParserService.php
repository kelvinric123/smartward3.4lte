<?php

namespace App\Services\HL7Integration;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HL7ParserService
{
    protected $fieldSeparator = '|';
    protected $componentSeparator = '^';
    protected $repetitionSeparator = '~';
    protected $escapeCharacter = '\\';
    protected $subComponentSeparator = '&';

    /**
     * Parse HL7 message into structured array
     *
     * @param string $rawMessage
     * @return array|null
     */
    public function parse(string $rawMessage): ?array
    {
        try {
            // Clean the message - remove extra whitespace and carriage returns
            $message = trim($rawMessage);
            $message = str_replace(["\r\n", "\r", "\n"], "\r", $message);
            
            // Split into segments
            $segments = explode("\r", $message);
            
            if (empty($segments)) {
                throw new Exception('Empty HL7 message');
            }

            $parsedData = [
                'message_type' => '',
                'message_control_id' => '',
                'patient' => [],
                'admission' => [],
                'segments' => []
            ];

            foreach ($segments as $segment) {
                if (empty(trim($segment))) {
                    continue;
                }

                $segmentData = $this->parseSegment($segment);
                
                if (!$segmentData) {
                    continue;
                }

                $parsedData['segments'][] = $segmentData;

                // Process specific segment types
                switch ($segmentData['type']) {
                    case 'MSH':
                        $parsedData = $this->processMSHSegment($segmentData, $parsedData);
                        break;
                    case 'PID':
                        $parsedData = $this->processPIDSegment($segmentData, $parsedData);
                        break;
                    case 'PV1':
                        $parsedData = $this->processPV1Segment($segmentData, $parsedData);
                        break;
                    case 'EVN':
                        $parsedData = $this->processEVNSegment($segmentData, $parsedData);
                        break;
                    case 'AL1':
                        $parsedData = $this->processAL1Segment($segmentData, $parsedData);
                        break;
                }
            }

            return $parsedData;

        } catch (Exception $e) {
            Log::error('HL7 Parsing Error', [
                'error' => $e->getMessage(),
                'message' => $rawMessage,
            ]);

            return null;
        }
    }

    /**
     * Parse individual segment
     *
     * @param string $segment
     * @return array|null
     */
    protected function parseSegment(string $segment): ?array
    {
        if (empty($segment)) {
            return null;
        }

        $fields = explode($this->fieldSeparator, $segment);
        
        if (empty($fields)) {
            return null;
        }

        $segmentType = $fields[0];
        
        // Remove segment type from fields
        array_shift($fields);

        return [
            'type' => $segmentType,
            'fields' => $fields,
        ];
    }

    /**
     * Process MSH (Message Header) segment
     *
     * @param array $segmentData
     * @param array $parsedData
     * @return array
     */
    protected function processMSHSegment(array $segmentData, array $parsedData): array
    {
        $fields = $segmentData['fields'];
        
        // MSH segment structure:
        // MSH|^~\&|SendingApp|SendingFacility|ReceivingApp|ReceivingFacility|TimeStamp|Security|MessageType|MessageControlID|ProcessingID|VersionID
        
        if (isset($fields[7])) { // MessageType (MSH.9)
            $messageType = $this->parseComponents($fields[7]);
            $parsedData['message_type'] = $messageType[0] ?? '';
        }

        if (isset($fields[8])) { // MessageControlID (MSH.10)
            $parsedData['message_control_id'] = $fields[8];
        }

        return $parsedData;
    }

    /**
     * Process PID (Patient Identification) segment
     *
     * @param array $segmentData
     * @param array $parsedData
     * @return array
     */
    protected function processPIDSegment(array $segmentData, array $parsedData): array
    {
        $fields = $segmentData['fields'];
        
        // PID segment structure:
        // PID|1|PatientID|MRN|AlternatePatientID|PatientName|MotherMaidenName|DateOfBirth|Sex|...
        
        if (isset($fields[1])) { // PatientID (PID.2)
            $parsedData['patient']['patient_id'] = $fields[1];
        }

        if (isset($fields[2])) { // MRN (PID.3)
            $mrn = $this->parseComponents($fields[2]);
            $parsedData['patient']['mrn'] = $mrn[0] ?? '';
        }

        if (isset($fields[4])) { // PatientName (PID.5)
            $name = $this->parseComponents($fields[4]);
            $parsedData['patient']['last_name'] = $name[0] ?? '';
            $parsedData['patient']['first_name'] = $name[1] ?? '';
            $parsedData['patient']['middle_name'] = $name[2] ?? '';
            $parsedData['patient']['full_name'] = trim(($name[1] ?? '') . ' ' . ($name[2] ?? '') . ' ' . ($name[0] ?? ''));
        }

        if (isset($fields[6])) { // DateOfBirth (PID.7)
            $dob = $this->parseHL7Date($fields[6]);
            if ($dob) {
                $parsedData['patient']['date_of_birth'] = $dob->format('Y-m-d');
            }
        }

        if (isset($fields[7])) { // Sex (PID.8)
            $parsedData['patient']['gender'] = $fields[7];
        }

        if (isset($fields[10])) { // Race (PID.11)
            $parsedData['patient']['race'] = $fields[10];
        }

        if (isset($fields[11])) { // Address (PID.12)
            $address = $this->parseComponents($fields[11]);
            $parsedData['patient']['address'] = $address[0] ?? '';
            $parsedData['patient']['city'] = $address[2] ?? '';
            $parsedData['patient']['state'] = $address[3] ?? '';
            $parsedData['patient']['postal_code'] = $address[4] ?? '';
        }

        if (isset($fields[12])) { // Phone (PID.13)
            $parsedData['patient']['phone'] = $fields[12];
        }

        return $parsedData;
    }

    /**
     * Process PV1 (Patient Visit) segment
     *
     * @param array $segmentData
     * @param array $parsedData
     * @return array
     */
    protected function processPV1Segment(array $segmentData, array $parsedData): array
    {
        $fields = $segmentData['fields'];
        
        // PV1 segment structure:
        // PV1|1|PatientClass|AssignedPatientLocation|AdmissionType|PreadmitNumber|PriorPatientLocation|AttendingDoctor|...
        
        if (isset($fields[1])) { // PatientClass (PV1.2)
            $parsedData['admission']['patient_class'] = $fields[1];
        }

        if (isset($fields[2])) { // AssignedPatientLocation (PV1.3)
            $location = $this->parseComponents($fields[2]);
            $parsedData['admission']['room'] = $location[0] ?? '';
            $parsedData['admission']['bed'] = $location[1] ?? '';
            $parsedData['admission']['ward'] = $location[2] ?? '';
            $parsedData['admission']['facility'] = $location[3] ?? '';
        }

        if (isset($fields[3])) { // AdmissionType (PV1.4)
            $parsedData['admission']['admission_type'] = $fields[3];
        }

        if (isset($fields[6])) { // AttendingDoctor (PV1.7)
            $doctor = $this->parseComponents($fields[6]);
            $parsedData['admission']['attending_doctor_id'] = $doctor[0] ?? '';
            $parsedData['admission']['attending_doctor_name'] = ($doctor[1] ?? '') . ' ' . ($doctor[2] ?? '');
        }

        if (isset($fields[43])) { // AdmitDateTime (PV1.44)
            $admitDate = $this->parseHL7DateTime($fields[43]);
            if ($admitDate) {
                $parsedData['admission']['admission_date'] = $admitDate->format('Y-m-d H:i:s');
            }
        }

        if (isset($fields[17])) { // AdmittingDoctor (PV1.18)
            $admittingDoctor = $this->parseComponents($fields[17]);
            $parsedData['admission']['admitting_doctor_id'] = $admittingDoctor[0] ?? '';
            $parsedData['admission']['admitting_doctor_name'] = ($admittingDoctor[1] ?? '') . ' ' . ($admittingDoctor[2] ?? '');
        }

        return $parsedData;
    }

    /**
     * Process EVN (Event Type) segment
     *
     * @param array $segmentData
     * @param array $parsedData
     * @return array
     */
    protected function processEVNSegment(array $segmentData, array $parsedData): array
    {
        $fields = $segmentData['fields'];
        
        if (isset($fields[1])) { // RecordedDateTime (EVN.2)
            $recordedDate = $this->parseHL7DateTime($fields[1]);
            if ($recordedDate) {
                $parsedData['admission']['recorded_date'] = $recordedDate->format('Y-m-d H:i:s');
            }
        }

        if (isset($fields[4])) { // OperatorID (EVN.5)
            $operator = $this->parseComponents($fields[4]);
            $parsedData['admission']['operator_id'] = $operator[0] ?? '';
            $parsedData['admission']['operator_name'] = ($operator[1] ?? '') . ' ' . ($operator[2] ?? '');
        }

        return $parsedData;
    }

    /**
     * Process AL1 (Allergy) segment
     *
     * @param array $segmentData
     * @param array $parsedData
     * @return array
     */
    protected function processAL1Segment(array $segmentData, array $parsedData): array
    {
        $fields = $segmentData['fields'];
        
        if (!isset($parsedData['patient']['allergies'])) {
            $parsedData['patient']['allergies'] = [];
        }

        $allergy = [];
        
        if (isset($fields[2])) { // AllergenType (AL1.3)
            $allergy['type'] = $fields[2];
        }

        if (isset($fields[3])) { // AllergenCode (AL1.4)
            $allergen = $this->parseComponents($fields[3]);
            $allergy['allergen'] = $allergen[0] ?? '';
            $allergy['description'] = $allergen[1] ?? '';
        }

        if (isset($fields[4])) { // Severity (AL1.5)
            $allergy['severity'] = $fields[4];
        }

        if (isset($fields[5])) { // Reaction (AL1.6)
            $allergy['reaction'] = $fields[5];
        }

        $parsedData['patient']['allergies'][] = $allergy;

        return $parsedData;
    }

    /**
     * Parse components (separated by ^)
     *
     * @param string $field
     * @return array
     */
    protected function parseComponents(string $field): array
    {
        return explode($this->componentSeparator, $field);
    }

    /**
     * Parse HL7 date format (YYYYMMDD)
     *
     * @param string $date
     * @return Carbon|null
     */
    protected function parseHL7Date(string $date): ?Carbon
    {
        try {
            if (strlen($date) >= 8) {
                return Carbon::createFromFormat('Ymd', substr($date, 0, 8));
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Parse HL7 datetime format (YYYYMMDDHHMMSS)
     *
     * @param string $datetime
     * @return Carbon|null
     */
    protected function parseHL7DateTime(string $datetime): ?Carbon
    {
        try {
            if (strlen($datetime) >= 14) {
                return Carbon::createFromFormat('YmdHis', substr($datetime, 0, 14));
            } elseif (strlen($datetime) >= 8) {
                return Carbon::createFromFormat('Ymd', substr($datetime, 0, 8));
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Validate HL7 message format
     *
     * @param string $message
     * @return bool
     */
    public function validateMessage(string $message): bool
    {
        // Check for MSH segment
        if (!str_starts_with($message, 'MSH')) {
            return false;
        }

        // Check for required segments
        $requiredSegments = ['MSH', 'PID'];
        foreach ($requiredSegments as $segment) {
            if (!str_contains($message, $segment)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get parsed message summary
     *
     * @param array $parsedData
     * @return array
     */
    public function getMessageSummary(array $parsedData): array
    {
        return [
            'message_type' => $parsedData['message_type'] ?? 'Unknown',
            'message_control_id' => $parsedData['message_control_id'] ?? 'Unknown',
            'patient_name' => $parsedData['patient']['full_name'] ?? 'Unknown',
            'patient_mrn' => $parsedData['patient']['mrn'] ?? 'Unknown',
            'admission_date' => $parsedData['admission']['admission_date'] ?? 'Unknown',
            'ward' => $parsedData['admission']['ward'] ?? 'Unknown',
            'bed' => $parsedData['admission']['bed'] ?? 'Unknown',
            'segments_count' => count($parsedData['segments'] ?? []),
        ];
    }
} 