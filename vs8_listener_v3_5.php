<?php
set_time_limit(0);
error_reporting(E_ALL);

echo "Current directory: " . __DIR__ . "\n";

// Load Laravel's environment and database configuration
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get database configuration from Laravel's .env
$host = env('DB_HOST', 'localhost');
$dbname = env('DB_DATABASE', 'smartward34lte');
$username = env('DB_USERNAME', 'root');
$password = env('DB_PASSWORD', '');

echo "Connecting to database: $dbname on $host\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    echo "Database connection successful!\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Function to calculate EWS scores (based on VitalSign model logic)
function calculateEWS($vitals) {
    $scores = [
        'temperature_score' => 0,
        'heart_rate_score' => 0,
        'respiratory_rate_score' => 0,
        'blood_pressure_score' => 0,
        'oxygen_saturation_score' => 0,
        'consciousness_score' => 0,
    ];

    // Temperature score
    if (isset($vitals['temperature']) && $vitals['temperature'] !== null) {
        $temp = (float)$vitals['temperature'];
        if ($temp <= 35.0) $scores['temperature_score'] = 3;
        elseif ($temp <= 35.9) $scores['temperature_score'] = 1;
        elseif ($temp <= 38.4) $scores['temperature_score'] = 0;
        elseif ($temp <= 39.0) $scores['temperature_score'] = 1;
        else $scores['temperature_score'] = 2;
    }

    // Heart rate score (using pulse_rate as heart_rate)
    if (isset($vitals['heart_rate']) && $vitals['heart_rate'] !== null) {
        $hr = (int)$vitals['heart_rate'];
        if ($hr <= 40) $scores['heart_rate_score'] = 3;
        elseif ($hr <= 50) $scores['heart_rate_score'] = 1;
        elseif ($hr <= 90) $scores['heart_rate_score'] = 0;
        elseif ($hr <= 110) $scores['heart_rate_score'] = 1;
        elseif ($hr <= 130) $scores['heart_rate_score'] = 2;
        else $scores['heart_rate_score'] = 3;
    }

    // Respiratory rate score
    if (isset($vitals['respiratory_rate']) && $vitals['respiratory_rate'] !== null) {
        $rr = (int)$vitals['respiratory_rate'];
        if ($rr <= 8) $scores['respiratory_rate_score'] = 3;
        elseif ($rr <= 11) $scores['respiratory_rate_score'] = 1;
        elseif ($rr <= 20) $scores['respiratory_rate_score'] = 0;
        elseif ($rr <= 24) $scores['respiratory_rate_score'] = 2;
        else $scores['respiratory_rate_score'] = 3;
    }

    // Blood pressure score (using systolic_bp)
    if (isset($vitals['systolic_bp']) && $vitals['systolic_bp'] !== null) {
        $sys = (int)$vitals['systolic_bp'];
        if ($sys <= 90) $scores['blood_pressure_score'] = 3;
        elseif ($sys <= 100) $scores['blood_pressure_score'] = 2;
        elseif ($sys <= 110) $scores['blood_pressure_score'] = 1;
        elseif ($sys <= 219) $scores['blood_pressure_score'] = 0;
        else $scores['blood_pressure_score'] = 2;
    }

    // Oxygen saturation score (using spo2 as oxygen_saturation)
    if (isset($vitals['oxygen_saturation']) && $vitals['oxygen_saturation'] !== null) {
        $spo2 = (float)$vitals['oxygen_saturation'];
        if ($spo2 <= 91) $scores['oxygen_saturation_score'] = 3;
        elseif ($spo2 <= 93) $scores['oxygen_saturation_score'] = 2;
        elseif ($spo2 <= 95) $scores['oxygen_saturation_score'] = 1;
        else $scores['oxygen_saturation_score'] = 0;
    }

    // Consciousness score (AVPU)
    if (isset($vitals['consciousness']) && $vitals['consciousness'] !== null) {
        $consciousness = strtolower($vitals['consciousness']);
        if (in_array($consciousness, ['alert', 'a'])) {
            $scores['consciousness_score'] = 0;
        } else {
            $scores['consciousness_score'] = 3; // V, P, U all score 3
        }
    }

    $totalEws = array_sum($scores);

    return [
        'scores' => $scores,
        'total_ews' => $totalEws
    ];
}

// Function to find patient by MRN
function findPatientByMrn($pdo, $mrn) {
    if (empty($mrn)) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, name, mrn FROM patients WHERE mrn = :mrn LIMIT 1");
        $stmt->execute([':mrn' => $mrn]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error finding patient by MRN: " . $e->getMessage());
        return null;
    }
}

// Function to find nurse/user by nurse_id
function findUserByNurseId($pdo, $nurseId) {
    if (empty($nurseId)) {
        return null;
    }
    
    try {
        // Try to find user by ID first, then by name/email containing the nurse ID
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = :nurse_id OR name LIKE :nurse_name OR email LIKE :nurse_email LIMIT 1");
        $stmt->execute([
            ':nurse_id' => $nurseId,
            ':nurse_name' => "%$nurseId%",
            ':nurse_email' => "%$nurseId%"
        ]);
        $user = $stmt->fetch();
        return $user ? $user['id'] : null;
    } catch (PDOException $e) {
        error_log("Error finding user by nurse ID: " . $e->getMessage());
        return null;
    }
}

// Create TCP/IP socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    exit(1);
}

// Set socket options
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

// Bind the socket to the specific IP and port
$ip = '0.0.0.0';  // Listen on all available network interfaces
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

// Initialize all variables before the loop
$spo2 = $pulseRate = $respiratoryRate = $temperature = $glucose = $painScore = $systolicBP = $diastolicBP = $meanBP = $avpu = null;
$bodyPosition = $height = $weight = null;
$recordedBy = '';
$mrn = '';
$name = '';

while (true) {
    $client = socket_accept($socket);
    
    if ($client) {
        $input = socket_read($client, 4096);
        
        if ($input) {
            $currentTime = date('Y-m-d H:i:s');
            
            // Parse HL7 message
            $segments = explode("\r", $input);
            
            // Initialize variables
            $mrn = '';
            $name = '';
            $vitalSigns = [];
            $nurseId = '';
            $ewsTotal = null;
            $temperature = $meanBP = $bodyPosition = $height = $weight = null;
            
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
                    
                    // Check for nurse ID in field 16 or at the end of the segment
                    if (isset($obxFields[16]) && !empty($obxFields[16])) {
                        $nurseId = $obxFields[16];
                        error_log("Found Nurse ID in OBX field 16: $nurseId");
                    }
                    // Also check for nurse ID at the end (after the last ||)
                    if (count($obxFields) > 16 && isset($obxFields[count($obxFields)-1]) && !empty($obxFields[count($obxFields)-1])) {
                        $nurseId = $obxFields[count($obxFields)-1];
                        error_log("Found Nurse ID at end of OBX segment: $nurseId");
                    }
                    
                    error_log("OBX segment fields count: " . count($obxFields));
                    error_log("OBX segment: " . $segment);
                    
                    if (count($obxFields) >= 6) {
                        $observationId = explode('^', $obxFields[3])[1] ?? '';
                        $value = $obxFields[5] ?? '';
                        
                        // Debug log to see what we're parsing
                        if (strpos($observationId, 'PULS_RATE') !== false) {
                            error_log("Found PULSE RATE observation: $observationId with value: $value");
                        }
                        
                        switch ($observationId) {
                            case 'MDC_PULS_OXIM_SAT_O2':  // SpO2
                                $spo2 = $value;
                                break;
                                
                            case 'MDC_PULS_OXIM_PULS_RATE':  // Pulse Rate (from pulse oximeter)
                                $pulseRate = $value;
                                error_log("Captured Pulse Rate from OXIM: $value bpm");
                                break;
                                
                            case 'MDC_PULS_RATE':  // Pulse Rate (direct measurement)
                                $pulseRate = $value;
                                error_log("Captured Pulse Rate from PULS: $value bpm");
                                break;
                                
                            case 'MDC_RESP_RATE':  // Direct RR measurement - this is the correct one
                                $respiratoryRate = $value;
                                error_log("Respiratory Rate received: $value");
                                break;

                            case 'MDC_PRESS_BLD_NONINV_SYS':  // Systolic Blood Pressure
                                $systolicBP = $value;
                                break;

                            case 'MDC_PRESS_BLD_NONINV_DIA':  // Diastolic Blood Pressure
                                $diastolicBP = $value;
                                break;

                            case 'MDC_PRESS_BLD_NONINV_MEAN':  // Mean Blood Pressure
                                $meanBP = $value;
                                break;

                            case 'MDC_TEMP':  // Temperature
                                $temperature = $value;
                                break;

                            case 'MDC_LEN_BODY_ACTUAL':  // Height
                                $height = $value;
                                break;

                            case 'MDC_MASS_BODY_ACTUAL':  // Weight
                                $weight = $value;
                                break;

                            case 'COMEN_POS_BODY':  // Body Position
                                // Extract position from coded value (e.g., "2821^COMEN_POS_SITTING^99COMEN")
                                $positionCode = explode('^', $value)[1] ?? '';
                                switch ($positionCode) {
                                    case 'COMEN_POS_SITTING':
                                        $bodyPosition = 'sitting';
                                        break;
                                    case 'COMEN_POS_STANDING':
                                        $bodyPosition = 'standing';
                                        break;
                                    case 'COMEN_POS_LYING':
                                        $bodyPosition = 'lying';
                                        break;
                                    case 'COMEN_POS_SUPINE':
                                        $bodyPosition = 'supine';
                                        break;
                                    default:
                                        $bodyPosition = $positionCode;
                                }
                                break;

                            case 'MNDRY_EWS_LOC_AVPU':  // AVPU Score from OBX|19
                                error_log("Processing AVPU - Raw value: " . print_r($value, true));
                                
                                // Extract the AVPU code from the value (format: 60036^MNDRY_SCORE_LOC_AVPU_VOICE^99MNDRY)
                                $avpuCode = explode('^', $value)[1] ?? '';
                                
                                // Convert Mindray AVPU codes to standardized values
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
                                    default:
                                        $avpu = null;
                                        error_log("AVPU value not recognized: $avpuCode");
                                }
                                error_log("Final AVPU value to be stored: " . ($avpu ?? 'NULL'));
                                break;

                            case 'MNDRY_EWS_SCORE_TOTAL':  // EWS Total Score from OBX|10
                                $ewsTotal = $value;
                                error_log("EWS Total Score: $ewsTotal");
                                break;
                        }
                    }
                }
                
                // Remove OBR nurse ID parsing since we're now getting it from OBX
                if (strpos($segment, 'OBR|') === 0) {
                    // Keep other OBR parsing if needed
                }
            }

            // Add nurse ID to debug logging
            error_log("Parsed vital signs:");
            error_log("Nurse ID: $nurseId");
            error_log("Temperature: {$temperature}°C");
            error_log("SpO2: {$spo2}%, Pulse: {$pulseRate} bpm, RR: {$respiratoryRate}/min");
            error_log("BP: {$systolicBP}/{$diastolicBP} (Mean: {$meanBP}) mmHg");
            error_log("AVPU: {$avpu}, Position: {$bodyPosition}");
            error_log("Height: {$height}cm, Weight: {$weight}kg");
            error_log("EWS Total: {$ewsTotal}");

            error_log("Patient info: MRN: $mrn, Name: $name");

            // Convert empty strings to NULL for decimal fields to prevent database errors
            $temperature = ($temperature === '' || $temperature === null) ? null : (float)$temperature;
            $height = ($height === '' || $height === null) ? null : (float)$height;
            $weight = ($weight === '' || $weight === null) ? null : (float)$weight;
            $meanBP = ($meanBP === '' || $meanBP === null) ? null : (int)$meanBP;
            $spo2 = ($spo2 === '' || $spo2 === null) ? null : (float)$spo2;
            $pulseRate = ($pulseRate === '' || $pulseRate === null) ? null : (int)$pulseRate;
            $respiratoryRate = ($respiratoryRate === '' || $respiratoryRate === null) ? null : (int)$respiratoryRate;
            $systolicBP = ($systolicBP === '' || $systolicBP === null) ? null : (int)$systolicBP;
            $diastolicBP = ($diastolicBP === '' || $diastolicBP === null) ? null : (int)$diastolicBP;
            $ewsTotal = ($ewsTotal === '' || $ewsTotal === null) ? null : (int)$ewsTotal;

            try {
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Insert to vital_sign_integration table (existing functionality)
                $stmt = $pdo->prepare("
                    INSERT INTO vital_sign_integration (
                        device_timestamp, raw_message, mrn, patient_name,
                        respiratory_rate, spo2, pulse_rate, temperature,
                        nurse_id, systolic_bp, diastolic_bp, mean_bp,
                        avpu, ews_score_total, body_position, height, weight,
                        processed, created_at, updated_at
                    ) VALUES (
                        :device_timestamp, :raw_message, :mrn, :patient_name,
                        :respiratory_rate, :spo2, :pulse_rate, :temperature,
                        :nurse_id, :systolic_bp, :diastolic_bp, :mean_bp,
                        :avpu, :ews_score_total, :body_position, :height, :weight,
                        :processed, :created_at, :updated_at
                    )
                ");
                
                $stmt->execute([
                    ':device_timestamp' => $currentTime,
                    ':raw_message' => $input,
                    ':mrn' => $mrn,
                    ':patient_name' => $name,
                    ':respiratory_rate' => $respiratoryRate,
                    ':spo2' => $spo2,
                    ':pulse_rate' => $pulseRate,
                    ':temperature' => $temperature,
                    ':nurse_id' => $nurseId,
                    ':systolic_bp' => $systolicBP,
                    ':diastolic_bp' => $diastolicBP,
                    ':mean_bp' => $meanBP,
                    ':avpu' => $avpu,
                    ':ews_score_total' => $ewsTotal,
                    ':body_position' => $bodyPosition,
                    ':height' => $height,
                    ':weight' => $weight,
                    ':processed' => 0, // false, not processed yet
                    ':created_at' => $currentTime,
                    ':updated_at' => $currentTime
                ]);

                error_log("Vital signs received and stored in vital_sign_integration table at $currentTime for patient $name (MRN: $mrn)");
                
                // NEW FUNCTIONALITY: Also insert to vital_signs table if patient is found
                $patient = findPatientByMrn($pdo, $mrn);
                if ($patient) {
                    error_log("Patient found in database: ID = {$patient['id']}, Name = {$patient['name']}");
                    
                    // Always use nurse ID 6 for all machine recordings
                    $recordedBy = 6;
                    
                    // Convert AVPU format for vital_signs table
                    $consciousness = null;
                    if ($avpu) {
                        switch (strtolower($avpu)) {
                            case 'alert':
                                $consciousness = 'A';
                                break;
                            case 'reacting to voice':
                                $consciousness = 'V';
                                break;
                            case 'reacting to pain':
                                $consciousness = 'P';
                                break;
                            case 'unresponsive':
                                $consciousness = 'U';
                                break;
                        }
                    }
                    
                    // Prepare vital signs data for EWS calculation
                    $vitalSignsData = [
                        'temperature' => $temperature, // Now available from HL7 message
                        'heart_rate' => $pulseRate,
                        'respiratory_rate' => $respiratoryRate,
                        'systolic_bp' => $systolicBP,
                        'oxygen_saturation' => $spo2,
                        'consciousness' => $consciousness,
                    ];
                    
                    // Calculate EWS scores
                    $ewsResults = calculateEWS($vitalSignsData);
                    
                    // Insert to vital_signs table
                    $vitalSignStmt = $pdo->prepare("
                        INSERT INTO vital_signs (
                            patient_id, recorded_by, recorded_at,
                            temperature, heart_rate, respiratory_rate,
                            systolic_bp, diastolic_bp, oxygen_saturation, consciousness,
                            temperature_score, heart_rate_score, respiratory_rate_score,
                            blood_pressure_score, oxygen_saturation_score, consciousness_score,
                            total_ews, notes, created_at, updated_at
                        ) VALUES (
                            :patient_id, :recorded_by, :recorded_at,
                            :temperature, :heart_rate, :respiratory_rate,
                            :systolic_bp, :diastolic_bp, :oxygen_saturation, :consciousness,
                            :temperature_score, :heart_rate_score, :respiratory_rate_score,
                            :blood_pressure_score, :oxygen_saturation_score, :consciousness_score,
                            :total_ews, :notes, :created_at, :updated_at
                        )
                    ");
                    
                    $vitalSignStmt->execute([
                        ':patient_id' => $patient['id'],
                        ':recorded_by' => $recordedBy,
                        ':recorded_at' => $currentTime,
                        ':temperature' => $temperature, // Now available from HL7 message
                        ':heart_rate' => $pulseRate,
                        ':respiratory_rate' => $respiratoryRate,
                        ':systolic_bp' => $systolicBP,
                        ':diastolic_bp' => $diastolicBP,
                        ':oxygen_saturation' => $spo2,
                        ':consciousness' => $consciousness,
                        ':temperature_score' => $ewsResults['scores']['temperature_score'],
                        ':heart_rate_score' => $ewsResults['scores']['heart_rate_score'],
                        ':respiratory_rate_score' => $ewsResults['scores']['respiratory_rate_score'],
                        ':blood_pressure_score' => $ewsResults['scores']['blood_pressure_score'],
                        ':oxygen_saturation_score' => $ewsResults['scores']['oxygen_saturation_score'],
                        ':consciousness_score' => $ewsResults['scores']['consciousness_score'],
                        ':total_ews' => $ewsResults['total_ews'],
                        ':notes' => "Automatically recorded from vital signs monitor via HL7",
                        ':created_at' => $currentTime,
                        ':updated_at' => $currentTime
                    ]);
                    
                    error_log("Vital signs also inserted to vital_signs table for patient ID {$patient['id']} with Total EWS: {$ewsResults['total_ews']}");
                } else {
                    error_log("Patient not found in database with MRN: $mrn");
                }
                
            } catch (PDOException $e) {
                error_log("Database error in vs8_listener_v3.php: " . $e->getMessage());
                echo "Database error: " . $e->getMessage() . "\n";
            }
        }
        
        socket_close($client);
    }
    
    usleep(100000);
}