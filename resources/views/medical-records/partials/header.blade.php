<div class="header">
    <div class="logo">
        <img src="{{ asset('images/pioneers-clinic-logo.png') }}">
    </div>
    <div class="header-content">
        <div class="clinic-name">Pioneers Clinic</div>
        <div class="clinic-sub">نظام السجل الطبي الإلكتروني المتطور</div>
    </div>
    <div class="patient-box">
        <div class="patient-label">اسم المريض</div>
        <div class="patient-name">{{ $record->patient->name ?? 'غير محدد' }}</div>
    </div>
</div>
