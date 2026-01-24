<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Password Set Successfully' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        :root {
            --primary-color: #4f46e5;
            --success-color: #10b981;
            --bg-light: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            margin: 0;
        }

        .main-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .content-col {
            padding: 4rem;
        }

        .success-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.6rem 1.2rem;
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 2rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .success-icon {
            animation: pop 0.6s ease-out forwards;
        }

        @keyframes pop {
            0% {
                transform: scale(0.6);
                opacity: 0;
            }

            70% {
                transform: scale(1.15);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        h1 {
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -0.03em;
        }

        .description {
            font-size: 1.15rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2.5rem;
            max-width: 480px;
        }

        .btn-login {
            background-color: var(--primary-color);
            color: #fff;
            padding: 1rem 2.5rem;
            border-radius: 0.8rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        .btn-login:hover {
            background-color: #4338ca;
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4);
            color: #fff;
        }

        .redirect-text {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 1.2rem;
        }

        .illustration-col {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .vector-container {
            width: 100%;
            max-width: 500px;
            animation: float 6s ease-in-out infinite;
        }

        .bg-circle {
            position: absolute;
            z-index: -1;
            border-radius: 50%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.08), transparent 70%);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        @media (max-width: 991px) {
            .main-wrapper {
                flex-direction: column-reverse;
                text-align: center;
                padding: 4rem 1rem;
            }

            .content-col {
                padding: 2rem;
            }

            .description {
                margin-left: auto;
                margin-right: auto;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .vector-container,
            .success-icon {
                animation: none;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid main-wrapper">
        <div class="row w-100 align-items-center">

            <div class="col-lg-5 content-col offset-lg-1">
                <div class="success-badge">
                    <svg class="success-icon"
                         width="20"
                         height="20"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="3"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Security Update
                </div>

                <h3>{{ $title ?? 'Password set successfully!' }}</h1>

                    <p class="description">
                        {{ $message ?? 'Your account security has been updated. You can now login using your new password.' }}
                    </p>
                    @if ($user_type == 'Employee')
                        <a href="{{ url('/employee-login') }}"
                           class="btn-login">
                            Go to Login
                        </a>
                    @else
                        <a href="{{ route('company.auth.login', ['company' => 'company']) }}"
                           class="btn-login">
                            Go to Login
                        </a>
                    @endif


                    <div class="redirect-text">
                        Redirecting in <strong id="redirect-count">5</strong> seconds…
                    </div>
            </div>

            <div class="col-lg-6 illustration-col">
                <div class="bg-circle"></div>
                <div class="vector-container">
                    <svg viewBox="0 0 500 500"
                         xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="gradSuccess"
                                            x1="0%"
                                            y1="0%"
                                            x2="100%"
                                            y2="100%">
                                <stop offset="0%"
                                      style="stop-color:#10b981" />
                                <stop offset="100%"
                                      style="stop-color:#059669" />
                            </linearGradient>
                        </defs>

                        <rect x="180"
                              y="120"
                              width="140"
                              height="260"
                              rx="20"
                              fill="#fff"
                              stroke="#e2e8f0"
                              stroke-width="4" />
                        <path d="M250 180 L310 210 V260 C310 300 250 330 250 330 C250 330 190 300 190 260 V210 Z"
                              fill="url(#gradSuccess)" />
                        <polyline points="225 255 242 272 275 240"
                                  fill="none"
                                  stroke="#fff"
                                  stroke-width="8"
                                  stroke-linecap="round"
                                  stroke-linejoin="round" />
                    </svg>
                </div>
            </div>

        </div>
    </div>

    <script>
        let seconds = 5;
        const el = document.getElementById('redirect-count');

        const timer = setInterval(() => {
            seconds--;
            el.innerText = seconds;

            if (seconds <= 0) {
                clearInterval(timer);

                @if ($user_type == 'Employee')
                    window.location.href = "{{ url('/employee-login') }}";
                @else
                    window.location.href = "{{ route('company.auth.login', ['company' => 'company']) }}";
                @endif

            }
        }, 1000);
    </script>

</body>

</html>
