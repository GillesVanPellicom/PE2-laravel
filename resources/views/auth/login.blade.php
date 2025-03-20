<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Signin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  </head>
  <body class="bg-gray-100">
    
    <main class="flex items-center justify-center min-h-screen">
      <form action="{{ route('auth.authenticate') }}" method="POST" class="bg-white p-8 rounded shadow-md w-full max-w-md">
          @csrf
        <h1 class="text-2xl font-bold mb-6">Please sign in</h1>

        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700">Email address <span class="text-red-500">*</span></label>
          <input type="email" value="{{ old('email') }}" id="email" name="email" placeholder="name@example.com" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          @error('email')
          <div class="text-red-500 text-sm mt-1">
          {{ $message }}
          </div>
          @enderror
        </div>
        
        <div class="mb-4">
          <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
          <input type="password" name="password" id="password" placeholder="Password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          @error('password')
          <div class="text-red-500 text-sm mt-1">
          {{ $message }}
          </div>
          @enderror
        </div>

        <div class="mb-4">
          <label class="inline-flex items-center">
            <input type="checkbox" value="remember-me" class="form-checkbox">
            <span class="ml-2 text-sm text-gray-700">Remember me</span>
          </label>
        </div>
        
        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Sign in</button>
        <a href="{{ route('welcome') }}" class="mt-4 text-indigo-600 hover:text-indigo-900">Return to homepage</a>
      </form>
    </main>
  </body>
</html>