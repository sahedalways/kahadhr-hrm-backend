<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>

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

        /* Illustration Side */
        .illustration-col {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 3rem;
        }

        .illustration-container {
            width: 100%;
            max-width: 500px;
            /* Subtly floating animation like Freepik previews */
            animation: float 6s ease-in-out infinite;
        }

        /* Content Side */
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

        /* Buttons */
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

        /* Responsive adjustments */
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

            .description {
                margin-left: auto;
                margin-right: auto;
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
                <span class="error-badge">Error 404</span>
                <h1>Oops! Page not found.</h1>
                <p class="description">
                    {{ $exception->getMessage() ?? "The page you're looking for doesn't exist. It may have been moved, or the link might be broken." }}
                </p>

                <div class="cta-group">
                    <a href="{{ url('/') }}"
                       class="btn-primary-custom">
                        Back to Home
                    </a>
                    <a href="mailto:{{ getSiteEmail() ?? 'support@company.com' }}"
                       class="btn-outline-custom">
                        Contact Support
                    </a>
                </div>
            </div>

            <div class="col-lg-5 illustration-col">
                <div class="illustration-container">
                    <svg viewBox="0 0 500 500"
                         xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="grad1"
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
                        <text x="50%"
                              y="55%"
                              text-anchor="middle"
                              font-family="Plus Jakarta Sans"
                              font-weight="900"
                              font-size="120"
                              fill="url(#grad1)"
                              opacity="0.2">404</text>
                        <path d="M150 350 L350 350 Q360 350 360 340 L360 200 Q360 190 350 190 L150 190 Q140 190 140 200 L140 340 Q140 350 150 350"
                              fill="white"
                              stroke="#cbd5e1"
                              stroke-width="2" />
                        <rect x="170"
                              y="220"
                              width="160"
                              height="10"
                              rx="5"
                              fill="#e2e8f0" />
                        <rect x="170"
                              y="250"
                              width="100"
                              height="10"
                              rx="5"
                              fill="#e2e8f0" />
                        <circle cx="250"
                                cy="270"
                                r="60"
                                fill="none"
                                stroke="url(#grad1)"
                                stroke-width="8"
                                stroke-dasharray="20 10" />
                        <path d="M280 300 L320 340"
                              stroke="url(#grad1)"
                              stroke-width="12"
                              stroke-linecap="round" />
                        <circle cx="210"
                                cy="150"
                                r="15"
                                fill="#f43f5e"
                                opacity="0.8" />
                        <circle cx="290"
                                cy="120"
                                r="10"
                                fill="#10b981"
                                opacity="0.8" />
                    </svg>
                </div>
            </div>

        </div>
    </div>

</body>

</html>
