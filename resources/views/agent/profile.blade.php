@extends('agent.layout')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')

@section('content')

{{-- ================= SUCCESS MESSAGES ================= --}}
@if(session('success_info'))
<div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6">
    {{ session('success_info') }}
</div>
@endif

@if(session('success_password'))
<div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6">
    {{ session('success_password') }}
</div>
@endif

{{-- ================= ERRORS ================= --}}
@if($errors->any())
<div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ================= GRID LAYOUT ================= --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ================= USER INFO FORM ================= --}}
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-xl font-semibold mb-6 flex items-center gap-2">
            <!-- User Icon -->
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Informations personnelles
        </h2>

        <form method="POST"
              action="{{ route('agent.profile.info.update') }}"
              enctype="multipart/form-data"
              class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="text-sm font-medium">Nom</label>
                <input type="text" name="name"
                       class="border rounded-lg p-2 w-full focus:ring focus:ring-green-200"
                       value="{{ old('name', $user->name) }}" required>
            </div>

            <div>
                <label class="text-sm font-medium">Email</label>
                <input type="email" name="email"
                       class="border rounded-lg p-2 w-full focus:ring focus:ring-green-200"
                       value="{{ old('email', $user->email) }}" required>
            </div>

            <div>
                <label class="text-sm font-medium">Téléphone</label>
                <input type="text" name="phone"
                       class="border rounded-lg p-2 w-full focus:ring focus:ring-green-200"
                       value="{{ old('phone', $user->phone) }}">
            </div>

            <div>
                <label class="text-sm font-medium">Région</label>
                <input type="text"
                       class="border rounded-lg p-2 w-full bg-gray-100 text-gray-600"
                       value="{{ $user->region->name ?? 'Non défini' }}" disabled>
            </div>

            <div>
                <label class="text-sm font-medium">Photo</label>
                <input type="file" name="photo"
                       class="border rounded-lg p-2 w-full">

                @if($user->photo)
                    <img src="{{ asset('storage/'.$user->photo) }}"
                         class="mt-3 w-24 h-24 rounded-full object-cover border">
                @endif
            </div>

            <button class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                Mettre à jour les informations
            </button>
        </form>
    </div>

    {{-- ================= PASSWORD FORM ================= --}}
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-xl font-semibold mb-6 flex items-center gap-2">
            <!-- Lock Icon -->
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 11c1.657 0 3 1.343 3 3m-6 0a3 3 0 016 0m-9 4h12a1 1 0 001-1v-5a1 1 0 00-1-1h-1V9a4 4 0 00-8 0v2H6a1 1 0 00-1 1v5a1 1 0 001 1z"/>
            </svg>
            Changer le mot de passe
        </h2>

        <form method="POST"
              action="{{ route('agent.profile.password.update') }}"
              class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="text-sm font-medium">Mot de passe actuel</label>
                <input type="password" name="current_password"
                       class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200" required>
            </div>

            <div>
                <label class="text-sm font-medium">Nouveau mot de passe</label>
                <input type="password" name="password"
                       class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200" required>
            </div>

            <div>
                <label class="text-sm font-medium">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation"
                       class="border rounded-lg p-2 w-full focus:ring focus:ring-blue-200" required>
            </div>

            <button class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Mettre à jour le mot de passe
            </button>
        </form>
    </div>

</div>
@endsection
