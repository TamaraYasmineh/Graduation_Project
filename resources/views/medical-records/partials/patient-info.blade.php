{{-- بيانات شخصية --}}
<div class="card">
    <div class="section-title">بيانات المريض الشخصية</div>
    <div class="grid">
        <div class="item">
            <div class="label">الجنس</div>
            <div class="value">{{ $record->patient->gender ?? 'غير محدد' }}</div>
        </div>
        <div class="item">
            <div class="label">تاريخ الميلاد</div>
            <div class="value">{{ optional($record->patient->patient)->date_of_birth ?? 'غير محدد' }}</div>
        </div>
        <div class="item">
            <div class="label">العمر</div>
            <div class="value">
                {{ optional($record->patient->patient)->date_of_birth
                    ? \Carbon\Carbon::parse($record->patient->patient->date_of_birth)->age . ' سنة'
                    : 'غير محدد' }}
            </div>
        </div>
        <div class="item">
            <div class="label">الصورة الشخصية</div>
            <div class="value">
                @if ($record->patient->profile_image)
                    <img src="{{ asset('storage/' . $record->patient->profile_image) }}"
                         style="width:70px;height:70px;border-radius:50%;object-fit:cover;">
                @else
                    غير متوفرة
                @endif
            </div>
        </div>
    </div>
</div>

{{-- الإحصائيات --}}
<div class="stats">
    @foreach([
        ['value' => $record->blood_type,     'label' => 'فصيلة الدم'],
        ['value' => $record->height,         'label' => 'الطول / سم'],
        ['value' => $record->weight,         'label' => 'الوزن / كغ'],
        ['value' => $record->blood_pressure, 'label' => 'ضغط الدم'],
    ] as $stat)
        <div class="stat-card">
            <div class="stat-number">{{ $stat['value'] ?? '—' }}</div>
            <div class="stat-label">{{ $stat['label'] }}</div>
        </div>
    @endforeach
</div>

{{-- المعلومات الأساسية --}}
<div class="card">
    <div class="section-title">المعلومات الأساسية</div>
    <div class="grid">
        <div class="item">
            <div class="label">التدخين</div>
            <span class="badge {{ $record->is_smoker ? 'yes' : 'no' }}">
                {{ $record->is_smoker ? 'مدخن' : 'غير مدخن' }}
            </span>
        </div>
        <div class="item">
            <div class="label">الحالة الاجتماعية</div>
            <div class="value">{{ $record->marital_status ?? 'غير محدد' }}</div>
        </div>
        <div class="item full">
            <div class="label">عدد الأطفال</div>
            <span class="badge blue">{{ $record->number_of_children ?? '0' }} أطفال</span>
        </div>
    </div>
</div>
