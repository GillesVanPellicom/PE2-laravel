<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
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
        <form method="post" action="{{ route('employees.store') }}">
            @csrf           <!--protection against cross-site request forgery-->
            @method('POST')

            <div>
                <label for="name">name</label>
                <input type="text" name="name" id="name" placeholder="lastname">
                @error('name')
                    <div>{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="firstname">firstname</label>
                <input type="text" name="firstname" id="firstname" placeholder="firstname">
                @error('firstname')
                    <div>{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="email">email</label>
                <input type="email" name="email" id="email" placeholder="example@example.com">
                @error('email')
                    <div>{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="birthdate">birthdate</label>
                <input type="date" name="birthdate" id="birthdate" placeholder="date of birth">
                @error('birthdate')
                    <div>{{ $message }}</div>
                @enderror
            </div>

            <!--<div>
                <label for="hire_date">date of hire</label>
                <input type="date" name="hire_date" id="hire_date" placeholder="date of hire">
            </div>-->

            <div>
                <label for="vacation_days">vacation days</label>
                <input type="int" name="vacation_days" id="vacation_days" placeholder="when left empty the default value is 25">
            </div>

            <!--<div>
                @if($errors->any())
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>-->

            <div>
                <button type="submit">create</button>
            </div>

        </form>
    </div>
</div>
</body>
</html>