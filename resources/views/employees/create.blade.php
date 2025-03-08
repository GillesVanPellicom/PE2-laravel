<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            background-color: #aaa;
        }
        .cent {
            width: 50%; /* or any fixed width */
            margin: 0 auto;
            text-align: center; /* Optional for centering text */
        }
    </style>
</head>
<body>
    <div class="cent">
    <h1>new employee</h1>
    <div>
        <h1><a href="{{ route('employees.index') }}">Home</a></h1>
    </div>
    <div>
        <form method="post" action="{{ route('employees.store_employee') }}">
            @csrf           <!--protection against cross-site request forgery-->
            @method('POST')
            <!-- https://5balloons.info/retain-old-form-data-on-validation-error-in-laravel/ -->
            <div>
                <label for="lastname">Lastname:</label>
                <input type="text" name="lastname" id="lastname"  value="{{ old('lastname') }}">
            </div>

            <div>
                <label for="firstname">Firstname:</label>
                <input type="text" name="firstname" id="firstname"  value="{{ old('firstname') }}">
            </div>

            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email"  value="{{ old('email') }}">
            </div>

            <div>
                <label for="phone">Phone:</label>
                <input type="text" name="phone" id="phone"  value="{{ old('phone') }}">
            </div>

            <div>
                <label for="birth_date">Birth date:</label>
                <input type="date" name="birth_date" id="birth_date"  value="{{ old('birth_date') }}">
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
                <label for="city">City:</label>
                <select name="city" id="city">
                    <option value="-1">Select a city</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
                @error('city')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="street">Street:</label>
                <input type="text" name="street" id="street"  value="{{ old('street') }}">
                @error('street')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="house_number">House number:</label>
                <input type="text" name="house_number" id="house_number" value="{{ old('house_number') }}">
                @error('house_number')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nationality">Nationality:</label>
                <input type="text" name="nationality" id="nationality" value="{{ old('nationality') }}">
            </div>

            <div>
                <label for="leave_balance">Leave balance:</label>
                <input type="text" name="leave_balance" id="leave_balance"  value="{{ old('leave_balance') }}">
            </div>

            <div>
                <button type="submit">Create</button>
            </div>

        </form>

        @if (session('success'))
            <div>
                {{ session('success') }}
            </div>
        @endif

    </div>
</div>
</body>
</html>