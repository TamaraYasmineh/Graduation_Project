<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>السجل الطبي - {{ $record->patient->name }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<link rel="stylesheet"
      href="{{ asset('assets/medical-record/medical-record.css') }}">

      <style>
    body::before {
        background:
            linear-gradient(rgba(255,255,255,.72), rgba(255,255,255,.72)),
            url('{{ asset('images/dna-bg.jpg') }}');
    }
</style>
</head>
<body>

    {{-- الإضاءة الخلفية --}}
    <div class="glow one"></div>
    <div class="glow two"></div>
    <div class="glow three"></div>

    <div class="container">
        @yield('content')
    </div>

    {{-- Modal الصور --}}
    <div id="imageModal" class="image-modal">
        <span class="close-modal">&times;</span>
        <img id="modalImage">
    </div>

    <script src="{{ asset('assets/medical-record/medical-record.js') }}"></script>
</body>
</html>
