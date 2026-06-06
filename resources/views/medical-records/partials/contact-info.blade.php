<div class="card">
    <div class="section-title">معلومات التواصل</div>
    <div class="grid">
        <div class="item">
            <div class="label">البريد الإلكتروني</div>
            <div class="value">{{ $record->patient->email ?? 'غير محدد' }}</div>
        </div>
        <div class="item">
            <div class="label">رقم الهاتف</div>
            <div class="value">{{ $record->patient->phone ?? 'غير محدد' }}</div>
        </div>
        <div class="item">
            <div class="label">الدولة</div>
            <div class="value">{{ optional($record->patient->patient)->country ?? 'غير محدد' }}</div>
        </div>
        <div class="item">
            <div class="label">المدينة</div>
            <div class="value">{{ optional($record->patient->patient)->city ?? 'غير محدد' }}</div>
        </div>
        <div class="item full">
            <div class="label">العنوان الكامل</div>
            <div class="value">
                {{ optional($record->patient->patient)->address
                    ?? optional($record->patient->patient)->country . ' - ' . optional($record->patient->patient)->city }}
            </div>
        </div>
        <div class="item full">
            <div class="label">جهة الاتصال للطوارئ</div>
            <div class="value">{{ optional($record->patient->patient)->emergency_contact ?? 'غير محدد' }}</div>
        </div>
    </div>
</div>
