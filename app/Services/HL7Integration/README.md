# HL7 Integration System for SmartWard

## Overview

This HL7 integration system provides a comprehensive solution for receiving, parsing, mapping, and processing HL7 admission messages (ADT^A01) in the SmartWard application. The system is designed to automatically admit patients based on HL7 messages received from external hospital systems.

## Architecture

The system consists of four main components:

1. **HL7 HTTP Listener** - Receives incoming HL7 messages via HTTP endpoints
2. **HL7 Parser** - Parses HL7 messages into structured data
3. **HL7 Mapper** - Maps parsed HL7 data to SmartWard's data structure
4. **Message History & Status View** - Web interface for monitoring message processing

## Components

### 1. HL7ListenerService (`HL7ListenerService.php`)

**Purpose**: Handles incoming HL7 messages and orchestrates the processing pipeline.

**Key Features**:
- Receives and validates HL7 messages
- Coordinates parsing and mapping
- Manages message status tracking
- Handles error logging and recovery
- Processes patient admissions

### 2. HL7ParserService (`HL7ParserService.php`)

**Purpose**: Parses HL7 messages into structured PHP arrays.

**Supported Segments**:
- **MSH** - Message Header
- **PID** - Patient Identification
- **PV1** - Patient Visit
- **EVN** - Event Type
- **AL1** - Allergy Information

**Key Features**:
- Validates HL7 message format
- Extracts patient demographic data
- Parses admission details
- Handles date/time conversion
- Processes component and field separators

### 3. HL7AdmissionMapperService (`HL7AdmissionMapperService.php`)

**Purpose**: Maps parsed HL7 data to SmartWard's admission format.

**Key Features**:
- Maps patient data to SmartWard patient model
- Resolves ward, consultant, and nurse assignments
- Handles data validation and defaults
- Provides mapping configuration options
- Supports flexible field mapping

### 4. HL7MessageLog Model (`HL7MessageLog.php`)

**Purpose**: Tracks all HL7 messages and their processing status.

**Key Features**:
- Stores raw HL7 messages
- Tracks processing status and timestamps
- Provides statistical analysis
- Supports filtering and searching
- Maintains performance metrics

## Database Schema

The system uses a single table `hl7_message_logs` with the following structure:

```sql
- id (primary key)
- message_id (unique identifier)
- message_control_id (HL7 control ID)
- message_type (HL7 message type)
- raw_message (original HL7 message)
- headers (HTTP headers)
- parsed_data (parsed HL7 data)
- mapped_data (mapped admission data)
- status (processing status)
- error_message (error details)
- received_at (timestamp)
- processed_at (timestamp)
- completed_at (timestamp)
- processing_time_ms (performance metric)
- admission_id (linked admission)
- patient_mrn (patient identifier)
- patient_name (patient name)
```

## API Endpoints

### Public Endpoints (No Authentication)

```
POST /api/hl7/receive
- Receives HL7 messages from external systems
- Content-Type: text/plain or application/hl7-v2
- Returns: JSON response with processing status

GET /api/hl7/status/{messageId}
- Retrieves processing status of a specific message
- Returns: JSON response with message status
```

### Admin Endpoints (Authentication Required)

```
GET /admin/hl7/messages
- View message history with filtering options
- Displays dashboard with statistics

GET /admin/hl7/messages/{id}
- View detailed information about a specific message
- Shows raw message, parsed data, and mapping results

POST /admin/hl7/messages/{id}/retry
- Retry processing of a failed message

POST /admin/hl7/test-message
- Send a test HL7 message for testing purposes

GET /admin/hl7/dashboard-data
- Get dashboard statistics in JSON format

POST /admin/hl7/cleanup-logs
- Clean up old message logs
```

## Web Interface

### Message History Page

**Features**:
- Real-time statistics dashboard
- Message filtering by status, date, and patient
- Pagination and search capabilities
- Performance metrics display
- Recent error monitoring
- Test message functionality

### Message Details Page

**Features**:
- Complete message information
- Raw HL7 message display
- Parsed data visualization
- Mapping results comparison
- Error details and retry options
- Related admission information

## Usage

### Receiving HL7 Messages

External systems can send HL7 messages to:
```
POST https://your-domain.com/api/hl7/receive
Content-Type: application/hl7-v2

MSH|^~\&|SENDING_APP|SENDING_FACILITY|SMARTWARD|SMARTWARD|20231201120000||ADT^A01|MSG123|P|2.5
EVN|A01|20231201120000|||DOC1^SMITH^JOHN^||20231201120000
PID|1||MRN1234^^^MR^MR||DOE^JOHN^MIDDLE||19800101|M|||123 MAIN ST^^CITY^STATE^12345||555-1234||||
PV1|1|I|ICU^01^ICU^HOSPITAL|||DOC1^SMITH^JOHN^|MED||||||||||||||||||||||||||||||||||||20231201120000
```

### Processing Status

The system tracks messages through these statuses:
- **received** - Message received and logged
- **parsed** - Message successfully parsed
- **mapped** - Data successfully mapped
- **processed** - Admission successfully created
- **failed** - Processing failed (with error details)

### Error Handling

Failed messages can be:
- Viewed in the admin interface
- Retried manually
- Analyzed for debugging
- Exported for external analysis

## Configuration

### Ward Mapping

The system automatically maps HL7 ward names to SmartWard ward IDs:
- Exact name matching
- Partial name matching
- Facility-based mapping
- Default ward assignment

### Consultant Mapping

Consultants are mapped using:
- External ID matching
- Name-based matching
- Default consultant assignment

### Nurse Mapping

Nurses are mapped using:
- External ID matching
- Name-based matching
- Default nurse assignment

## Testing

### Test Message

Use the "Test Message" button in the admin interface to send a sample HL7 message for testing.

### Manual Testing

Send a POST request to `/api/hl7/receive` with a valid HL7 message:

```bash
curl -X POST http://your-domain.com/api/hl7/receive \
  -H "Content-Type: application/hl7-v2" \
  -d "MSH|^~\&|TEST|TEST|SMARTWARD|SMARTWARD|20231201120000||ADT^A01|TEST123|P|2.5
EVN|A01|20231201120000
PID|1||TEST123^^^MR^MR||DOE^JOHN^||19800101|M
PV1|1|I|WARD1^BED1^WARD1|||||||||||||||||||||||||||||||||||||20231201120000"
```

## Monitoring

### Performance Metrics

The system tracks:
- Average processing time
- Success/failure rates
- Message volume by day
- Error frequency
- Peak processing times

### Logging

All activities are logged using Laravel's logging system:
- Message processing steps
- Error conditions
- Performance metrics
- Mapping decisions

## Maintenance

### Log Cleanup

Old message logs can be cleaned up using:
```
POST /admin/hl7/cleanup-logs
{
  "days": 90
}
```

### Database Maintenance

Regular maintenance includes:
- Cleaning up old successful messages
- Archiving failed messages
- Optimizing database indexes
- Monitoring table size

## Security

### Authentication

- Admin endpoints require authentication
- Public endpoints are rate-limited
- Message content is validated
- SQL injection protection

### Data Protection

- Patient data is handled securely
- Message content is encrypted at rest
- Audit trails are maintained
- Access is logged

## Troubleshooting

### Common Issues

1. **Message Format Errors**
   - Check HL7 message format
   - Verify segment separators
   - Validate required fields

2. **Mapping Failures**
   - Verify ward/consultant data
   - Check patient MRN format
   - Review mapping configuration

3. **Performance Issues**
   - Monitor processing times
   - Check database indexes
   - Review log file sizes

### Debugging

1. Enable detailed logging
2. Check message details page
3. Review error messages
4. Use test message function
5. Monitor system resources

## Future Enhancements

Potential improvements include:
- Support for additional HL7 message types
- Real-time message monitoring
- Advanced mapping configuration
- Message routing capabilities
- Integration with external systems
- Performance optimization

## Support

For support and questions:
- Check the message details page for errors
- Review the system logs
- Use the test message functionality
- Contact the development team

---

**Note**: This system is specifically designed for SmartWard's admission workflow and may require customization for different hospital systems or workflows. 