<?php
/**
 * Template Name: Coming Soon / Maintenance
 *
 * A standalone template for the password-protected coming soon page.
 */

$is_hebrew = function_exists( 'handandvision_is_hebrew' ) ? handandvision_is_hebrew() : false;
$dir       = $is_hebrew ? 'rtl' : 'ltr';
$lang      = $is_hebrew ? 'he' : 'en';

// Get error from query var if set
$error     = get_query_var( 'hv_maintenance_error' );

?>
<!DOCTYPE html>
<html lang="<?php echo esc_attr( $lang ); ?>" dir="<?php echo esc_attr( $dir ); ?>">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_hebrew ? 'בקרוב - ' : 'Coming Soon - '; bloginfo( 'name' ); ?></title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@200;300;400;500;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/css/hv-unified.css' ); ?>">

    <style>
        :root {
            --hv-maintenance-bg: #F9F5F0; /* Warm Cream */
            --hv-maintenance-text: #2C3E50; /* Petrol */
            --hv-maintenance-accent: #A890F0; /* Lilac */
        }

        body {
            background-color: var(--hv-maintenance-bg);
            color: var(--hv-maintenance-text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            font-family: 'Inter', 'Heebo', sans-serif;
            text-align: center;
        }

        .hv-maintenance-container {
            max-width: 480px;
            width: 100%;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 12px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.8);
            animation: fadeIn 1s ease-out;
        }

        .hv-maintenance-logo {
            margin-bottom: 2rem;
            font-size: 1.5rem;
            font-weight: 300;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--hv-maintenance-text);
            display: inline-block;
        }

        .hv-maintenance-title {
            font-size: 2.5rem;
            font-weight: 400;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .hv-maintenance-text {
            font-size: 1.125rem;
            color: #666;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .hv-pass-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .hv-pass-input {
            width: 100%;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: white;
            font-size: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .hv-pass-input:focus {
            outline: none;
            border-color: var(--hv-maintenance-accent);
            box-shadow: 0 0 0 3px rgba(168, 144, 240, 0.2);
        }

        .hv-pass-submit {
            padding: 1rem;
            background: var(--hv-maintenance-text);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .hv-pass-submit:hover {
            background: black;
            transform: translateY(-1px);
        }

        .hv-error-message {
            color: #e74c3c;
            margin-top: 1rem;
            font-size: 0.9rem;
            animation: shake 0.5s ease-in-out;
        }

        /* Countdown Styles */
        .hv-countdown {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2.5rem;
            width: 100%;
        }

        .hv-countdown-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--hv-maintenance-text);
        }

        .hv-countdown-number {
            font-family: 'Heebo', sans-serif;
            font-size: 2.5rem;
            font-weight: 200;
            line-height: 1;
        }

        .hv-countdown-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            opacity: 0.7;
            margin-top: 0.5rem;
            font-family: 'Inter', sans-serif;
        }

        @media (max-width: 600px) {
            .hv-countdown {
                gap: 1rem;
            }
            .hv-countdown-number {
                font-size: 1.5rem;
            }
            .hv-countdown-label {
                font-size: 0.6rem;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-5px); }
            40% { transform: translateX(5px); }
            60% { transform: translateX(-5px); }
            80% { transform: translateX(5px); }
        }
    </style>
</head>
<body>

    <div class="hv-maintenance-container">

        <div class="hv-maintenance-logo">
            Hand & Vision
        </div>

        <h1 class="hv-maintenance-title">
            <?php echo $is_hebrew ? 'בקרוב...' : 'Coming Soon'; ?>
        </h1>

        <p class="hv-maintenance-text">
            <?php echo $is_hebrew
                ? 'אנחנו בונים משהו חדש ומרגש. האתר יפתח בקרוב.'
                : 'We are curating a new experience. The gallery will open soon.'; ?>
        </p>

        <!-- Countdown Timer -->
        <div id="hv-countdown" class="hv-countdown" dir="ltr">
            <div class="hv-countdown-item">
                <span class="hv-countdown-number" id="days">00</span>
                <span class="hv-countdown-label">Days</span>
            </div>
            <div class="hv-countdown-item">
                <span class="hv-countdown-number" id="hours">00</span>
                <span class="hv-countdown-label">Hours</span>
            </div>
            <div class="hv-countdown-item">
                <span class="hv-countdown-number" id="minutes">00</span>
                <span class="hv-countdown-label">Minutes</span>
            </div>
            <div class="hv-countdown-item">
                <span class="hv-countdown-number" id="seconds">00</span>
                <span class="hv-countdown-label">Seconds</span>
            </div>
        </div>

        <form method="post" class="hv-pass-form">
            <input type="password"
                   name="hv_pass"
                   class="hv-pass-input"
                   placeholder="<?php echo $is_hebrew ? 'סיסמאת גישה' : 'Enter Access Password'; ?>"
                   required
                   autofocus>

            <button type="submit" class="hv-pass-submit">
                <?php echo $is_hebrew ? 'כניסה' : 'Enter'; ?>
            </button>
        </form>

        <?php if ( ! empty( $error ) ) : ?>
            <div class="hv-error-message">
                <?php echo esc_html( $is_hebrew ? 'סיסמא שגויה' : $error ); ?>
            </div>
        <?php endif; ?>

    </div>

    <script>
        // Set the date we're counting down to (14 days from now for demo)
        // In production, set this to a specific timestamp string
        const countDownDate = new Date();
        countDownDate.setDate(countDownDate.getDate() + 14);

        const updateTimer = () => {
            const now = new Date().getTime();
            const distance = countDownDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("days").innerText = days.toString().padStart(2, '0');
            document.getElementById("hours").innerText = hours.toString().padStart(2, '0');
            document.getElementById("minutes").innerText = minutes.toString().padStart(2, '0');
            document.getElementById("seconds").innerText = seconds.toString().padStart(2, '0');

            if (distance < 0) {
                document.getElementById("hv-countdown").innerHTML = "EXPIRED";
            }
        };

        setInterval(updateTimer, 1000);
        updateTimer();
    </script>
</body>
</html>
