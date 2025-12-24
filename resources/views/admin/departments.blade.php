@extends('admin.layout')

@section('title', 'Départements & Services')
@section('page-title', 'Départements / Services')

@section('content')

<div class="flex flex-col gap-6">

    <!-- DEPARTMENTS -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Départements</h2>

        <!-- Add/Edit Department -->
        <form method="POST" action="{{ route('admin.departments.store') }}" class="flex gap-2 mb-4">
            @csrf
            <input type="hidden" name="id" value="">
            <input type="text" name="name" placeholder="Nom du département"
                class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
            <button type="submit"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Ajouter</button>
        </form>

        <!-- Departments List -->
        <table class="w-full text-left border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">#</th>
                    <th class="px-4 py-2 border">Nom</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $department)
                <tr>
                    <td class="px-4 py-2 border">{{ $department->id }}</td>
                    <td class="px-4 py-2 border">{{ $department->name }}</td>
                    <td class="px-4 py-2 border flex gap-2">

                        <!-- Inline Edit Form -->
                        <form method="POST" action="{{ route('admin.departments.store') }}" class="flex gap-1">
                            @csrf
                            <input type="hidden" name="id" value="{{ $department->id }}">
                            <input type="text" name="name" value="{{ $department->name }}"
                                class="px-2 py-1 border rounded-lg" required>
                            <button type="submit"
                                class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Modifier</button>
                        </form>

                        <!-- Delete -->
                        <form action="{{ route('admin.departments.destroy', $department->id) }}" method="POST"
                              onsubmit="return confirm('Êtes-vous sûr ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">Supprimer</button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- SERVICES -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Services</h2>

        <!-- Add/Edit Service -->
        <form method="POST" action="{{ route('admin.services.store') }}" class="flex gap-2 mb-4">
            @csrf
            <input type="hidden" name="id" value="">
            <input type="text" name="name" placeholder="Nom du service"
                class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
            <select name="department_id" required
                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Sélectionner un département</option>
                @foreach($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
            <button type="submit"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Ajouter</button>
        </form>

        <!-- Services List -->
        <table class="w-full text-left border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">#</th>
                    <th class="px-4 py-2 border">Nom</th>
                    <th class="px-4 py-2 border">Département</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td class="px-4 py-2 border">{{ $service->id }}</td>
                    <td class="px-4 py-2 border">{{ $service->name }}</td>
                    <td class="px-4 py-2 border">{{ $service->department->name }}</td>
                    <td class="px-4 py-2 border flex gap-2">

                        <!-- Inline Edit Form -->
                        <form method="POST" action="{{ route('admin.services.store') }}" class="flex gap-1">
                            @csrf
                            <input type="hidden" name="id" value="{{ $service->id }}">
                            <input type="text" name="name" value="{{ $service->name }}"
                                class="px-2 py-1 border rounded-lg" required>
                            <select name="department_id" required class="px-2 py-1 border rounded-lg">
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}" 
                                    @if($department->id == $service->department_id) selected @endif>
                                    {{ $department->name }}
                                </option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Modifier</button>
                        </form>

                        <!-- Delete -->
                        <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST"
                              onsubmit="return confirm('Êtes-vous sûr ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">Supprimer</button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection
