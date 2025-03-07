<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <title>Courier Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: red;
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 24px;
            font-weight: bold;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            margin-bottom: 20px;
        }
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            font-size: 16px;
            background: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: darkred;
        }
        .navbar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: red;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 10px 0;
            height: 70px;
        }
        .navbar ul {
            display: flex;
            list-style: none;
            width: 100%;
            justify-content: space-around;
            padding: 0;
        }
        .navbar img {
            width: 35px;
        }
        .navbar i {
    font-size: 35px; /* Grootte */
    color: white; /* Kleur */
    padding: 10px;
}
.navbar a:hover i {
    color: black; /* Kleur bij hover */
}

.back-arrow {
    position: absolute;
    left: 20px; /* Afstand van de rand */
    top: 50%;
    transform: translateY(-50%);
    color: white;
    font-size: 24px; 
    text-decoration: none;
}

.back-arrow:hover {
    color: black; /* Hover effect */
}

    </style>
</head>
<body>

<header class="header">
    <a href="{{ route('index.page') }}" class="back-arrow">
        <i class="fas fa-arrow-left"></i>
    </a>
    ShipCompany
</header>



    <h1>Packages List</h1>
    <div id="packageList"></div>

    



    <nav class="navbar">
    <ul>
        <li><a href="{{ route('route.page') }}"><i class="fas fa-map"></i></a></li> <!-- Route Icon -->
        <li><a href="{{ route('packages.page') }}"><i class="fas fa-box"></i></a></li> <!-- Package Icon -->
        <li><a href="{{ route('scan.page') }}"><i class="fas fa-qrcode"></i></a></li> <!-- QR Code Icon -->
    </ul>
</nav>



<script>
    function addToList(package) {
        let list = document.getElementById("packageList");
        let item = document.createElement("div");
        item.textContent = "Package: " + package;
        list.appendChild(item);
    }
</script>

</body>
</html>