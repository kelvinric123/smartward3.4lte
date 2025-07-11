<?php
// Simple HL7 message sender for testing
error_reporting(E_ALL);

$hl7_message = "MSH|^~\&|NC_Monitor|KC261551373G|||20250610015127+0800||ORU^R01^ORU_R01|202506100151270003|P|2.6||||||UNICODE UTF-8\r" .
"PID|||10006||lee|||M||Unknown\r" .
"PV1||I|^^&10|R\r" .
"OBR||||1^MON_PARAM^99COMEN|||20250610015127+0800\r" .
"OBX||NM|150456^MDC_PULS_OXIM_SAT_O2^MDC|1.3.1.150456|97|262688^MDC_DIM_PERCENT^MDC|||||F|||20250610015127+0800\r" .
"OBX||NM|160324^MDC_SPO2_SIGNAL_QUALITY_INDEX^MDC|1.3.1.160324|3.56|262688^MDC_DIM_PERCENT^MDC|||||F|||20250610015127+0800\r" .
"OBX||NM|149522^MDC_PULS_RATE^MDC|1.0.0.149522|59|264864^MDC_DIM_BEAT_PER_MIN^MDC|||||F|||20250610015127+0800\r" .
"OBX||NM|150456^MDC_PULS_OXIM_SAT_O2^MDC|1.3.2.150456|97|262688^MDC_DIM_PERCENT^MDC|||||F|||20250610015127+0800\r" .
"OBX||NM|150468^MDC_PULS_OXIM_SAT_O2_DIFF^MDC|1.3.2.150468|0|262688^MDC_DIM_PERCENT^MDC|||||F|||20250610015127+0800\r" .
"OBX||NM|151562^MDC_RESP_RATE^MDC|1.14.1.151562|20|264928^MDC_DIM_RESP_PER_MIN^MDC|||||F|||20250610015127+0800\r" .
"OBX||NM|150344^MDC_TEMP^MDC|1.2.1.150344|37.0|268192^MDC_DIM_DEGC^MDC|||||F|||20250610015127+0800\r" .
"OBX||NM|150021^MDC_PRESS_BLD_NONINV_SYS^MDC|1.1.9.150021|120|266016^MDC_DIM_MMHG^MDC|||||F|||20250610015127+0800\r" .
"OBX||NM|150022^MDC_PRESS_BLD_NONINV_DIA^MDC|1.1.9.150022|80|266016^MDC_DIM_MMHG^MDC|||||F|||20250610015127+0800\r" .
"OBX||NM|150023^MDC_PRESS_BLD_NONINV_MEAN^MDC|1.1.9.150023|90|266016^MDC_DIM_MMHG^MDC|||||F|||20250610015127+0800\r" .
"OBX||CNE|2453^COMEN_POS_BODY^99COMEN|1.1.9.2453|2821^COMEN_POS_SITTING^99COMEN||||||F|||20250610015127+0800\r" .
"OBX||NM|188740^MDC_LEN_BODY_ACTUAL^MDC|1.10.1.188740||263441^MDC_DIM_CENTI_M^MDC|||||F|||20250610015127+0800\r" .
"OBX||NM|188736^MDC_MASS_BODY_ACTUAL^MDC|1.10.1.188736||263875^MDC_DIM_KILO_G^MDC|||||F|||20250610015127+0800\r";

echo "Sending HL7 message to VS8 listener...\n";
echo "Message length: " . strlen($hl7_message) . " bytes\n";

// Create socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    die("socket_create() failed: " . socket_strerror(socket_last_error()) . "\n");
}

// Connect to the listener
$result = socket_connect($socket, '127.0.0.1', 2575);
if ($result === false) {
    die("socket_connect() failed: " . socket_strerror(socket_last_error($socket)) . "\n");
}

// Send message
$bytes_sent = socket_write($socket, $hl7_message, strlen($hl7_message));
if ($bytes_sent === false) {
    die("socket_write() failed: " . socket_strerror(socket_last_error($socket)) . "\n");
}

echo "Successfully sent $bytes_sent bytes to VS8 listener\n";

// Close socket
socket_close($socket);

echo "Test message sent successfully!\n";
echo "Check the listener logs and database for processed data.\n";
?> 