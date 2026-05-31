{{--
    Shared error display partial untuk semua login templates.
    Variabel input (opsional):
      - $errorClass : Tailwind class untuk container error (override per template)
--}}
@php
    $errorClass = $errorClass ?? 'mb-4 rounded-xl bg-red-500/10 border border-red-500/20 px-4 py-3 text-sm text-red-400';
    // $errors di-share via ShareErrorsFromSession middleware (web group). Defensive
    // check supaya partial tetap render kalau dipanggil di luar HTTP request flow.
    $errBag = isset($errors) && is_object($errors) ? $errors : null;
@endphp

@if(session('error'))
<div class="{{ $errorClass }}">
    {{ session('error') }}
</div>
@endif

@if($errBag && $errBag->any())
<div class="{{ $errorClass }}">
    <ul class="list-disc pl-4 space-y-1">
        @foreach($errBag->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif
