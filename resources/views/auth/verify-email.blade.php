<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <main class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold mb-6">Verify Your Email Address</h1>
            @if (session('message'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('message') }}
                </div>
            @endif
            <p class="mb-4 text-sm text-gray-700">Before proceeding, please check your email for a verification link.</p>
            <p class="mb-4 text-sm text-gray-700">If you did not receive the email,</p>
            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Request another verification link</button>
            </form>
        </div>
    </main>
</body>
</html>