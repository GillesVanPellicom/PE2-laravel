<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers</title>
</head>

<body>
    <h1>Customers</h1>
    <p>Your role: {{ Auth::user()->role }}</p>
    <p>Your first name: {{ Auth::user()->first_name }}</p>
    <p>Your last name: {{ Auth::user()->last_name }}</p>
    <p>Your email: {{ Auth::user()->email }}</p>
    <p>Your country: {{ Auth::user()->country }}</p>
    <p>Your postal code: {{ Auth::user()->postal_code }}</p>
    <p>Your city: {{ Auth::user()->city }}</p>
    <p>Your street: {{ Auth::user()->street }}</p>
    <p>Your house number: {{ Auth::user()->house_number }}</p>
    <p>Your phone number: {{ Auth::user()->phone_number }}</p>

    <form action="{{ route('auth.logout') }}" method="POST">
        @csrf
        <button type="submit">Sign Out</button>
    </form>

    <h2>Edit Your Information</h2>
    <form action="{{ route('auth.update') }}" method="POST">
        @csrf
        <p>
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="{{ Auth::user()->first_name }}">
        </p>
        <p>
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="{{ Auth::user()->last_name }}">
        </p>
        <p>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ Auth::user()->email }}">
        </p>
        <p>
            <label for="country">Country:</label>
            <input type="text" id="country" name="country" value="{{ Auth::user()->country }}">
        </p>
        <p>
            <label for="postal_code">Postal Code:</label>
            <input type="text" id="postal_code" name="postal_code" value="{{ Auth::user()->postal_code }}">
        </p>
        <p>
            <label for="city">City:</label>
            <input type="text" id="city" name="city" value="{{ Auth::user()->city }}">
        </p>
        <p>
            <label for="street">Street:</label>
            <input type="text" id="street" name="street" value="{{ Auth::user()->street }}">
        </p>
        <p>
            <label for="house_number">House Number:</label>
            <input type="text" id="house_number" name="house_number" value="{{ Auth::user()->house_number }}">
        </p>
        <p>
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="{{ Auth::user()->phone_number }}">
        </p>
        <button type="submit">Update</button>
    </form>
</body>

</html>