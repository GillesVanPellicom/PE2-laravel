@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Package Inventory per Locatie</h1>

        @foreach ($packagesByLocation as $locationName => $packages)
            <h2>{{ $locationName }}</h2>
            @if($packages->isEmpty())
                <p>Geen packages gevonden voor deze locatie.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Referentie</th>
                            <th>Status</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $package)
                            <tr>
                                <td>{{ $package->id }}</td>
                                <td>{{ $package->reference }}</td>
                                <td>{{ $package->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    </div>
@endsection
