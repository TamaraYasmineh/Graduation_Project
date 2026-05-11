<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        السجل الطبي - {{ $record->patient->name }}
    </title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {

            --primary: #2563eb;
            --primary2: #60a5fa;

            --success: #16a34a;
            --danger: #dc2626;

            --text: #0f172a;
            --text-light: #64748b;

            --glass:
                rgba(255, 255, 255, .24);

            --glass-strong:
                rgba(255, 255, 255, .38);

            --border:
                rgba(255, 255, 255, .48);

            --shadow:
                0 10px 40px rgba(37, 99, 235, .12);

        }

        /* BODY */

        body {

            font-family: 'Cairo', sans-serif;

            min-height: 100vh;

            overflow-x: hidden;

            position: relative;

            padding: 45px 20px;

            color: var(--text);

            background:
                radial-gradient(circle at top right, #bfdbfe 0%, transparent 30%),
                radial-gradient(circle at bottom left, #93c5fd 0%, transparent 30%),
                linear-gradient(135deg,
                    #edf5ff 0%,
                    #dbeafe 45%,
                    #f8fbff 100%);
        }

        /* DNA BG */

        body::before {

            content: '';

            position: fixed;

            inset: 0;

            background:
                linear-gradient(rgba(255, 255, 255, .72),
                    rgba(255, 255, 255, .72)),
                url('{{ asset('images/dna-bg.jpg') }}');

            background-size: cover;

            background-position: center;

            opacity: .22;

            z-index: -4;

            filter:
                blur(2px) brightness(1.08) saturate(1.4);
        }

        /* FLOATING LIGHTS */

        .glow {

            position: fixed;

            border-radius: 50%;

            filter: blur(110px);

            z-index: -3;

            opacity: .55;
        }

        .glow.one {

            width: 320px;
            height: 320px;

            background: #60a5fa;

            top: -120px;
            right: -80px;
        }

        .glow.two {

            width: 260px;
            height: 260px;

            background: #38bdf8;

            bottom: -90px;
            left: -70px;
        }

        .glow.three {

            width: 220px;
            height: 220px;

            background: #93c5fd;

            top: 45%;
            left: 50%;

            transform: translate(-50%, -50%);
        }

        /* CONTAINER */

        .container {

            max-width: 1100px;

            margin: auto;
        }

        /* HEADER */

        .header {

            position: relative;

            overflow: hidden;

            display: flex;

            align-items: center;

            gap: 25px;

            padding: 32px;

            border-radius: 34px;

            margin-bottom: 28px;

            background:
                linear-gradient(145deg,
                    rgba(255, 255, 255, .38),
                    rgba(255, 255, 255, .18));

            backdrop-filter: blur(26px);

            -webkit-backdrop-filter: blur(26px);

            border:
                1px solid rgba(255, 255, 255, .58);

            box-shadow:
                0 15px 45px rgba(37, 99, 235, .10),
                inset 0 1px 1px rgba(255, 255, 255, .85);
        }

        /* HEADER SHINE */

        .header::before {

            content: '';

            position: absolute;

            top: 0;
            left: 0;
            right: 0;

            height: 1px;

            background:
                linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, .95),
                    transparent);
        }

        /* LOGO */

        .logo {

            width: 110px;
            height: 110px;

            border-radius: 30px;

            display: flex;
            align-items: center;
            justify-content: center;

            overflow: hidden;

            background:
                linear-gradient(145deg,
                    rgba(255, 255, 255, .55),
                    rgba(255, 255, 255, .15));

            border: 1px solid rgba(255, 255, 255, .7);

            backdrop-filter: blur(20px);

            box-shadow:
                0 10px 35px rgba(37, 99, 235, .12),
                inset 0 1px 1px rgba(255, 255, 255, .9);
        }

        .logo img {

            width: 100%;
            height: 100%;

            object-fit: cover;

            border-radius: 30px;

            position: relative;
            z-index: 2;
        }

        .logo::after {

            content: '';

            position: absolute;

            width: 140%;
            height: 140%;

            background:
                radial-gradient(circle,
                    rgba(59, 130, 246, .18),
                    transparent 70%);

            z-index: 0;
        }

        /* TITLES */

        .header-content {

            flex: 1;
        }

        .clinic-name {

            font-size: 34px;

            font-weight: 900;

            background:
                linear-gradient(90deg,
                    #1d4ed8,
                    #3b82f6,
                    #60a5fa);

            -webkit-background-clip: text;

            -webkit-text-fill-color: transparent;

            text-shadow:
                0 0 20px rgba(59, 130, 246, .16);
        }

        .clinic-sub {

            margin-top: 8px;

            color: #64748b;

            font-size: 14px;

            font-weight: 600;

            letter-spacing: .4px;
        }

        /* PATIENT CARD */

        .patient-box {

            position: relative;

            min-width: 270px;

            padding: 20px 24px;

            border-radius: 26px;

            overflow: hidden;

            background:
                linear-gradient(145deg,
                    rgba(255, 255, 255, .46),
                    rgba(255, 255, 255, .20));

            border:
                1px solid rgba(255, 255, 255, .65);

            backdrop-filter: blur(20px);

            box-shadow:
                0 12px 35px rgba(37, 99, 235, .10),
                inset 0 1px 1px rgba(255, 255, 255, .85);
        }

        /* PATIENT SHINE */

        .patient-box::before {

            content: '';

            position: absolute;

            top: 0;
            left: 0;
            right: 0;

            height: 1px;

            background:
                linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, .95),
                    transparent);
        }

        .patient-icon {

            position: absolute;

            top: 18px;
            left: 18px;

            width: 42px;
            height: 42px;

            border-radius: 15px;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                rgba(37, 99, 235, .10);

            color: #2563eb;

            font-size: 20px;

            border:
                1px solid rgba(96, 165, 250, .25);
        }

        .patient-label {

            font-size: 12px;

            color: #64748b;

            margin-bottom: 8px;

            font-weight: 700;

            letter-spacing: .5px;
        }

        .patient-name {

            font-size: 24px;

            font-weight: 900;

            color: #2563eb;

            line-height: 1.3;

            text-shadow:
                0 0 12px rgba(59, 130, 246, .15);
        }

        /* STATS */

        .stats {

            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 20px;

            margin-bottom: 26px;
        }

        .stat-card {

            position: relative;

            overflow: hidden;

            border-radius: 28px;

            padding: 26px;

            text-align: center;

            background:
                linear-gradient(145deg,
                    rgba(255, 255, 255, .42),
                    rgba(255, 255, 255, .18));

            border:
                1px solid rgba(255, 255, 255, .55);

            backdrop-filter: blur(22px);

            transition: .35s;

            box-shadow:
                0 10px 35px rgba(37, 99, 235, .08);
        }

        .stat-card:hover {

            transform:
                translateY(-5px) scale(1.02);

            box-shadow:
                0 18px 45px rgba(37, 99, 235, .16);
        }

        .stat-card::after {

            content: '';

            position: absolute;

            top: 0;
            left: -120%;

            width: 70%;
            height: 100%;

            background:
                linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, .25),
                    transparent);

            transform: skewX(-25deg);

            transition: 1.2s;
        }

        .stat-card:hover::after {

            left: 130%;
        }

        .stat-number {

            font-size: 34px;

            font-weight: 900;

            color: #2563eb;

            text-shadow:
                0 0 18px rgba(59, 130, 246, .25);
        }

        .stat-label {

            margin-top: 8px;

            font-size: 13px;

            color: #64748b;

            font-weight: 600;
        }

        /* MAIN CARD */

        .card {

            position: relative;

            overflow: hidden;

            border-radius: 34px;

            padding: 32px;

            margin-bottom: 24px;

            background:
                linear-gradient(145deg,
                    rgba(255, 255, 255, .36),
                    rgba(255, 255, 255, .15));

            border:
                1px solid rgba(255, 255, 255, .58);

            backdrop-filter: blur(26px);

            box-shadow:
                0 15px 45px rgba(37, 99, 235, .10),
                inset 0 1px 1px rgba(255, 255, 255, .85);

            transition: .35s;
        }

        .card:hover {

            transform:
                translateY(-3px);

            box-shadow:
                0 20px 60px rgba(37, 99, 235, .16);
        }

        /* SIDE GLOW */

        .card::before {

            content: '';

            position: absolute;

            top: 0;
            right: 0;

            width: 5px;
            height: 100%;

            background:
                linear-gradient(180deg,
                    #60a5fa,
                    transparent);
        }

        /* SHINE */

        .card::after {

            content: '';

            position: absolute;

            top: 0;
            left: -120%;

            width: 70%;
            height: 100%;

            background:
                linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, .22),
                    transparent);

            transform: skewX(-25deg);

            transition: 1.2s;
        }

        .card:hover::after {

            left: 130%;
        }

        /* TITLES */

        .section-title {

            display: flex;

            align-items: center;

            gap: 12px;

            margin-bottom: 24px;

            font-size: 24px;

            font-weight: 900;

            color: #1d4ed8;
        }

        .section-title::before {

            content: '';

            width: 12px;
            height: 12px;

            border-radius: 50%;

            background: #60a5fa;

            box-shadow:
                0 0 15px rgba(96, 165, 250, .6);
        }

        /* GRID */

        .grid {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 18px;
        }

        /* ITEMS */

        .item {

            position: relative;

            overflow: hidden;

            padding: 22px;

            border-radius: 24px;

            background:
                rgba(255, 255, 255, .30);

            border:
                1px solid rgba(255, 255, 255, .48);

            transition: .3s;
        }

        .item:hover {

            transform: translateY(-3px);

            background:
                rgba(255, 255, 255, .38);
        }

        .item.full {

            grid-column: 1/-1;
        }

        .label {

            font-size: 13px;

            color: #64748b;

            margin-bottom: 10px;

            font-weight: 700;
        }

        .value {

            font-size: 17px;

            font-weight: 800;
        }

        /* BADGES */

        .badge {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 8px 18px;

            border-radius: 40px;

            font-size: 13px;

            font-weight: 800;
        }

        .badge.yes {

            background:
                rgba(239, 68, 68, .12);

            color: #dc2626;
        }

        .badge.no {

            background:
                rgba(34, 197, 94, .12);

            color: #16a34a;
        }

        .badge.blue {

            background:
                rgba(37, 99, 235, .12);

            color: #2563eb;
        }

        /* TEXT AREA */

        .text-title {

            margin-bottom: 10px;

            font-size: 14px;

            font-weight: 800;

            color: #475569;
        }

        .text-block {

            padding: 20px;

            margin-bottom: 20px;

            border-radius: 22px;

            line-height: 2;

            background:
                rgba(255, 255, 255, .28);

            border:
                1px solid rgba(255, 255, 255, .48);

            backdrop-filter: blur(12px);

            color: #334155;

            font-size: 15px;
        }

        /* FOOTER */

        .footer {

            margin-top: 32px;

            text-align: center;
        }

        .footer-box {

            display: inline-flex;

            align-items: center;

            gap: 12px;

            padding: 18px 28px;

            border-radius: 24px;

            background:
                linear-gradient(145deg,
                    rgba(255, 255, 255, .42),
                    rgba(255, 255, 255, .20));

            border:
                1px solid rgba(255, 255, 255, .55);

            backdrop-filter: blur(22px);

            box-shadow:
                0 12px 35px rgba(37, 99, 235, .10);

            font-weight: 700;

            color: #334155;
        }

        .footer-box::before {

            content: '';

            font-size: 18px;
        }

        .date {

            margin-top: 14px;

            color: #64748b;

            font-size: 13px;

            font-weight: 600;
        }

        /* MOBILE */

        @media(max-width:900px) {

            .header {

                flex-direction: column;

                text-align: center;
            }

            .patient-box {

                width: 100%;
            }

            .stats {

                grid-template-columns: 1fr 1fr;
            }

            .grid {

                grid-template-columns: 1fr;
            }
        }

        @media(max-width:600px) {

            .stats {

                grid-template-columns: 1fr;
            }

            .clinic-name {

                font-size: 28px;
            }

            .patient-name {

                font-size: 22px;
            }

            .card,
            .header {

                padding: 24px;
            }
        }
    </style>

</head>

<body>

    <!-- LIGHTS -->

    <div class="glow one"></div>
    <div class="glow two"></div>
    <div class="glow three"></div>

    <div class="container">

        <!-- HEADER -->

        <div class="header">

            <div class="logo">
                <img src="{{ asset('images/pioneers-clinic-logo.png') }}">
            </div>

            <div class="header-content">

                <div class="clinic-name">
                    Pioneers Clinic
                </div>

                <div class="clinic-sub">
                    نظام السجل الطبي الإلكتروني المتطور
                </div>

            </div>

            <div class="patient-box">



                <div class="patient-label">
                    اسم المريض
                </div>

                <div class="patient-name">
                    {{ $record->patient->name ?? 'غير محدد' }}
                </div>

            </div>

        </div>
        <!-- ================= ADDITIONAL PERSONAL INFO ================= -->

        <div class="card">

            <div class="section-title">
                بيانات المريض الشخصية
            </div>

            <div class="grid">

                <div class="item">

                    <div class="label">الجنس</div>

                    <div class="value">
                        {{ $record->patient->gender ?? 'غير محدد' }}
                    </div>

                </div>

                <div class="item">

                    <div class="label">تاريخ الميلاد</div>

                    <div class="value">
                        {{ optional($record->patient->patient)->date_of_birth ?? 'غير محدد' }}
                    </div>

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

        <!-- STATS -->

        <div class="stats">

            <div class="stat-card">
                <div class="stat-number">
                    {{ $record->blood_type ?? '—' }}
                </div>
                <div class="stat-label">
                    فصيلة الدم
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-number">
                    {{ $record->height ?? '—' }}
                </div>
                <div class="stat-label">
                    الطول / سم
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-number">
                    {{ $record->weight ?? '—' }}
                </div>
                <div class="stat-label">
                    الوزن / كغ
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-number">
                    {{ $record->blood_pressure ?? '—' }}
                </div>
                <div class="stat-label">
                    ضغط الدم
                </div>
            </div>

        </div>
        <!-- ================= CONTACT INFO ================= -->

        <div class="card">

            <div class="section-title">
                معلومات التواصل
            </div>

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
                    <div class="value">
                        {{ optional($record->patient->patient)->country ?? 'غير محدد' }}
                    </div>

                </div>

                <div class="item">

                    <div class="label">المدينة</div>
                    <div class="value">
                        {{ optional($record->patient->patient)->city ?? 'غير محدد' }}
                    </div>

                </div>

                <div class="item full">

                    <div class="label">العنوان الكامل</div>
                    <div class="value">
                        {{ optional($record->patient->patient)->address ??
                            optional($record->patient->patient)->country . ' - ' . optional($record->patient->patient)->city }}
                    </div>

                </div>

                <div class="item full">

                    <div class="label">جهة الاتصال للطوارئ</div>

                    <div class="value">
                        {{ optional($record->patient->patient)->emergency_contact ?? 'غير محدد' }}
                    </div>

                </div>

            </div>

        </div>
        <!-- BASIC INFO -->

        <div class="card">

            <div class="section-title">
                المعلومات الأساسية
            </div>

            <div class="grid">

                <div class="item">

                    <div class="label">
                        التدخين
                    </div>

                    <span class="badge {{ $record->is_smoker ? 'yes' : 'no' }}">
                        {{ $record->is_smoker ? 'مدخن' : 'غير مدخن' }}
                    </span>

                </div>

                <div class="item">

                    <div class="label">
                        الحالة الاجتماعية
                    </div>

                    <div class="value">
                        {{ $record->marital_status ?? 'غير محدد' }}
                    </div>

                </div>

                <div class="item full">

                    <div class="label">
                        عدد الأطفال
                    </div>

                    <span class="badge blue">
                        {{ $record->number_of_children ?? '0' }} أطفال
                    </span>

                </div>

            </div>

        </div>

        <!-- MEDICAL -->

        <div class="card">

            <div class="section-title">
                الأمراض والحساسية
            </div>

            <div class="text-title">
                الأمراض المزمنة
            </div>

            <div class="text-block">
                {{ $record->chronic_diseases ?? 'لا يوجد' }}
            </div>

            <div class="text-title">
                الحساسية
            </div>

            <div class="text-block">
                {{ $record->allergies ?? 'لا يوجد' }}
            </div>

            <div class="text-title">
                الأدوية الحالية
            </div>

            <div class="text-block">
                {{ $record->medications ?? 'لا يوجد' }}
            </div>

        </div>

        <!-- HISTORY -->

        <div class="card">

            <div class="section-title">
                التاريخ الطبي
            </div>

            <div class="text-title">
                العمليات السابقة
            </div>

            <div class="text-block">
                {{ $record->surgeries ?? 'لا يوجد' }}
            </div>

            <div class="text-title">
                التاريخ العائلي
            </div>

            <div class="text-block">
                {{ $record->family_history ?? 'لا يوجد' }}
            </div>

            <div class="text-title">
                ملاحظات
            </div>

            <div class="text-block">
                {{ $record->notes ?? 'لا يوجد' }}
            </div>

        </div>

        <!-- FOOTER -->

        <div class="footer">

            <div class="footer-box">
                هذه البيانات سرية وخاصة بالمريض
            </div>

            <div class="date">
                {{ now()->format('Y-m-d H:i') }}
            </div>

        </div>

    </div>

</body>

</html>
