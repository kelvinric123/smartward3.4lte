<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hl7MappingProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'mapping_codes',
        'is_active',
        'is_default',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'mapping_codes' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get the user who created this profile
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the currently active profile
     */
    public static function getActiveProfile()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Get the default profile
     */
    public static function getDefaultProfile()
    {
        return self::where('is_default', true)->first();
    }

    /**
     * Set this profile as active (and deactivate others)
     */
    public function setAsActive()
    {
        // Deactivate all other profiles
        self::where('id', '!=', $this->id)->update(['is_active' => false]);
        
        // Activate this profile
        $this->update(['is_active' => true]);
        
        return $this;
    }

    /**
     * Set this profile as default (and remove default from others)
     */
    public function setAsDefault()
    {
        // Remove default from all other profiles
        self::where('id', '!=', $this->id)->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
        
        return $this;
    }

    /**
     * Get default HL7 mapping codes for new profiles
     */
    public static function getDefaultMappingCodes()
    {
        return [
            'patient_id' => 'PID.3',
            'patient_name' => 'PID.5',
            'mrn' => 'PID.3.1',
            'identity_number' => 'PID.3.4',
            'age' => 'PID.7',
            'gender' => 'PID.8',
            'email' => 'PID.13.4',
            'phone' => 'PID.13.1',
            'address' => 'PID.11',
            'allergies' => 'AL1.3',
            'ward_id' => 'PV1.3.1',
            'bed_id' => 'PV1.3.3',
            'consultant_id' => 'PV1.7',
            'nurse_id' => 'PV1.8',
            'admission_date' => 'PV1.44',
            'patient_class' => 'PV1.2',
            'diet_type' => 'ORC.7',
            'fall_risk_alert' => 'ZAT.1',
            'isolation_precautions' => 'ZIT.1',
            'expected_length_of_stay' => 'PV1.48',
            'expected_discharge_date' => 'PV1.45',
            'clinical_alerts' => 'NTE.3',
            'admission_notes' => 'DG1.4',
        ];
    }

    /**
     * Create or update a profile
     */
    public static function createOrUpdateProfile($data)
    {
        $profile = self::updateOrCreate(
            ['name' => $data['name']],
            [
                'description' => $data['description'] ?? null,
                'mapping_codes' => $data['mapping_codes'] ?? self::getDefaultMappingCodes(),
                'created_by' => auth()->id(),
            ]
        );

        // If this is the first profile, make it default and active
        if (self::count() === 1) {
            $profile->setAsDefault();
            $profile->setAsActive();
        }

        return $profile;
    }

    /**
     * Get mapping code for a specific field
     */
    public function getMappingCode($field)
    {
        return $this->mapping_codes[$field] ?? null;
    }

    /**
     * Set mapping code for a specific field
     */
    public function setMappingCode($field, $code)
    {
        $mappingCodes = $this->mapping_codes;
        $mappingCodes[$field] = $code;
        $this->update(['mapping_codes' => $mappingCodes]);
        
        return $this;
    }

    /**
     * Get all available profiles for selection
     */
    public static function getAllForSelection()
    {
        return self::orderBy('is_default', 'desc')
                   ->orderBy('is_active', 'desc')
                   ->orderBy('name')
                   ->get();
    }
}
