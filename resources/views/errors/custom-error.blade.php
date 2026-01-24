<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>{{ $code }} - {{ $title }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            margin: 0;
            overflow-x: hidden;
        }

        .main-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .illustration-col {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 3rem;
        }

        .illustration-container {
            width: 100%;
            max-width: 500px;
            animation: float 6s ease-in-out infinite;
        }

        .content-col {
            padding: 4rem;
        }

        .error-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
            border-radius: 99px;
            font-weight: 700;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -0.03em;
        }

        .description {
            font-size: 1.25rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 3rem;
            max-width: 500px;
        }

        .cta-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        .btn-primary-custom:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4);
            color: white;
        }

        .btn-outline-custom {
            background-color: white;
            color: var(--text-main);
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
        }

        .btn-outline-custom:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            color: var(--text-main);
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
                padding-top: 5rem;
                padding-bottom: 5rem;
            }

            .content-col {
                padding: 2rem;
            }

            .cta-group {
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid main-wrapper">
        <div class="row w-100 align-items-center">

            <div class="col-lg-6 content-col offset-lg-1">
                <span class="error-badge">{{ $code }}</span>
                <h1>{{ $title }}</h1>
                <p class="description">
                    {{ $message }}
                </p>

                <div class="cta-group">
                    <a href="{{ url('/') }}"
                       class="btn-primary-custom">
                        Back to Dashboard
                    </a>
                    <a href="mailto:{{ getSiteEmail() ?? 'support@company.com' }}"
                       class="btn-outline-custom">
                        Contact Support
                    </a>
                </div>
            </div>

            <div class="col-lg-5 illustration-col">
                <div class="illustration-container">
                    @switch($code)
                        @case('404')
                            <svg viewBox="0 0 500 500"
                                 xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="grad404"
                                                    x1="0%"
                                                    y1="0%"
                                                    x2="100%"
                                                    y2="100%">
                                        <stop offset="0%"
                                              style="stop-color:#818cf8;stop-opacity:1" />
                                        <stop offset="100%"
                                              style="stop-color:#4f46e5;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <circle cx="250"
                                        cy="250"
                                        r="200"
                                        fill="#eef2ff" />
                                <circle cx="230"
                                        cy="230"
                                        r="80"
                                        fill="none"
                                        stroke="url(#grad404)"
                                        stroke-width="15" />
                                <line x1="290"
                                      y1="290"
                                      x2="380"
                                      y2="380"
                                      stroke="url(#grad404)"
                                      stroke-width="20"
                                      stroke-linecap="round" />
                                <text x="230"
                                      y="250"
                                      text-anchor="middle"
                                      font-weight="900"
                                      font-size="60"
                                      fill="url(#grad404)">?</text>
                            </svg>
                        @break

                        @case('500')
                            <svg viewBox="0 0 500 500"
                                 xmlns="http://www.w3.org/2000/svg">
                                <circle cx="250"
                                        cy="250"
                                        r="200"
                                        fill="#fff1f2" />
                                <path d="M220 180 L280 180 L290 150 L210 150 Z"
                                      fill="#f43f5e" />
                                <rect x="180"
                                      y="180"
                                      width="140"
                                      height="120"
                                      rx="10"
                                      fill="#fb7185" />
                                <circle cx="220"
                                        cy="210"
                                        r="5"
                                        fill="white" />
                                <circle cx="240"
                                        cy="210"
                                        r="5"
                                        fill="white" />
                                <line x1="200"
                                      y1="250"
                                      x2="300"
                                      y2="250"
                                      stroke="white"
                                      stroke-width="4"
                                      stroke-dasharray="8 4" />
                                <path d="M230 300 L210 350 M270 300 L290 350"
                                      stroke="#f43f5e"
                                      stroke-width="8"
                                      stroke-linecap="round" />
                            </svg>
                        @break

                        @case('503')
                            <svg viewBox="0 0 500 500"
                                 xmlns="http://www.w3.org/2000/svg">
                                <circle cx="250"
                                        cy="250"
                                        r="200"
                                        fill="#fffbeb" />
                                <circle cx="250"
                                        cy="250"
                                        r="85"
                                        fill="none"
                                        stroke="#f59e0b"
                                        stroke-width="15" />
                                <path d="M250 250 V190 M250 250 L300 300"
                                      stroke="#f59e0b"
                                      stroke-width="15"
                                      stroke-linecap="round" />
                                <circle cx="250"
                                        cy="250"
                                        r="15"
                                        fill="#d97706" />
                                <path d="M150 350 L180 320 M320 180 L350 150"
                                      stroke="#fbbf24"
                                      stroke-width="10"
                                      stroke-linecap="round"
                                      opacity="0.6" />
                            </svg>
                        @break

                        @case('419')
                            <svg viewBox="0 0 500 500"
                                 xmlns="http://www.w3.org/2000/svg">
                                <circle cx="250"
                                        cy="250"
                                        r="200"
                                        fill="#faf5ff" />
                                <path d="M190 160 H310 L250 250 L190 160 Z"
                                      fill="#a855f7"
                                      opacity="0.3" />
                                <path d="M190 340 H310 L250 250 L190 340 Z"
                                      fill="#a855f7" />
                                <rect x="180"
                                      y="150"
                                      width="140"
                                      height="15"
                                      rx="7"
                                      fill="#7e22ce" />
                                <rect x="180"
                                      y="335"
                                      width="140"
                                      height="15"
                                      rx="7"
                                      fill="#7e22ce" />
                                <path d="M190 160 Q190 250 250 250 Q310 250 310 160 M190 340 Q190 250 250 250 Q310 250 310 340"
                                      fill="none"
                                      stroke="#7e22ce"
                                      stroke-width="8" />
                            </svg>
                        @break

                        @case('403')
                            <svg viewBox="0 0 500 500"
                                 xmlns="http://www.w3.org/2000/svg">
                                <circle cx="250"
                                        cy="250"
                                        r="200"
                                        fill="#f0fdf4" />
                                <rect x="180"
                                      y="240"
                                      width="140"
                                      height="100"
                                      rx="10"
                                      fill="#10b981" />
                                <path d="M210 240 V190 A40 40 0 0 1 290 190 V240"
                                      fill="none"
                                      stroke="#059669"
                                      stroke-width="15" />
                                <circle cx="250"
                                        cy="290"
                                        r="10"
                                        fill="white" />
                            </svg>
                        @break

                        @default
                            <svg viewBox="0 0 500 500"
                                 xmlns="http://www.w3.org/2000/svg">
                                <circle cx="250"
                                        cy="250"
                                        r="200"
                                        fill="#eef2ff" />
                                <text x="50%"
                                      y="55%"
                                      text-anchor="middle"
                                      font-weight="900"
                                      font-size="120"
                                      fill="#4f46e5"
                                      opacity="0.2">{{ $code }}</text>
                                <rect x="150"
                                      y="200"
                                      width="200"
                                      height="150"
                                      rx="10"
                                      fill="white"
                                      stroke="#cbd5e1"
                                      stroke-width="2" />
                            </svg>
                    @endswitch
                </div>
            </div>

        </div>
    </div>

</body>

</html>
