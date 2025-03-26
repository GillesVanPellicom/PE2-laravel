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
                                    <option value="Cancelled" {{ $flight->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>

                                <div id="delayInput_{{ $flight->id }}" style="display: {{ $flight->status == 'Delayed' ? 'block' : 'none' }};">
                                    <label for="delay_minutes_{{ $flight->id }}">Delay (minutes):</label>
                                    <input type="number" name="delay_minutes" id="delay_minutes_{{ $flight->id }}" value="{{ $flight->delay_minutes }}">
                                </div>

                                <button type="submit">Update Status</button>
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
            const delayMinutes = document.getElementById(`delay_minutes_${id}`);
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
