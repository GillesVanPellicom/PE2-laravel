<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Signin</title>
  </head>
  <body>
    
    <main>
      <form action="{{ route('auth.authenticate') }}" method="POST">
          @csrf
        <h1>Please sign in</h1>

        <div>
          <label for="email">Email address</label>
          <input type="email" value="{{ old('email') }}" id="email" name="email" placeholder="name@example.com">
          @error('email')
          <div>
          {{ $message }}
          </div>
          @enderror
        </div>
        
        <div>
          <label for="password">Password</label>
          <input type="password" name="password" id="password" placeholder="Password">
          @error('password')
          <div>
          {{ $message }}
          </div>
          @enderror
        </div>

        <div>
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        
        <button type="submit">Sign in</button>
      </form>
      <a href="{{ route('welcome') }}">Return to homepage</a>
    </main>
  </body>
</html>
