<?php
require_once 'klassen/db.php';
require_once 'klassen/Auth.php';

use Klassen\Auth;

// Zorg dat er een sessie actief is voor Auth::isLoggedIn()
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="utf-8" />
  <title>RoadMate Kaart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #ffffff;
      color: #2c3e50;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    header {
      background-color: #2c3e50;
      color: white;
      padding: 0.7rem 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header img { width: 200px; height: auto; }
    .account-btn {
      background-color: #f1c40f;
      color: #2c3e50;
      padding: 8px 18px;
      border-radius: 20px;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }
    .account-btn:hover { background-color: #d4ac0d; }

    main {
      flex: 1;
      display: flex;
      flex-direction: column;
      margin: 0;
      padding: 0;
    }

    h1 { margin-bottom: 1rem; }

    #map-placeholder {
      flex: 1;
      width: 100%;
      height: 100%;
      margin: 0;
      border: none;
      border-radius: 0;
      position: relative;
    }

    #map { width: 100%; height: 100%; }

    #start-route-btn {
      position: absolute;
      bottom: 20px;
      right: 20px;
      z-index: 1000;
      background: #136AEC;
      color: white;
      border: none;
      padding: 10px 14px;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
      display: none;
      box-shadow: 0 2px 6px rgba(0,0,0,0.3);
      pointer-events: auto;
    }
    #start-route-btn.show { display: block; }

    .top-controls, .bottom-panel {
      position: absolute;
      width: 100%;
      display: flex;
      justify-content: space-between;
      pointer-events: none;
    }
    .top-controls { top: 10px; padding: 0 10px; }
    .top-controls .btn {
      background: rgba(255,255,255,0.9);
      border-radius: 50%;
      width: 40px; height: 40px;
      display: flex; align-items: center; justify-content: center;
      pointer-events: auto; margin-left: 5px; font-size: 20px;
    }

    .speed-display {
      position: absolute;
      left: 10px; bottom: 60px;
      background: rgba(255,255,255,0.9);
      border-radius: 4px;
      padding: 5px 8px;
      pointer-events: auto;
      font-weight: bold;
    }

    .bottom-panel {
      bottom: 0;
      flex-direction: column;
      align-items: center;
      padding: 10px;
    }
    .route-info {
      background: rgba(0,0,0,0.7);
      color: #0f0;
      font-size: 24px;
      padding: 5px 10px;
      border-radius: 4px;
      pointer-events: auto;
    }
    .route-summary {
      color: #fff;
      font-size: 14px;
      pointer-events: auto;
    }

    .button-group {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1001;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 1rem;
      pointer-events: auto;
      background: none; /* Geen witte achtergrond */
    }

    .button-group button {
      background-color: rgb(255, 255, 255);
      border: none;
      padding: 6px;
      border-radius: 12px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .button-group button img {
      height: 36px;
      width: auto;
    }
    .button-group button:hover {
      background-color: rgba(0,0,0,0.05);
    }

    .slider-container {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.9rem;
      color: #34495e;
      padding: 4px 6px;
      border-radius: 8px;
      background: rgb(255, 255, 255);
    }
    .slider-container input[type="range"] {
      width: 100px;
    }
    .info-icon {
      font-size: 16px;
      color: #555;
      cursor: pointer;
      margin-left: 4px;
      position: relative;
    }

    .tooltip-box {
      display: none;
      position: absolute;
      bottom: 125%; /* boven het icoon */
      right: 0;
      background-color: #ffffff;
      color: #333;
      padding: 8px 12px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      font-size: 0.85rem;
      width: 200px;
      z-index: 9999;
    }

    .tooltip-box::after {
      content: '';
      position: absolute;
      top: 100%;
      right: 10px;
      border-width: 6px;
      border-style: solid;
      border-color: #ffffff transparent transparent transparent;
    }

    .info-icon:hover .tooltip-box {
      display: block;
    }

    /* === QUIZ POPUP === */
    #quiz-popup-overlay {
      position: fixed;
      left:0; top:0; right:0; bottom:0;
      background: rgba(44, 62, 80, 0.16);
      z-index: 10000;
      display: none;
      justify-content: center;
      align-items: center;
    }
    #quiz-popup {
      background:rgb(255, 255, 255);
      border-radius: 32px;
      box-shadow: 0 8px 40px #0001;
      padding: 36px 24px 32px 24px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center; /* ✅ Zorgt voor verticale centrering */
      min-width: 320px;
      max-width: 90vw;
      text-align: center; /* ✅ Zorgt voor centrering van tekst */
      position: relative;
      animation: quiz-in 0.32s cubic-bezier(.38,1.5,.7,1.01);
    }
    @keyframes quiz-in {
      from { opacity: 0; transform: scale(.8);}
      to   { opacity: 1; transform: scale(1);}
    }
    .quiz-img {
      width: 172px;
      height: 172px;
      margin-bottom: 16px;
      border-radius: 24px;
      object-fit: contain;
      background: #fff;
      display: block;
      margin-left: auto;
      margin-right: auto; /* ✅ Image centreren */
    }

    .quiz-title,
    .quiz-question,
    .quiz-score-title,
    .quiz-score,
    .quiz-feedback {
      text-align: center; /* ✅ Zorgt dat alles mooi gecentreerd staat */
    }

    .quiz-score-img img,
    #quiz-mail-section img {
      display: block;
      margin: 0 auto;         /* ✅ Centreert de afbeelding */
      width: 140px;           /* ✅ Kleinere breedte */
      height: auto;           /* ✅ Houdt verhoudingen correct */
      max-width: 80%;         /* ✅ Past zich aan op mobiel */
    }

    .quiz-answers {
      display: flex;
      flex-direction: column;
      gap: 14px;
      width: 100%;
      align-items: center; /* ✅ Knoppen centreren */
    }

    .quiz-answers button {
      text-align: center;
    }

    .quiz-btn.wide,
    .quiz-btn.sm {
      width: 100%;
      max-width: 300px; /* ✅ Behoudt mooi formaat, maar gecentreerd */
    }


    .quiz-title {
      font-size: 1.45rem;
      text-align: center;
      margin-bottom: 28px;
      margin-top: 8px;
      font-weight: 500;
      color: #222;
      line-height: 1.2;
    }
    .quiz-btn {
      background: #ffd416;
      color: #222;
      border: none;
      outline: none;
      border-radius: 32px;
      font-size: 1.19rem;
      font-weight: 500;
      padding: 13px 54px;
      margin-top: 8px;
      width: 90%;
      cursor: pointer;
      transition: background .15s;
      box-shadow: 0 2px 8px #0002;
      letter-spacing: 0.1px;
    }
    .quiz-btn.secondary {
      background: #ffe780;
      color: #222;
      margin-top: 10px;
    }
    .quiz-btn:active {
      background: #e2ba10;
    }

    
    /* QUIZ LOGIC extra */
    .quiz-question {
      font-size: 1.22rem; margin-bottom: 22px; text-align:center; }
    .quiz-answers { display: flex; flex-direction: column; gap: 14px; width:100%; margin-bottom:14px;}
    .quiz-btn.wide {width:100%;}
    .quiz-btn.sm {font-size:1rem; padding:9px 0;}
    .quiz-feedback { color:#c0392b;font-size:1.03rem;margin-bottom:5px;text-align:center;height:22px;}
    #quiz-next-btn, #quiz-score-next-btn {margin-top:18px;}
    .quiz-score-title {margin: 0 0 8px 0;font-size:1.2rem;text-align:center;}
    .quiz-score {font-size:2.5rem;color:#222;font-weight:600;text-align:center;margin:0 0 15px 0;}
    .quiz-score-img {text-align:center;}


    @media (max-width: 600px) {
      #quiz-popup {
      min-width: 0;
      padding: 16px 16px;
      max-width: 90vw;
      border-radius: 20px;
      }
      .quiz-img {
      width: 100px;
      height: 100px;
      margin-bottom: 12px;
      }
      .quiz-title {
      font-size: 1rem;
      margin-bottom: 20px;
      margin-top: 4px;
      }
      .quiz-question {
      font-size: 1rem;
      margin-bottom: 18px;
      }
      .quiz-btn {
      font-size: 0.95rem;
      padding: 10px 20px;
      width: 100%;
      max-width: 90vw;
      }
      .quiz-score-img img,
      #quiz-mail-section img {
        width: 80px;
        max-width: 60vw;
      }
      .quiz-score {
        font-size: 2rem;
      }
      
      .quiz-score-title {
        font-size: 1rem;
        margin-bottom: 10px;
      }
      .quiz-btn.sm {
        font-size: 0.85rem;
        padding: 8px 0;
      }
      .quiz-btn.wide {
        max-width: 90vw;
      }
      .quiz-btn.secondary {
        width: 90vw;
      }
      .quiz-answers {
      gap: 10px;
      }
      .quiz-feedback {
      font-size: 0.9rem;
      margin-bottom: 8px;
      }
      #quiz-mail-section .quiz-score-title {
      font-size: 1rem;
      line-height: 1.3;
      margin: 12px 0;
      }
      

      /* ✅ HEADER: logo + knop naast elkaar op mobiel */
      header {
        flex-direction: row;        
        justify-content: space-between;
      }

      header img {
        margin-top: 10px;
        margin-bottom: 0px;
        width: 120px;
      }

      .account-btn {
        padding: 4px 12px;
        font-size: 0.7rem;
      }



      .button-group {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1001;
      flex-direction: row;
      justify-content: center;
      flex-wrap: nowrap;
      gap: 0.6rem;
      }


      .slider-container {
      flex-direction: row;
      align-items: center;
      padding: 4px 6px;
      border-radius: 8px;
      }

      .slider-container label {
        font-size: 0.85rem;
      }

      .slider-container input[type="range"] {
        width: 80px;
      }

      /* ✅ Start-knop groter en beter klikbaar */
      #start-route-btn {
        padding: 12px 16px;
        font-size: 1rem;
        bottom: 16px;
        right: 16px;
      }

      /* ✅ Verklein icons rechtsboven */
      .top-controls .btn {
        width: 36px;
        height: 36px;
        font-size: 18px;
      }

      /* ✅ Kleinere tekst bij route-info */
      .route-info {
        font-size: 18px;
        padding: 4px 8px;
      }

      .route-summary {
        font-size: 12px;
      }

      /* ✅ Snelheidsweergave kleiner */
      .speed-display {
        font-size: 0.9rem;
        padding: 4px 6px;
      }

      /* ✅ Popup voor loginherinnering aanpassen */
      #login-reminder-popup > div {
        max-width: 85vw;
        padding: 20px;
      }

      #login-reminder-popup h2 {
        font-size: 1.1rem;
      }

      #login-reminder-popup p {
        font-size: 0.95rem;
      }

      #login-reminder-popup .account-btn {
        padding: 8px 14px;
        font-size: 0.9rem;
      }

      .info-icon {
        font-size: 14px;
      }

      .tooltip-box {
        font-size: 0.8rem;
        width: 180px;
      }


    }



    html, body {
    height: 100%;
    }
    #map {
      width: 100%;
      height: 100%;
    }
  </style>
</head>
<body>

<?php include 'klassen/nav.php'; ?>


<main>

  <div id="map-placeholder">
    <div id="map"></div>
    <div class="top-controls">
      <div style="flex:1;"></div>
      <div style="display:flex;">
        <div class="btn" id="search-btn">🔍</div>
        <div class="btn" id="north-btn">🧭</div>
      </div>
    </div>
    <div class="speed-display" id="speed">0 km/u</div>
    <div class="bottom-panel">
      <div class="route-info" id="time"></div>
      <div class="route-summary" id="summary"></div>
    </div>
  </div>
  <button id="start-route-btn">Route starten</button>
  
  <div class="button-group">
    <button id="btn-start-speech">
      <img src="images/play-button.png" alt="Start RoadMate" style="height: 24px;" />
    </button>

    <div class="slider-container">
      <label for="interval-slider">Interval (min):</label>
      <input type="range" id="interval-slider" min="1" max="20" step="1" value="1" />
      <span id="slider-value">1</span>
      <div class="info-icon" id="interval-info">❓</div>
    </div>

    <button id="btn-stop-speech">
      <img src="images/stop.png" alt="Stop RoadMate" style="height: 24px;" />
    </button>

    
  </div>
</main>

<!-- QUIZ POPUP (alle stappen in één overlay) -->
<div id="quiz-popup-overlay">
  <div id="quiz-popup">
    <!-- START -->
    <div id="quiz-intro-section">
      <img src="images/quiz.png" alt="Quiz" class="quiz-img" />
      <div class="quiz-title">We zullen nu een quiz<br/>houden over uw rit</div>
      <button class="quiz-btn" id="quiz-start-btn">Start</button>
      <button class="quiz-btn secondary" id="quiz-later-btn">Een andere keer</button>
    </div>


    <!-- VRAGEN -->
    <div id="quiz-main-section" style="display:none;">
      <img id="quiz-img" src="images/quiz2.png" alt="Quiz" class="quiz-img" style="margin-bottom: 18px;"/>
      <div id="quiz-question" class="quiz-question"></div>
      <div id="quiz-answers" class="quiz-answers"></div>
      <div id="quiz-feedback" class="quiz-feedback"></div>
      <button id="quiz-next-btn" class="quiz-btn yellow" style="display:none;">Volgende</button>
    </div>
    <!-- SCORE -->
    <div id="quiz-score-section" style="display:none;">
      <div class="quiz-score-img">
        <img src="images/results.png?v=2" alt="Score">
      </div>
      <div class="quiz-score-title">Uw score is:</div>
      <div id="quiz-score" class="quiz-score"></div>
      <button id="quiz-score-next-btn" class="quiz-btn yellow">volgende</button>
    </div>
    <!-- EMAIL -->
    <div id="quiz-mail-section" style="display:none;">
      <div class="quiz-score-img">
        <img src="images/mail.png" alt="E-mail">
      </div>
      <div class="quiz-score-title">Wenst u een overzicht te ontvangen via e-mail van uw resultaten?</div>
      <button class="quiz-btn yellow" id="quiz-mail-yes">Ja</button>
      <button class="quiz-btn yellow" id="quiz-mail-no">Nee</button>
    </div>
  </div>
</div>
    <!-- LOGIN HERINNERING POPUP -->
     <?php if (!Auth::isLoggedIn()): ?>
      <div id="login-reminder-popup" style="display:none; position: fixed; left:0; top:0; right:0; bottom:0; background: rgba(44, 62, 80, 0.3); z-index: 11000; justify-content: center; align-items: center;">
          <div style="background: #f2f6fa; border-radius: 28px; padding: 28px 24px; box-shadow: 0 8px 40px rgba(0,0,0,0.15); max-width: 360px; text-align: center;">
            <h2 style="margin-top:0; font-size: 1.3rem; color:#2c3e50;">Melding</h2>
            <p style="font-size: 1rem; color:#34495e; margin: 1rem 0;">Om een herinnering en/of een overzicht van uw reslutaten te ontvangen, moet u inloggen of een account aanmaken.</p>
            <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 1.5rem;">
              <a href="login.php" class="account-btn">Login</a>
              <button onclick="window.location.href='index.php'" class="account-btn" style="background-color: #ccc; color: #2c3e50;cursor: pointer;">Annuleren</button>
            </div>
          </div>
        </div>
      <?php endif; ?>

<script src="https://unpkg.com/proj4@2.8.0/dist/proj4.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>
<script>
  // RoadMate functionaliteit...
  let nearbySignCodes = [];
  let routingControl = null,
      currentRoute   = null,
      currentPos     = null,
      currentDest    = null,
      firstLoad      = true,
      followRoute    = false,
      rotationAngle  = 0;
  let speakInterval = null;
  const slider = document.getElementById('interval-slider');
  const sliderValueEl = document.getElementById('slider-value');

  // === QUIZ LOGICA ===
  const quizPopup = document.getElementById('quiz-popup-overlay');
  const quizIntro = document.getElementById('quiz-intro-section');
  const quizMain  = document.getElementById('quiz-main-section');
  const quizScoreSection = document.getElementById('quiz-score-section');
  const quizMailSection = document.getElementById('quiz-mail-section');
  const quizLaterBtn = document.getElementById('quiz-later-btn');
  const quizStartBtn = document.getElementById('quiz-start-btn');
  const quizImg = document.getElementById('quiz-img');
  const quizQEl = document.getElementById('quiz-question');
  const quizAEl = document.getElementById('quiz-answers');
  const quizFEl = document.getElementById('quiz-feedback');
  const quizNextBtn = document.getElementById('quiz-next-btn');
  const quizScoreEl = document.getElementById('quiz-score');
  const quizScoreNext = document.getElementById('quiz-score-next-btn');
  const quizMailYes = document.getElementById('quiz-mail-yes');
  const quizMailNo = document.getElementById('quiz-mail-no');

  // Quizvragen
  const quizQuestions = [
    {
      image: "images/quiz-icoon.png",
      question: "Hebben de voetgangers altijd voorrang in een zone 30?",
      type: "binary", // ja/nee
      answers: ["JA", "NEE"],
      correct: 0,
      feedback: "JA"
    },
    {
      image: "images/quiz-icoon.png",
      question: "Hoe wordt je snelheid gemeten bij een trajectcontrole?",
      type: "multi",
      answers: ["Over het hele weg", "Bij de camera zelf"],
      correct: 0,
      feedback: "Over het hele weg"
    },
    {
      image: "images/quiz-icoon.png",
      question: "Wat is de snelheidslimiet in een schoolomgeving?",
      type: "multi4",
      answers: ["10 Km/u", "25 Km/u", "30 Km/u", "15 Km/u"],
      correct: 2,
      feedback: "30Km/u"
    }
  ];
  let quizIndex = 0;
  let quizScore = 0;
  let quizAnswered = false;

  // Popup tonen na Stop RoadMate
  document.getElementById('btn-stop-speech').addEventListener('click', () => {
    clearInterval(speakInterval);
    speakInterval = null;
    setTimeout(() => {
      openQuizIntro();
    }, 400);
  });

  // Quiz: start of "een andere keer"
  
  quizStartBtn.addEventListener('click', () => {
    quizIntro.style.display = 'none';
    quizMain.style.display = '';
    quizIndex = 0; quizScore = 0; quizAnswered = false;
    showQuizQuestion();
  });

  // Toon melding wanneer op "Een andere keer" wordt geklikt
  quizLaterBtn.addEventListener('click', () => {
    <?php if (!Auth::isLoggedIn()): ?>
      document.getElementById('login-reminder-popup').style.display = 'flex';
    <?php else: ?>
      window.location.href = 'index.php';
    <?php endif; ?>
  });


  // Sluit herinnering
  function closeReminder() {
    document.getElementById('login-reminder-popup').style.display = 'none';
  }


  // Quiz-vraag tonen
  function showQuizQuestion() {
    quizMain.style.display = '';
    quizScoreSection.style.display = 'none';
    quizMailSection.style.display = 'none';
    const q = quizQuestions[quizIndex];
    // quizImg.src = q.image;
    quizQEl.textContent = q.question;
    quizAEl.innerHTML = '';
    quizFEl.textContent = '';
    quizNextBtn.style.display = 'none';
    quizAnswered = false;

    if(q.type === 'binary' || q.type === 'multi') {
      q.answers.forEach((ans, i) => {
        const btn = document.createElement('button');
        btn.className = 'quiz-btn wide';
        btn.textContent = ans;
        btn.onclick = () => answerQuizQuestion(i, btn);
        quizAEl.appendChild(btn);
      });
    } else if(q.type === 'multi4') {
      const grid = document.createElement('div');
      grid.style.display = 'grid';
      grid.style.gridTemplateColumns = '1fr 1fr';
      grid.style.gap = '10px';
      q.answers.forEach((ans, i) => {
        const btn = document.createElement('button');
        btn.className = 'quiz-btn sm';
        btn.textContent = ans;
        btn.onclick = () => answerQuizQuestion(i, btn);
        grid.appendChild(btn);
      });
      quizAEl.appendChild(grid);
    }
  }

  function answerQuizQuestion(i, btn) {
    if(quizAnswered) return;
    quizAnswered = true;
    Array.from(quizAEl.querySelectorAll('button')).forEach(b => b.disabled = true);
    const q = quizQuestions[quizIndex];
    if(i === q.correct) {
      quizScore++;
      btn.classList.add('selected');
      quizFEl.style.color = '#27ae60';
      quizFEl.textContent = 'Goed!';
    } else {
      quizFEl.style.color = '#c0392b';
      quizFEl.textContent = 'Het antwoord is ' + (q.type==='multi4'||q.type==='multi'? "'" : '') + q.feedback + (q.type==='multi4'||q.type==='multi'? "'" : '');
      btn.classList.add('selected');
      const btns = quizAEl.querySelectorAll('button');
      if(btns[q.correct]) { btns[q.correct].style.background='#27ae60'; btns[q.correct].style.color='#fff'; }
    }
    quizNextBtn.style.display = '';
  }

  quizNextBtn.onclick = function() {
    quizIndex++;
    if(quizIndex < quizQuestions.length) {
      showQuizQuestion();
    } else {
      showQuizScore();
    }
  }
  function showQuizScore() {
    quizMain.style.display = 'none';
    quizScoreSection.style.display = '';
    quizMailSection.style.display = 'none';
    quizScoreEl.textContent = quizScore + "/" + quizQuestions.length;
  }
  quizScoreNext.onclick = function() {
    // ✅ Resultaat opslaan in database
    fetch('save_resultaat.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'score=' + encodeURIComponent(quizScore) + '&totaal=' + encodeURIComponent(quizQuestions.length)
    })
    .then(res => res.text())
    .then(data => console.log("Score opgeslagen:", data))
    .catch(err => console.error("Fout bij opslaan:", err));

    quizScoreSection.style.display = 'none';
    quizMailSection.style.display = '';
  };
  quizMailYes.onclick = function() {
    <?php if (!Auth::isLoggedIn()): ?>
      // alert("Log eerst in om het resultaat via e-mail te ontvangen.");
      document.getElementById('login-reminder-popup').style.display = 'flex';
    <?php else: ?>
      alert("Het resultaat zal via e-mail verzonden worden."); // hier kan je nog AJAX toevoegen indien je het echt wil mailen
      quizPopup.style.display = 'none';
      quizIntro.style.display = '';
      quizMain.style.display = 'none';
      quizScoreSection.style.display = 'none';
      quizMailSection.style.display = 'none';
    <?php endif; ?>
  };

  quizMailNo.onclick = function() {
    quizPopup.style.display = 'none';
    quizIntro.style.display = '';
    quizMain.style.display = 'none';
    quizScoreSection.style.display = 'none';
    quizMailSection.style.display = 'none';
  };

  function openQuizIntro() {
    quizPopup.style.display = 'flex';
    quizIntro.style.display = '';
    quizMain.style.display = 'none';
    quizScoreSection.style.display = 'none';
    quizMailSection.style.display = 'none';
  }
  // === EINDE QUIZ LOGICA ===

  // RoadMate kaart init, event handlers, enz...
  slider.addEventListener('input', () => {
    sliderValueEl.textContent = slider.value;
    if (speakInterval) {
      clearInterval(speakInterval);
      speakInterval = setInterval(speakRandomSign, slider.value * 60000);
    }
  });
  document.getElementById('btn-start-speech').addEventListener('click', () => {
    if (speakInterval) return;
    speakRandomSign();
    speakInterval = setInterval(
      speakRandomSign,
      slider.value * 60000
    );
  });

  function speakRandomSign() {
    if (!nearbySignCodes.length) return;
    const code = nearbySignCodes[
      Math.floor(Math.random() * nearbySignCodes.length)
    ];
    if ('speechSynthesis' in window) {
      const utter = new SpeechSynthesisUtterance(`Bordcode ${code}`);
      utter.lang = 'nl-NL';
      speechSynthesis.speak(utter);
    }
  }

  const map = L.map('map').setView([0,0], 1);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom:19, attribution:'&copy; OpenStreetMap'
  }).addTo(map);
  proj4.defs("EPSG:4326","+proj=longlat +datum=WGS84 +no_defs");
  proj4.defs("EPSG:31370",
    "+proj=lcc +lat_0=90 +lon_0=4.367486666666667 "+
    "+lat_1=51.1666672333333 +lat_2=49.8333339 "+
    "+x_0=150000.013 +y_0=5400088.438 "+
    "+ellps=intl +towgs84=-106.8686,52.2978,-103.7239,0.3366,"+
    "-0.457,1.8422,-1.2747 +units=m +no_defs"
  );

  const mapPane       = map.getPane('mapPane');
  const userMarker    = L.circleMarker([0,0], { radius:6, fillColor:'#136AEC', color:'#fff', weight:2 }).addTo(map);
  const accuracyCircle= L.circle([0,0], { color:'#136AEC', fillColor:'#136AEC', fillOpacity:0.15 }).addTo(map);
  const bordenGroup   = L.layerGroup().addTo(map);

  const startBtn  = document.getElementById('start-route-btn');
  const searchBtn = document.getElementById('search-btn');
  const northBtn  = document.getElementById('north-btn');
  const speedEl   = document.getElementById('speed');
  const timeEl    = document.getElementById('time');
  const summaryEl = document.getElementById('summary');

  const geocoder = L.Control.geocoder({
    collapsed:false,
    placeholder:'Voer bestemming in…',
    defaultMarkGeocode:false,
    geocoder:L.Control.Geocoder.nominatim()
  }).addTo(map);

  geocoder.on('markgeocode', e => {
    currentDest = e.geocode.center;
    bordenGroup.clearLayers();
    if (routingControl) map.removeControl(routingControl);

    routingControl = L.Routing.control({
      waypoints: [currentPos, currentDest],
      router: L.Routing.osrmv1({ serviceUrl:'https://router.project-osrm.org/route/v1' }),
      showAlternatives:false,
      fitSelectedRoutes:false,
      routeWhileDragging:false,
      draggableWaypoints:false,
      addWaypoints:false,
      collapsible:true,
      createMarker:()=>null,
      lineOptions:{styles:[{weight:4,color:'#136AEC'}]}
    }).addTo(map);

    routingControl.once('routesfound', evt => {
      currentRoute = evt.routes[0];
      plotSignsAlongRouteDynamic();
      startBtn.classList.add('show');
      const mins = Math.round(currentRoute.summary.totalTime/60);
      const km   = (currentRoute.summary.totalDistance/1000).toFixed(0);
      timeEl.textContent = `${mins} min`;
      const eta = new Date(Date.now() + currentRoute.summary.totalTime*1000);
      summaryEl.textContent = `${km} km · ${eta.getHours()}:${String(eta.getMinutes()).padStart(2,'0')}`;
    });
  });

  searchBtn.addEventListener('click', () => geocoder._toggle());
  northBtn.addEventListener('click', () => {
    rotationAngle = (rotationAngle + 90) % 360;
    mapPane.style.transform = `rotate(${rotationAngle}deg)`;
    mapPane.style.transformOrigin = '50% 50%';
  });
  startBtn.addEventListener('click', () => {
    if (!currentPos) return;
    followRoute = true;
    map.setView(currentPos, 19);
    startBtn.classList.remove('show');
  });

  navigator.geolocation.watchPosition(pos => {
    const { latitude:lat, longitude:lng, accuracy:acc, speed } = pos.coords;
    currentPos = L.latLng(lat, lng);
    userMarker.setLatLng(currentPos);
    accuracyCircle.setLatLng(currentPos).setRadius(acc);
    speedEl.textContent = `${speed!=null?Math.round(speed*3.6):0} km/u`;

    if (firstLoad) {
      map.setView(currentPos, 15);
      firstLoad = false;
    }
    if (routingControl && currentRoute) {
      plotSignsAlongRouteDynamic();
    } else {
      loadAndPlotSigns(currentPos, 300);
    }
    if (followRoute) {
      map.setView(currentPos, map.getZoom());
      followRoute = false;
    }
  }, console.warn, { enableHighAccuracy:true, timeout:10000, maximumAge:0 });

  function loadAndPlotSigns(latlng, radius) {
    nearbySignCodes = [];
    const [ux, uy] = proj4("EPSG:4326","EPSG:31370",[latlng.lng, latlng.lat]);
    const url =
      `https://opendata.apps.mow.vlaanderen.be/opendata-geoserver/awv/wfs?` +
      `service=WFS&version=1.1.0&request=GetFeature&` +
      `typeName=awv:Verkeersborden.Vlaanderen_Borden&` +
      `srsName=EPSG:31370&outputFormat=application/json&` +
      `bbox=${ux-radius},${uy-radius},${ux+radius},${uy+radius},EPSG:31370`;
    fetch(url)
      .then(r => r.json())
      .then(data => {
        bordenGroup.clearLayers();
        data.features.forEach((f,i) => {
          const code = f.properties.bordcode;
          const date = f.properties.datum_plaatsing || 'Onbekend';
          if (code) nearbySignCodes.push(code);
          const [x,y] = f.geometry.coordinates;
          const [lng2,lat2] = proj4("EPSG:31370","EPSG:4326",[x,y]);
          if (map.distance(latlng,[lat2, lng2]) <= radius) {
            L.marker([lat2, lng2])
              .bindPopup(`<b>Bord ${i+1}</b><br>Code: ${code||'–'}<br>Gemaakt: ${date}`)
              .addTo(bordenGroup);
          }
        });
      })
      .catch(console.error);
  }

  function plotSignsAlongRouteDynamic() {
    if (!currentRoute || !currentPos) return;
    bordenGroup.clearLayers();
    const coords = currentRoute.coordinates.map(c => [c.lng, c.lat]);
    const line   = turf.lineString(coords);
    const snapped= turf.nearestPointOnLine(line, turf.point([currentPos.lng, currentPos.lat]), {units:'meters'});  
    let cumDist = 0;  
    const slice = [[snapped.geometry.coordinates[0], snapped.geometry.coordinates[1]]];  
    for (let i = snapped.properties.index+1; i < coords.length; i++) {  
      const prev = turf.point(slice[slice.length-1]);  
      const curr = turf.point(coords[i]);  
      const d    = turf.distance(prev, curr, {units:'meters'});  
      cumDist += d;  
      if (cumDist > 1000) break;  
      slice.push(coords[i]);  
    }  
    const subLine = turf.lineString(slice);  
    const [minX,minY,maxX,maxY] = turf.bbox(subLine);  
    const buf = 30;  
    const [minX0,minY0] = proj4("EPSG:4326","EPSG:31370",[minX, minY]);  
    const [maxX0,maxY0] = proj4("EPSG:4326","EPSG:31370",[maxX, maxY]);  
    const url =  
      `https://opendata.apps.mow.vlaanderen.be/opendata-geoserver/awv/wfs?` +  
      `service=WFS&version=1.1.0&request=GetFeature&` +  
      `typeName=awv:Verkeersborden.Vlaanderen_Borden&` +  
      `srsName=EPSG:31370&outputFormat=application/json&` +  
      `bbox=${minX0-buf},${minY0-buf},${maxX0+buf},${maxY0+buf},EPSG:31370`;  
    fetch(url)  
      .then(r => r.json())  
      .then(data => {  
        data.features.forEach((f,i) => {  
          const [x,y] = f.geometry.coordinates;  
          const [lng3,lat3] = proj4("EPSG:31370","EPSG:4326",[x,y]);  
          const dLine = turf.pointToLineDistance(turf.point([lng3, lat3]), subLine, {units:'meters'});  
          if (dLine <= 30) {  
            const code = f.properties.bordcode || '–';  
            const date = f.properties.datum_plaatsing || 'Onbekend';  
            L.marker([lat3, lng3])  
              .bindPopup(  
                `<b>Bord ${i+1}</b><br>`+  
                `Code: ${code}<br>`+  
                `Gemaakt: ${date}<br>`+  
                `Afstand: ${dLine.toFixed(0)} m`  
              )  
              .addTo(bordenGroup);  
          }  
        });  
      })  
      .catch(console.error);  
  }
    const intervalInfo = document.getElementById('interval-info');
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip-box';
    tooltip.innerText = 'Stel hier in om de hoeveel minuten je een verkeersweetje wil horen.';
    intervalInfo.appendChild(tooltip);

    
</script>
</body>
</html>