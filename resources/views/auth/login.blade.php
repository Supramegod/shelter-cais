<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CAIS Shelter</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/icons/favicon-shelter.png') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        /* Kiri */
        .left {
            flex: 1;
            background: linear-gradient(rgba(0, 54, 128, 0.75), rgba(0, 54, 128, 0.75)),
                url('https://images.unsplash.com/photo-1593642532973-d31b6557fa68?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80') no-repeat center center/cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 2rem;
        }

        .left h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            font-weight: bold;
        }

        .left p {
            font-size: 1rem;
            max-width: 400px;
            line-height: 1.5;
        }

        /* Kanan */
        .right {
            flex: 0 0 500px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f5f7fa;
            text-align: center;
        }

        .login-card img {
            max-width: 200px;
            margin-bottom: 1rem;
        }

        .login-card h2 {
            margin-bottom: 0.5rem;
            color: #003680;
        }

        .login-card p {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 1.5rem;
        }

        .input-group {
            margin-bottom: 1rem;
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 0.75rem 0.75rem 0.75rem 2.5rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            box-sizing: border-box;
        }

        .input-group input:focus {
            border-color: #003680;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 54, 128, 0.1);
        }

        .input-group i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .remember {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .remember input {
            margin-right: 0.4rem;
        }

        .remember a {
            text-decoration: none;
            color: #0056d2;
        }

        .remember a:hover {
            text-decoration: underline;
        }

        .login-card button {
            width: 100%;
            padding: 0.75rem;
            background: #0056d2;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-card button:hover {
            background: #003ea8;
        }

        .footer {
            font-size: 0.8rem;
            color: #999;
            margin-top: 1.5rem;
        }

        @media (max-width: 768px) {
            .left {
                display: none;
            }

            .right {
                flex: 1;
            }
        }

        .invalid-feedback {
            color: #e53935;
            font-size: 0.85rem;
            margin-top: -1rem;
            text-align: left;
            padding-left: 2.5rem;
            margin-bottom: 0.5rem;
            display: block;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Kiri -->
        <div class="left">
            <h1>Selamat Datang di CAIS</h1>
            <h1><i>Customer Activity And Information System</i></h1>
            <p>Kelola data pelanggan dan aktivitas Anda dengan lebih mudah, cepat, dan efisien.</p>
        </div>

        <!-- Kanan -->
        <div class="right">
            <div class="login-card">
                <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 1rem;">
                    <img src="{{ asset('assets/img/icons/icon-shelter.png')}}" alt="Shelter Logo">
                </div>
                <p>Silakan login untuk melanjutkan ke sistem.</p>
                <form action="{{route('authenticate')}}" method="POST">
                    @csrf
                    <div class="input-group">
                        <i class="mdi mdi-account"></i>
                        <input type="text" name="username"
                            class="form-control @if ($errors->any()) @if($errors->has('username')) is-invalid @else @endif @endif"
                            placeholder="Username" required>
                    </div>
                    @if($errors->has('username'))
                        <div class="invalid-feedback">{{ $errors->first('username') }}</div>
                    @endif
                    <div class="input-group">
                        <i class="mdi mdi-lock"></i>
                        <input type="password" name="password"
                            class="form-control @if ($errors->any()) @if($errors->has('password')) is-invalid @else @endif @endif"
                            placeholder="Password" required>
                    </div>
                    @if($errors->has('password'))
                        <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                    @endif
                    <div class="remember">
                        <label><input type="checkbox"> Ingat saya</label>
                        <a href="#">Lupa password?</a>
                    </div>
                    <button type="submit">Log in</button>
                </form>
                <div class="footer">
                    © 2025 Shelter All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>

</html>