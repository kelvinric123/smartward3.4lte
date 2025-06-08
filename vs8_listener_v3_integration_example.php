<?php
/**
 * Example of how to integrate vs8_listener_v3.php with the new vital_sign_integration table
 * 
 * This is an example modification to show how you can store data in the new 
 * vital_sign_integration table instead of or in addition to vs8_continue_message
 */

set_time_limit(0);
error_reporting(E_ALL);

echo "Current directory: " . __DIR__ . "\n";
$configPath = str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/config.php');
echo "Looking for config at: " . $configPath . "\n";
require_once($configPath);

// Include Laravel's autoloader for using Eloquent models
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel (simplified version)
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\VitalSignIntegration;
use App\Models\Patient;

// Create TCP/IP socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    exit(1);
}

// Set socket options
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

// Bind the socket to the specific IP and port
$ip = '0.0.0.0';
$port = 2575;
if (!socket_bind($socket, $ip, $port)) {
    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($socket)) . "\n";
    exit(1);
}

// Start listening for connections
if (!socket_listen($socket, 5)) {
    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($socket)) . "\n";
    exit(1);
}

echo "Listening for continuous vital signs on $ip:$port...\n";

while (true) {
    $client = socket_accept($socket);
    
    if ($client) {
        $input = socket_read($client, 4096);
        
        if ($input) {
            $currentTime = date('Y-m-d H:i:s');
            
            // Parse HL7 message (same as original)
            $segments = explode("\r", $input);
            
            // Initialize variables
            $mrn = '';
            $name = '';
            $nurseId = '';
            $ewsTotal = null;
            $spo2 = $pulseRate = $respiratoryRate = $systolicBP = $diastolicBP = $avpu = null;
            
            foreach ($segments as $segment) {
                // Parse PID segment for patient info
                if (strpos($segment, 'PID|') === 0) {
                    $pidFields = explode('|', $segment);
                    if (isset($pidFields[3])) {
                        $mrn = explode('^', $pidFields[3])[0];
                    }
                    if (isset($pidFields[5])) {
                        $nameComponents = explode('^', $pidFields[5]);
                        $name = ($nameComponents[1] ?? '') . ' ' . ($nameComponents[0] ?? '');
                    }
                }
                
                // Parse OBX segments for vital signs and nurse ID
                if (strpos($segment, 'OBX|') === 0) {
                    $obxFields = explode('|', $segment);
                    
                    // Check for nurse ID in field 16
                    if (isset($obxFields[16]) && !empty($obxFields[16])) {
                        $nurseId = $obxFields[16];
                    }
                    
                    if (count($obxFields) >= 6) {
                        $observationId = explode('^', $obxFields[3])[1] ?? '';
                        $value = $obxFields[5] ?? '';
                        
                        switch ($observationId) {
                            case 'MDC_PULS_OXIM_SAT_O2':
                                $spo2 = $value;
                                break;
                            case 'MDC_PULS_OXIM_PULS_RATE':
                                $pulseRate = $value;
                                break;
                            case 'MDC_RESP_RATE':
                                $respiratoryRate = $value;
                                break;
                            case 'MDC_PRESS_BLD_NONINV_SYS':
                                $systolicBP = $value;
                                break;
                            case 'MDC_PRESS_BLD_NONINV_DIA':
                                $diastolicBP = $value;
                                break;
                            case 'MNDRY_EWS_LOC_AVPU':
                                $avpuCode = explode('^', $value)[1] ?? '';
                                switch ($avpuCode) {
                                    case 'MNDRY_SCORE_LOC_AVPU_ALERT':
                                        $avpu = 'alert';
                                        break;
                                    case 'MNDRY_SCORE_LOC_AVPU_VOICE':
                                        $avpu = 'reacting to voice';
                                        break;
                                    case 'MNDRY_SCORE_LOC_AVPU_PAIN':
                                        $avpu = 'reacting to pain';
                                        break;
                                    case 'MNDRY_SCORE_LOC_AVPU_UNRESPONSIVE':
                                        $avpu = 'unresponsive';
                                        break;
                                }
                                break;
                            case 'MNDRY_EWS_SCORE_TOTAL':
                                $ewsTotal = $value;
                                break;
                        }
                    }
                }
            }

            try {
                // Try to find existing patient by MRN
                $patient = Patient::where('mrn', $mrn)->first();
                
                // Create new vital sign integration record using Eloquent
                $vitalSignIntegration = VitalSignIntegration::create([
                    'device_timestamp' => $currentTime,
                    'raw_message' => $input,
                    'mrn' => $mrn,
                    'patient_name' => trim($name),
                    'respiratory_rate' => $respiratoryRate,
                    'spo2' => $spo2,
                    'pulse_rate' => $pulseRate,
                    'systolic_bp' => $systolicBP,
                    'diastolic_bp' => $diastolicBP,
                    'avpu' => $avpu,
                    'ews_score_total' => $ewsTotal,
                    'nurse_id' => $nurseId,
                    'patient_id' => $patient ? $patient->id : null,
                    'processed' => false, // Mark as unprocessed initially
                ]);

                error_log("Vital signs integration record created with ID: " . $vitalSignIntegration->id);
                error_log("Patient: $name (MRN: $mrn) - " . ($patient ? "Found in system" : "Not found in system"));
                
                // You could also add processing logic here to automatically
                // integrate the data into the main vital_signs table if needed
                
            } catch (Exception $e) {
                error_log("Error creating vital sign integration record: " . $e->getMessage());
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
        
        socket_close($client);
    }
    
    usleep(100000);
}
?> 