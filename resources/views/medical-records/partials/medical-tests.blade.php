<div class="card">

    <div class="section-title">
        التحاليل الطبية
    </div>

    @forelse($record->medicalTests as $test)

        @php
            $extension = strtolower(pathinfo($test->file_path, PATHINFO_EXTENSION));

            $fileUrl = Storage::url($test->file_path);

            $isImage = in_array($extension, [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'webp'
            ]);

            $isPdf = $extension === 'pdf';

            $isDoc = in_array($extension, [
                'doc',
                'docx'
            ]);
        @endphp

        <div class="item full medical-test-item">

            <div class="label">
                نوع التحليل
            </div>

            <div class="value">
                {{ $test->test_type ?? 'غير محدد' }}
            </div>

            <div class="label mt-15">
                تاريخ الرفع
            </div>

            <div class="value">
                {{ $test->created_at?->format('Y-m-d H:i') ?? 'غير معروف' }}
            </div>

            <div class="label mt-15">
                الملاحظات
            </div>

            <div class="text-block">
                {{ $test->notes ?? 'لا توجد ملاحظات' }}
            </div>

            {{-- أزرار التحكم --}}
            <div class="file-actions">

                <a href="{{ route('medical-test.view', $test->id) }}"
                   target="_blank"
                   class="badge blue">
                    عرض الملف
                </a>

                <a href="{{ route('medical-test.download', $test->id) }}"
                   target="_blank"
                   class="badge blue">
                    تحميل الملف
                </a>

            </div>

            {{-- صورة --}}
            @if($isImage)

                <img
                    src="{{ $fileUrl }}"
                    alt="Medical Test"
                    class="medical-image">

            {{-- PDF --}}
            @elseif($isPdf)

                <iframe
                    src="{{ $fileUrl }}"
                    class="pdf-viewer">
                </iframe>

            {{-- Word --}}
            @elseif($isDoc)

                <div class="word-file-box">

                    <div class="word-icon">
                        📄
                    </div>

                    <div class="word-name">
                        {{ basename($test->file_path) }}
                    </div>

                </div>

            {{-- أي ملف آخر --}}
            @else

                <div class="text-block">
                    {{ basename($test->file_path) }}
                </div>

            @endif

        </div>

    @empty

        <div class="text-block">
            لا توجد تحاليل طبية
        </div>

    @endforelse

</div>
