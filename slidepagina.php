<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RoadMate Slides</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color:rgb(255, 255, 255);
      color: #2c3e50;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      overflow-x: hidden;
    }

    header {
      background-color: #2c3e50;
      width: 100%;
      padding: 1rem 2rem;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo img{
        width: 200px;
        height: auto;
        margin-left: 1rem;
    }

    nav a {
      color: white;
      margin-left: 1.5rem;
      text-decoration: none;
      font-weight: 500;
    }

    .slider-container {
      max-width: 600px;
      margin: 35px auto;
      background-color: #F0F4F8;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .slide {
      display: none;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .slide.active {
      display: flex;
    }

    .slide img {
      width: 200px;
      margin-bottom: 1rem;
    }

    .slide h2 {
      font-size: 1.4rem;
      margin-bottom: 0.5rem;
    }

    .slide p {
      font-size: 1rem;
      color: #34495e;
    }

    .nav-buttons {
      position: relative;
      margin-top: 3rem; /* iets meer naar beneden */
      height: 50px;      /* zodat de container ruimte heeft */
    }

    .nav-buttons img {
      position: absolute;
      top: 0;
      width: 40px;
      cursor: pointer;
    }

    #prev {
      left: 0;
    }

    #next {
      right: 0;
    }


    .indicators {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 1rem;
    }

    .dot {
      width: 12px;
      height: 12px;
      background-color: #ccc;
      border-radius: 50%;
      display: inline-block;
      transition: background-color 0.3s ease, transform 0.3s ease;
      margin-top: -15px;
    }

    .dot.active {
      background-color: #f1c40f;
      transform: scale(1.5);
    }

    .account-btn {
      background-color: #f1c40f;
      color: #2c3e50;
      padding: 8px 18px;
      border-radius: 20px;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .account-btn:hover {
    background-color: #d4ac0d;
    }


    @media (max-width: 768px) {
      header {
        flex-direction: row;  /* fix voor knop op 1 lijn */
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
      }

      .logo {
        width: 200px;
        margin-left: 0;
      }

      .account-btn {
        padding: 8px 14px;
        font-size: 0.9rem;
      }

      .slider-container {
        width: 90%;
        padding: 1.2rem;
      }

      .slide img {
        width: 150px;
      }

      .slide h2 {
        font-size: 1.2rem;
      }

      .slide p {
        font-size: 0.95rem;
      }

      .nav-buttons {
        flex-direction: row;
        gap: 1rem;
        justify-content: center;
      }

      .nav-buttons button {
        padding: 8px 16px;
        font-size: 0.95rem;
        max-width: 130px;
      }

      .indicators {
        gap: 8px;
        margin-top: 1rem;
      }

      .dot {
        width: 10px;
        height: 10px;
      }

      .dot.active {
        transform: scale(1.3);
      }

      a[href="index.php"] {
        font-size: 0.95rem;
      }
    }




  </style>

    <?php include 'klassen/nav.php'; ?>
  <div class="slider-container">
    <div class="slide active">
      <img src="images/verkeersborden.png" alt="Slide 1" />
      <h2>Leer verkeersborden onderweg</h2>
      <p>RoadMate herkent nieuwe verkeersborden en legt je in realtime uit wat ze betekenen.</p>
    </div>
    <div class="slide">
      <img src="images/busy.png" alt="Slide 2" />
      <h2>Waarschuwing bij drukke punten</h2>
      <p>Wanneer je een druk kruispunt nadert, geeft RoadMate uitleg en tips om veilig door te rijden.</p>
    </div>
    <div class="slide">
      <img src="images/update.png" alt="Slide 3" />
      <h2>Altijd up-to-date</h2>
      <p>Schoolstraten, snelheidsbeperkingen of tijdelijke regels – je blijft altijd geïnformeerd.</p>
    </div>

    <div class="nav-buttons">
      <img id="prev" src="images/left-arrow.png" alt="vorige" style="width: 40px; cursor: pointer; display: none;">
      <img id="next" src="images/right-arrow.png" alt="volgende" style="width: 40px; cursor: pointer;">
    </div>


    


    <div class="indicators">
      <span class="dot active"></span>
      <span class="dot"></span>
      <span class="dot"></span>
    </div>

    <a href="index.php" class="account-btn" style="margin-top: 1.5rem;">Start met RoadMate</a>

    
  </div>

<script>
  const slides = document.querySelectorAll('.slide');
  const nextBtn = document.getElementById('next');
  const prevBtn = document.getElementById('prev');
  let current = 0;

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });
    document.querySelectorAll('.dot').forEach((dot, i) => {
      dot.classList.toggle('active', i === index);
    });

    prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
    nextBtn.style.display = index === slides.length - 1 ? 'none' : 'inline-block';
  }

  nextBtn.addEventListener('click', () => {
    if (current < slides.length - 1) {
      current++;
      showSlide(current);
    }
  });

  prevBtn.addEventListener('click', () => {
    if (current > 0) {
      current--;
      showSlide(current);
    }
  });

  // Swipe support (optioneel)
  let startX = 0;
  const slider = document.querySelector('.slider-container');
  slider.addEventListener('touchstart', e => {
    startX = e.touches[0].clientX;
  });
  slider.addEventListener('touchend', e => {
    const endX = e.changedTouches[0].clientX;
    if (endX < startX - 50) nextBtn.click();
    if (endX > startX + 50) prevBtn.click();
  });

  showSlide(current);
</script>

</body>
</html>