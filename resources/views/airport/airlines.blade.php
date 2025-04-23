@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Airlines</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Flight Number</th>
                    <th>Airline</th>
                    <th>Actions</th>
                    <th>From</th>
                    <th>To</th>
                </tr>
            </thead>
            <tbody>
                @foreach($flights as $flight)
                    <tr>
                        <td>{{ $flight->id }}</td>
                        <td>{{ $flight->status }}</td>
                        <td>
                            <form method="POST" action="{{ route('flights.updateStatus', $flight->id) }}">
                                @csrf
                                @method('PATCH')
                                <label for="status">Status:</label>
                                <select name="status" id="status_{{ $flight->id }}" onchange="toggleDelayInput(this.value, {{ $flight->id }})">
                                    <option value="On time" {{ $flight->status == 'On time' ? 'selected' : '' }}>On time</option>
                                    <option value="Delayed" {{ $flight->status == 'Delayed' ? 'selected' : '' }}>Delayed</option>
                                    <option value="Canceled" {{ $flight->status == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                                </select>

                                <div id="delayInput_{{ $flight->id }}" style="display: {{ $flight->status == 'Delayed' ? 'block' : 'none' }};">
                                    <label for="delay_minutes_{{ $flight->id }}">Delay (minutes):</label>
                                    <input type="number" name="delayed_minutes" id="delayed_minutes_{{ $flight->id }}" value="{{ $flight->delayed_minutes }}" inputmode="numeric" pattern="\d*">
                                </div>

                                <button type="submit">Update Status</button>
                            </form>
                        </td>
                        <td>{{ $flight->departureAirport->name ?? 'Unknown' }}</td>
                        <td>{{ $flight->arrivalAirport->name ?? 'Unknown' }}</td>
                        <td>
                            <form method="POST" action="{{ route('flightContracts.updateEndDate', $flight->id) }}">
                                @csrf
                                @method('PATCH')
                                <label for="end_date_{{ $flight->id }}">End Date:</label>
                                <input type="date" name="end_date" id="end_date_{{ $flight->id }}" value="{{ $flight->contract->end_date ?? '' }}">
                                <button type="submit">Set End Date</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        function toggleDelayInput(status, id) {
            const delayInput = document.getElementById(`delayInput_${id}`);
            const delayMinutes = document.getElementById(`delayed_minutes_${id}`);
            if (status === 'Delayed') {
                delayInput.style.display = 'block';
                delayMinutes.required = true;
            } else {
                delayInput.style.display = 'none';
                delayMinutes.required = false;
            }
        }
    </script>
@endsection
