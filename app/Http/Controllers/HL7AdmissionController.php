<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HL7Integration\HL7ListenerService;
use App\Services\HL7Integration\HL7ParserService;
use App\Services\HL7Integration\HL7AdmissionMapperService;
use App\Models\HL7MessageLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class HL7AdmissionController extends Controller
{
    protected $listenerService;
    protected $parserService;
    protected $mapperService;

    public function __construct(
        HL7ListenerService $listenerService,
        HL7ParserService $parserService,
        HL7AdmissionMapperService $mapperService
    ) {
        $this->listenerService = $listenerService;
        $this->parserService = $parserService;
        $this->mapperService = $mapperService;
    }

    /**
     * Receive and process HL7 admission message
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function receiveMessage(Request $request): JsonResponse
    {
        try {
            // Get the raw message from request body
            $rawMessage = $request->getContent();
            
            if (empty($rawMessage)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Empty message body',
                    'message' => 'No HL7 message content received'
                ], 400);
            }

            // Validate HL7 message format
            if (!$this->parserService->validateMessage($rawMessage)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid HL7 message format',
                    'message' => 'Message does not meet HL7 format requirements'
                ], 400);
            }

            // Get request headers
            $headers = $request->headers->all();
            
            // Process the message
            $result = $this->listenerService->handleIncomingMessage($rawMessage, $headers);
            
            // Log the result
            Log::info('HL7 Message Processing Result', [
                'message_id' => $result['message_id'] ?? null,
                'success' => $result['success'],
                'admission_id' => $result['admission_id'] ?? null,
            ]);

            // Return appropriate response
            $statusCode = $result['success'] ? 200 : 422;
            
            return response()->json([
                'success' => $result['success'],
                'message_id' => $result['message_id'] ?? null,
                'admission_id' => $result['admission_id'] ?? null,
                'message' => $result['message'] ?? $result['error'] ?? 'Unknown error',
                'timestamp' => now()->toISOString(),
            ], $statusCode);

        } catch (Exception $e) {
            Log::error('HL7 Message Receive Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'Failed to process HL7 message',
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Get message status by message ID
     *
     * @param string $messageId
     * @return JsonResponse
     */
    public function getMessageStatus(string $messageId): JsonResponse
    {
        try {
            $status = $this->listenerService->getMessageStatus($messageId);
            
            if (!$status) {
                return response()->json([
                    'success' => false,
                    'error' => 'Message not found',
                    'message' => 'No message found with the provided ID'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (Exception $e) {
            Log::error('Get Message Status Error', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'Failed to retrieve message status'
            ], 500);
        }
    }

    /**
     * Display HL7 message history and status page
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function messageHistory(Request $request)
    {
        try {
            // Get filter parameters
            $filters = $request->only(['status', 'message_type', 'patient_mrn', 'date_from', 'date_to']);
            
            // Build query with filters
            $query = HL7MessageLog::query()
                ->with(['admission.patient', 'admission.ward', 'admission.bed'])
                ->orderBy('received_at', 'desc');

            // Apply filters
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['message_type'])) {
                $query->where('message_type', $filters['message_type']);
            }

            if (!empty($filters['patient_mrn'])) {
                $query->where('patient_mrn', 'like', '%' . $filters['patient_mrn'] . '%');
            }

            if (!empty($filters['date_from'])) {
                $query->where('received_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->where('received_at', '<=', $filters['date_to'] . ' 23:59:59');
            }

            // Paginate results
            $messages = $query->paginate(50);

            // Get statistics
            $statistics = HL7MessageLog::getStatistics();
            
            // Get recent errors
            $recentErrors = HL7MessageLog::getRecentErrors(5);

            // Get performance metrics
            $performanceMetrics = HL7MessageLog::getPerformanceMetrics();

            return view('hl7.message-history', compact(
                'messages',
                'statistics',
                'recentErrors',
                'performanceMetrics',
                'filters'
            ));

        } catch (Exception $e) {
            Log::error('Message History View Error', [
                'error' => $e->getMessage(),
                'filters' => $filters ?? [],
            ]);

            // Create empty paginator for consistent interface
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                50,
                1,
                ['path' => request()->url(), 'pageName' => 'page']
            );

            return view('hl7.message-history', [
                'messages' => $emptyPaginator,
                'statistics' => [
                    'total_messages' => 0,
                    'successful_messages' => 0,
                    'failed_messages' => 0,
                    'pending_messages' => 0
                ],
                'recentErrors' => collect(),
                'performanceMetrics' => [
                    'average_processing_time' => 0,
                    'total_processed' => 0
                ],
                'filters' => $filters ?? [],
                'error' => 'Failed to load message history'
            ]);
        }
    }

    /**
     * Display individual message details
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function messageDetails(int $id)
    {
        try {
            $message = HL7MessageLog::with(['admission.patient', 'admission.ward', 'admission.bed'])
                ->findOrFail($id);

            // Parse the message for display if not already parsed
            $parsedSummary = null;
            if ($message->parsed_data) {
                $parsedSummary = $this->parserService->getMessageSummary($message->parsed_data);
            }

            // Get mapping summary if available
            $mappingSummary = null;
            if ($message->parsed_data && $message->mapped_data) {
                $mappingSummary = $this->mapperService->getMappingSummary(
                    $message->parsed_data,
                    $message->mapped_data
                );
            }

            // Process segments for better display
            $processedSegments = [];
            if ($message->parsed_data && isset($message->parsed_data['segments'])) {
                foreach ($message->parsed_data['segments'] as $index => $segment) {
                    $processedSegments[] = [
                        'type' => $segment['type'],
                        'description' => $this->getSegmentDescription($segment['type']),
                        'fields' => $segment['fields'],
                        'field_count' => count($segment['fields']),
                        'icon' => $this->getSegmentIcon($segment['type']),
                        'processed_fields' => $this->processSegmentFields($segment['type'], $segment['fields'])
                    ];
                }
            }

            return view('hl7.message-details', compact(
                'message',
                'parsedSummary',
                'mappingSummary',
                'processedSegments'
            ));

        } catch (Exception $e) {
            Log::error('Message Details View Error', [
                'message_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('hl7.message-history')
                ->with('error', 'Failed to load message details');
        }
    }

    /**
     * Get segment description
     *
     * @param string $type
     * @return string
     */
    private function getSegmentDescription(string $type): string
    {
        $descriptions = [
            'MSH' => 'Message Header',
            'PID' => 'Patient Identification',
            'PV1' => 'Patient Visit',
            'EVN' => 'Event Type',
            'AL1' => 'Allergy Information',
            'OBX' => 'Observation/Result',
            'DG1' => 'Diagnosis',
            'PR1' => 'Procedures',
            'NK1' => 'Next of Kin',
            'PV2' => 'Patient Visit - Additional Info',
            'OBR' => 'Observation Request',
            'NTE' => 'Notes and Comments',
            'MRG' => 'Merge Patient Information',
            'ROL' => 'Role'
        ];
        return $descriptions[$type] ?? 'Unknown Segment';
    }

    /**
     * Get segment icon
     *
     * @param string $type
     * @return string
     */
    private function getSegmentIcon(string $type): string
    {
        $icons = [
            'MSH' => 'envelope',
            'PID' => 'user',
            'PV1' => 'bed',
            'EVN' => 'clock',
            'AL1' => 'exclamation-triangle',
            'OBX' => 'chart-line',
            'DG1' => 'stethoscope',
            'PR1' => 'procedures',
            'NK1' => 'users',
            'PV2' => 'bed',
            'OBR' => 'clipboard-list',
            'NTE' => 'sticky-note',
            'MRG' => 'code-branch',
            'ROL' => 'user-tag'
        ];
        return $icons[$type] ?? 'question';
    }

    /**
     * Process segment fields with descriptions
     *
     * @param string $segmentType
     * @param array $fields
     * @return array
     */
    private function processSegmentFields(string $segmentType, array $fields): array
    {
        $descriptions = [
            'MSH' => [
                1 => 'Field Separator',
                2 => 'Encoding Characters',
                3 => 'Sending Application',
                4 => 'Sending Facility',
                5 => 'Receiving Application',
                6 => 'Receiving Facility',
                7 => 'Date/Time of Message',
                8 => 'Security',
                9 => 'Message Type',
                10 => 'Message Control ID',
                11 => 'Processing ID',
                12 => 'Version ID'
            ],
            'PID' => [
                1 => 'Set ID',
                2 => 'Patient ID',
                3 => 'Patient Identifier List',
                4 => 'Alternate Patient ID',
                5 => 'Patient Name',
                6 => 'Mother\'s Maiden Name',
                7 => 'Date/Time of Birth',
                8 => 'Administrative Sex',
                9 => 'Patient Alias',
                10 => 'Race',
                11 => 'Patient Address',
                12 => 'County Code',
                13 => 'Phone Number - Home',
                14 => 'Phone Number - Business',
                15 => 'Primary Language',
                16 => 'Marital Status',
                17 => 'Religion'
            ],
            'PV1' => [
                1 => 'Set ID',
                2 => 'Patient Class',
                3 => 'Assigned Patient Location',
                4 => 'Admission Type',
                5 => 'Preadmit Number',
                6 => 'Prior Patient Location',
                7 => 'Attending Doctor',
                8 => 'Referring Doctor',
                9 => 'Consulting Doctor',
                10 => 'Hospital Service',
                11 => 'Temporary Location',
                12 => 'Preadmit Test Indicator',
                13 => 'Re-admission Indicator',
                14 => 'Admit Source',
                15 => 'Ambulatory Status',
                16 => 'VIP Indicator',
                17 => 'Admitting Doctor',
                18 => 'Patient Type',
                19 => 'Visit Number',
                20 => 'Financial Class'
            ],
            'EVN' => [
                1 => 'Event Type Code',
                2 => 'Recorded Date/Time',
                3 => 'Date/Time Planned Event',
                4 => 'Event Reason Code',
                5 => 'Operator ID',
                6 => 'Event Occurred',
                7 => 'Event Facility'
            ],
            'AL1' => [
                1 => 'Set ID',
                2 => 'Allergen Type Code',
                3 => 'Allergen Code/Mnemonic/Description',
                4 => 'Allergy Severity Code',
                5 => 'Allergy Reaction Code',
                6 => 'Identification Date'
            ]
        ];

        $processedFields = [];
        foreach ($fields as $index => $field) {
            $fieldNumber = $index + 1;
            $processedFields[] = [
                'number' => $fieldNumber,
                'label' => $segmentType . '.' . $fieldNumber,
                'description' => $descriptions[$segmentType][$fieldNumber] ?? "Field $fieldNumber",
                'value' => $field,
                'is_empty' => empty($field)
            ];
        }

        return $processedFields;
    }

    /**
     * Get dashboard data for HL7 integration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDashboardData(Request $request): JsonResponse
    {
        try {
            $days = $request->input('days', 30);
            
            $data = [
                'statistics' => HL7MessageLog::getStatistics($days),
                'recent_errors' => HL7MessageLog::getRecentErrors(10),
                'performance_metrics' => HL7MessageLog::getPerformanceMetrics($days),
                'admission_success_rate' => HL7MessageLog::getAdmissionSuccessRate($days),
                'recent_messages' => HL7MessageLog::with(['admission.patient'])
                    ->orderBy('received_at', 'desc')
                    ->limit(10)
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (Exception $e) {
            Log::error('Dashboard Data Error', [
                'error' => $e->getMessage(),
                'days' => $days ?? 30,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load dashboard data'
            ], 500);
        }
    }

    /**
     * Retry failed message processing
     *
     * @param int $id
     * @return JsonResponse
     */
    public function retryMessage(int $id): JsonResponse
    {
        try {
            $message = HL7MessageLog::findOrFail($id);
            
            if ($message->status !== 'failed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only failed messages can be retried'
                ], 400);
            }

            // Reset message status
            $message->update([
                'status' => 'received',
                'error_message' => null,
                'processed_at' => null,
                'completed_at' => null,
                'processing_time_ms' => null,
            ]);

            // Process the message again
            $result = $this->listenerService->handleIncomingMessage(
                $message->raw_message,
                $message->headers ?? []
            );

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Message retried successfully' : 'Retry failed',
                'error' => $result['error'] ?? null,
            ]);

        } catch (Exception $e) {
            Log::error('Retry Message Error', [
                'message_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retry message'
            ], 500);
        }
    }

    /**
     * Test HL7 message processing with sample data
     *
     * @return JsonResponse
     */
    public function testMessage(): JsonResponse
    {
        try {
            // Sample HL7 ADT^A01 message
            $sampleMessage = "MSH|^~\&|SENDING_APP|SENDING_FACILITY|RECEIVING_APP|RECEIVING_FACILITY|" . 
                date('YmdHis') . "||ADT^A01|CTRL" . time() . "|P|2.5\r" .
                "EVN|A01|" . date('YmdHis') . "|||DOC1^SMITH^JOHN^||" . date('YmdHis') . "\r" .
                "PID|1||MRN" . rand(1000, 9999) . "^^^MR^MR||DOE^JOHN^MIDDLE||19800101|M|||123 MAIN ST^^CITY^STATE^12345||555-1234||||\r" .
                "PV1|1|I|WARD1^BED1^WARD1^FACILITY|||DOC1^SMITH^JOHN^|MED||||||||||||||||||||||||||||||||||||" . date('YmdHis') . "\r" .
                "AL1|1||DRUG^PENICILLIN|MO|RASH";

            // Process the sample message
            $result = $this->listenerService->handleIncomingMessage($sampleMessage, [
                'content-type' => 'application/hl7-v2',
                'user-agent' => 'HL7-Test-Client',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test message processed',
                'result' => $result,
                'sample_message' => $sampleMessage,
            ]);

        } catch (Exception $e) {
            Log::error('Test Message Error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to process test message',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clean up old message logs
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function cleanupLogs(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'days' => 'required|integer|min:30|max:365',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $days = $request->input('days');
            $deletedCount = HL7MessageLog::cleanupOldLogs($days);

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} old message logs",
                'deleted_count' => $deletedCount,
            ]);

        } catch (Exception $e) {
            Log::error('Cleanup Logs Error', [
                'error' => $e->getMessage(),
                'days' => $request->input('days'),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to cleanup old logs',
            ], 500);
        }
    }

    /**
     * Process manual test HL7 message
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function manualTest(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'message' => 'required|string|min:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid input data',
                    'details' => $validator->errors()
                ], 400);
            }

            $rawMessage = $request->input('message');

            // Validate HL7 message format
            if (!$this->parserService->validateMessage($rawMessage)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid HL7 message format',
                    'message' => 'Message does not meet HL7 format requirements'
                ], 400);
            }

            // Get request headers
            $headers = $request->headers->all();
            
            // Process the message
            $result = $this->listenerService->handleIncomingMessage($rawMessage, $headers);
            
            // Log the result
            Log::info('Manual HL7 Test Result', [
                'message_id' => $result['message_id'] ?? null,
                'success' => $result['success'],
                'admission_id' => $result['admission_id'] ?? null,
                'user_initiated' => true,
            ]);

            // Return appropriate response
            $statusCode = $result['success'] ? 200 : 422;
            
            return response()->json([
                'success' => $result['success'],
                'message_id' => $result['message_id'] ?? null,
                'admission_id' => $result['admission_id'] ?? null,
                'message' => $result['message'] ?? $result['error'] ?? 'Unknown error',
                'timestamp' => now()->toISOString(),
            ], $statusCode);

        } catch (Exception $e) {
            Log::error('Manual HL7 Test Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'Failed to process manual test message',
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Get mapping configuration options
     *
     * @return JsonResponse
     */
    public function getMappingOptions(): JsonResponse
    {
        try {
            $options = $this->mapperService->getMappingOptions();

            return response()->json([
                'success' => true,
                'data' => $options,
            ]);

        } catch (Exception $e) {
            Log::error('Get Mapping Options Error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load mapping options',
            ], 500);
        }
    }
} 