@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Package Inventory per Locatie</h1>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('package.list') }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label>Status:</label>
                    <select name="status" class="form-control">
                        <option value="">Alle</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Datum:</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                </div>
                <div class="col-md-4 mt-4">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        @foreach ($packagesByLocation as $locationDescription => $packages)
            <h2 class="mt-4">{{ $locationDescription }}</h2>
            @if($packages->isEmpty())
                <p class="text-muted">Geen pakketten gevonden voor deze locatie.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Referentie</th>
                            <th>Status</th>
                            <th>Locatie</th>
                            <th>Aangemaakt op</th>
                            <th>Laatste Update</th>
                            <th>Verwachte Leverdatum</th>
                            <th>Dagen op locatie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $package)
                            @php
                                $daysAtLocation = round($package->updated_at->diffInDays(now()));

                                // Bepaal de klasse voor de status van het pakket
                                $statusClass = match($package->status) {
                                    'Pending' => 'table-warning',
                                    'Delivered' => 'table-success',
                                    'In Transit' => 'table-primary',
                                    default => ''
                                };
                            @endphp
                            <tr class="{{ $daysAtLocation > 3 ? 'table-danger' : $statusClass }}">
                                <td>{{ $package->id }}</td>
                                <td>{{ $package->reference }}</td>
                                <td>{{ $package->status }}</td>
                                <td>{{ $locationDescription }}</td>
                                <td>{{ $package->created_at->format('d-m-Y') }}</td>
                                <td>{{ $package->updated_at->format('d-m-Y') }}</td>
                                <td>{{ $package->expected_delivery ? $package->expected_delivery->format('d-m-Y') : 'N.V.T.' }}</td>
                                <td>{{ $daysAtLocation }} dagen</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    </div>
@endsection
