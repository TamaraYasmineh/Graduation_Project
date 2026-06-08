<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * @property int $id
 * @property int $patient_id
 * @property string|null $chronic_diseases
 * @property string|null $allergies
 * @property string|null $medications
 * @property string|null $notes
 * @property int $is_smoker
 * @property float|null $height
 * @property float|null $weight
 * @property string|null $blood_type
 * @property string|null $surgeries
 * @property string|null $family_history
 * @property string|null $blood_pressure
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $patient
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereAllergies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereBloodPressure($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereBloodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereChronicDiseases($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereFamilyHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereIsSmoker($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereMedications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereSurgeries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicalRecord whereWeight($value)
 *
 * @mixin \Eloquent
 */
class MedicalRecord extends Model
{
    protected $fillable = [
        'patient_id',
        'chronic_diseases',
        'allergies',
        'medications',
        'notes',
        'marital_status',
        'number_of_children',
        'is_smoker',
        'height',
        'weight',
        'blood_type',
        'surgeries',
        'family_history',
        'blood_pressure',
        'qr_code_path',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function medicalTests()
    {
        return $this->hasMany(MedicalTest::class);
    }

    // ========== دوال QR Code ==========

    /**
     * توليد token مشفر وآمن يحمل patient_id و record id
     */
    public function getScanToken(): string
    {
        return encrypt($this->patient_id.'|'.$this->id);
    }

    /**
     * توليد QR Code وحفظه في Storage
     * يُستدعى مرة واحدة عند إنشاء السجل
     */
    public function generateAndSaveQrCode(): string
    {
        $appUrl = rtrim(config('services.public_url'), '/');
        $token = urlencode(encrypt($this->patient_id.'|'.$this->id));

        // ← بناء الرابط يدوياً بدون route()
        $scanUrl = $appUrl.'/scan?token='.$token;

        $qrImage = QrCode::format('svg')
            ->size(400)
            ->errorCorrection('H')
            ->generate($scanUrl);

        $path = 'qrcodes/patient_'.$this->patient_id.'.svg';
        Storage::disk('public')->put($path, $qrImage);
        $this->update(['qr_code_path' => $path]);

        return $path;

        // // توليد صورة QR بصيغة PNG
        // $qrImage = QrCode::format('svg')
        //     ->size(400)
        //     ->margin(2)
        //     ->errorCorrection('H')
        //     ->generate($url);

        // // مسار الحفظ
        // $path = 'qrcodes/patient_' . $this->patient_id . '.svg';

        // // حفظ الصورة في storage/app/public/qrcodes/
        // Storage::disk('public')->put($path, $qrImage);

        // // تحديث الحقل في قاعدة البيانات
        // $this->update(['qr_code_path' => $path]);

        // return $path;
    }

    /**
     * جلب الرابط الكامل لصورة QR
     */
    public function getQrCodeUrl(): string
    {
        if (! $this->qr_code_path ||
            ! Storage::disk('public')->exists($this->qr_code_path)) {
            $this->generateAndSaveQrCode();
        }

        $appUrl = rtrim(config('services.public_url'), '/');

        return $appUrl.'/storage/'.$this->qr_code_path;
    }

    public function treatmentPlan()
    {
        return $this->hasOne(Treatment_plan::class);
    }
}
