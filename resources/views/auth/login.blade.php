<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ticket & Booking Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9; /* لون خلفية هادئ يناسب لوحات التحكم */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-login {
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        .login-header {
            background: #fff;
            padding-bottom: 0;
        }

        .btn-primary {
            background-color: #1c92d2; /* نفس اللون الرئيسي المستخدم سابقاً */
            border-color: #1c92d2;
            padding: 12px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #157ab0;
            border-color: #157ab0;
        }

        .form-floating label {
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card card-login shadow-lg">
                <div class="card-body p-5 text-center">

                    <div class="mb-4">
                        <img src="https://cdn-icons-png.flaticon.com/512/11860/11860976.png" alt="Logo" width="80" class="mb-3">
                        <h4 class="fw-bold text-dark">Welcome Back!</h4>
                        <p class="text-muted small">Sign in to manage tickets and bookings.</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-floating mb-3 text-start">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required>
                            <label for="email">Email Address</label>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-4 text-start">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                            <label for="password">Password</label>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary rounded-3 shadow-sm">
                                Login to Dashboard
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="#" class="text-decoration-none text-muted small">Forgot password?</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3 text-muted small">
                &copy; {{ date('Y') }} Ticket Manager System
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
