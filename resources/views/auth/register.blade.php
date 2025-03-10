<form action="{{ route('auth.store') }}" method="POST">
    @csrf
    <h1>Create an Account</h1>
  
    <div>
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
        @error('first_name')
            <div>{{ $message }}</div>
        @enderror
    </div>
  
    <div>
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
        @error('last_name')
            <div>{{ $message }}</div>
        @enderror
    </div>
  
    <div>
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        @error('email')
            <div>{{ $message }}</div>
        @enderror
    </div>
  
    <div>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        @error('password')
            <div>{{ $message }}</div>
        @enderror
    </div>
  
    <div>
        <label for="confirm-password">Confirm Password</label>
        <input type="password" id="confirm-password" name="confirm-password" required>
        @error('confirm-password')
            <div>{{ $message }}</div>
        @enderror
    </div>
  
    <div>
        <label for="country">Country:</label>
        <select name="country" id="country">
            <option value="-1">Select a country</option>
        @foreach($countries as $country)
            <option value="{{ $country->id }}">{{ $country->country_name }}</option>
        @endforeach
        </select>
    </div>  

    <div>
        <label for="phone_number">Phone Number</label>
        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
        @error('phone_number')
            <div>{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="birth_date">Birth Date</label>
        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
        @error('birth_date')
            <div>{{ $message }}</div>
        @enderror
    </div>
  
    <div>
        <label for="postal_code">Postal Code</label>
        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
        @error('postal_code')
            <div>{{ $message }}</div>
        @enderror
    </div>
  
    <div>
        <label for="city">City</label>
        <input type="text" id="city" name="city" value="{{ old('city') }}">
        @error('city')
            <div>{{ $message }}</div>
        @enderror
    </div>
  
    <div>
        <label for="street">Street</label>
        <input type="text" id="street" name="street" value="{{ old('street') }}">
        @error('street')
            <div>{{ $message }}</div>
        @enderror
    </div>
  
    <div>
        <label for="house_number">House Number</label>
        <input type="text" id="house_number" name="house_number" value="{{ old('house_number') }}">
        @error('house_number')
            <div>{{ $message }}</div>
        @enderror
    </div>
  
    <button type="submit">Register</button>
  </form>
  <a href="{{ route('welcome') }}">Return to homepage</a>