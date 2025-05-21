<x-app-layout>
    @section('title', 'Airport Contracts')
<x-sidebar-airport>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contracts</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-2xl font-bold mb-4">Contracts</h1>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">Airline</th>
                    <th class="py-2 px-4 border">Flight</th>
                    <th class="py-2 px-4 border">Weight Available (kg)</th>
                    <th class="py-2 px-4 border">Price (€)</th>
                    <th class="py-2 px-4 border">Start Date</th>
                    <th class="py-2 px-4 border">End Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contracts as $contract)
                <tr class="border-b">
                    <td class="py-2 px-4 border">{{$contract->airline_id}}</td>
                    <td class="py-2 px-4 border">{{$contract->flight_id}}</td>
                    <td class="py-2 px-4 border">{{$contract->max_capacity}}</td>
                    <td class="py-2 px-4 border">{{$contract->price}}</td>
                    <td class="py-2 px-4 border">{{$contract->start_date}}</td>
                    <td class="py-2 px-4 border">{{$contract->end_date}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
</x-sidebar-airport>
</x-app-layout>
