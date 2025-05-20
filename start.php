<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RoadMate</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      height: 100vh;
      background-color: #f0f4f8;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      font-family: 'Poppins', sans-serif;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .curve-wrapper {
      position: absolute;
      top: 15%;
      left: 0;
      width: 100%;
      height: 400px;
      z-index: -1;
      overflow: visible;
    }

    .road-fill {
      fill: #f1c40f;
      stroke: none;
      animation: fadeInRoad 2s ease-out forwards;
    }

    .road-middle {
      stroke: white;
      stroke-width: 5;
      stroke-dasharray: 40 25;
      fill: none;
      stroke-dashoffset: 1200;
      animation: drawCurve 4s ease-out forwards;
    }

    @keyframes drawCurve {
      to {
        stroke-dashoffset: 0;
      }
    }

    @keyframes fadeInRoad {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    h1 {
      font-size: 2.5rem;
      color: #2c3e50;
      opacity: 0;
      margin-bottom: 50px;
      animation: fadeInText 2s forwards;
    }

    img {
      width: 500px;
      margin-top: 20px;
      opacity: 0;
      animation: fadeInLogo 1s 1s forwards, bounce 3s infinite ease-in-out;
    }

    h3 {
      font-size: 1.5rem;
      color: #34495e;
      margin-top: 150px;
      opacity: 0;
      animation: fadeInText 2s 2s forwards;
    }

    h3:hover {
      cursor: pointer;
      color: #898503;
    }

    @keyframes fadeInText {
      to {
        opacity: 1;
      }
    }

    @keyframes fadeInLogo {
      to {
        opacity: 1;
      }
    }

    @keyframes bounce {
      0%, 100% {
        transform: translateY(0px);
      }
      50% {
        transform: translateY(-15px);
      }
    }


    @media (max-width: 768px) {
      h1 {
        font-size: 1.8rem;
        margin-bottom: 30px;
      }

      h3 {
        font-size: 1.1rem;
        margin-top: 100px;
      }

      img {
        width: 80%;
        max-width: 300px;
      }

      .curve-wrapper {
        top: 35%;
        height: 300px;
      }

      .road-middle {
        stroke-width: 3;
        stroke-dasharray: 30 20;
      }
    }

  </style>
</head>
<body>
  <div class="curve-wrapper">
    <svg class="background-curve" viewBox="0 0 1440 480" preserveAspectRatio="none">
      <!-- Gele weg -->
      <path class="road-fill" d="
        M0,130 
        C240,90 480,170 720,130 
        C960,90 1200,170 1440,130 
        L1440,290 
        C1200,330 960,250 720,290 
        C480,330 240,250 0,290 
        Z" />

      <!-- Witte middenlijn -->
      <path id="roadPath" class="road-middle" d="
        M-100,210 
        C240,170 480,250 720,210 
        C960,170 1200,250 1600,210" />

      <!-- Auto -->
      <g>
        <g>
          <!-- Auto 20px lager dan pad -->
          <g id="car" transform="translate(0, 20)" visibility="hidden">
            <!-- Car body -->
            <rect x="-20" y="-10" width="60" height="30" rx="6" fill="#e74c3c"/>
            <!-- Windows -->
            <rect x="-15" y="-5" width="15" height="10" fill="#ecf0f1" />
            <rect x="5" y="-5" width="15" height="10" fill="#ecf0f1" />
            <!-- Wheels -->
            <circle cx="-15" cy="20" r="6" fill="#2c3e50"/>
            <circle cx="25" cy="20" r="6" fill="#2c3e50"/>
            <!-- Zichtbaar maken bij start -->
            <set attributeName="visibility" to="visible" begin="2s" />
          </g>

          <!-- Beweging langs pad -->
          <animateMotion dur="8s" begin="2s" fill="freeze" rotate="auto">
            <mpath href="#roadPath"/>
          </animateMotion>
        </g>
      </g>
    </svg>
  </div>

  <h1>Welcome to</h1>
  <img src="images/road mate logo.png" alt="RoadMate Logo" />
  <h3>klik op het scherm om verder te gaan</h3>

  <script>
    document.body.addEventListener('click', function() {
      window.location.href = 'slidepagina.php';
    });
  </script>
</body>
</html>
