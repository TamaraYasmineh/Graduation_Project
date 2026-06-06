{{-- الأمراض والحساسية --}}
<div class="card">
    <div class="section-title">الأمراض والحساسية</div>

    @foreach([
        ['title' => 'الأمراض المزمنة', 'value' => $record->chronic_diseases],
        ['title' => 'الحساسية',        'value' => $record->allergies],
        ['title' => 'الأدوية الحالية', 'value' => $record->medications],
    ] as $block)
        <div class="text-title">{{ $block['title'] }}</div>
        <div class="text-block">{{ $block['value'] ?? 'لا يوجد' }}</div>
    @endforeach
</div>

{{-- التاريخ الطبي --}}
<div class="card">
    <div class="section-title">التاريخ الطبي</div>

    @foreach([
        ['title' => 'العمليات السابقة', 'value' => $record->surgeries],
        ['title' => 'التاريخ العائلي', 'value' => $record->family_history],
        ['title' => 'ملاحظات',         'value' => $record->notes],
    ] as $block)
        <div class="text-title">{{ $block['title'] }}</div>
        <div class="text-block">{{ $block['value'] ?? 'لا يوجد' }}</div>
    @endforeach
</div>
