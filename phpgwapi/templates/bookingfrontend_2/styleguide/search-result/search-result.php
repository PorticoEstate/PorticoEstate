<section class="container py-5">
  <div class="mb-5">
    <p class="mb-3 text-bold">BILDEGALLERI</p>
    <div class="row g-2 mb-5 justify-content-center">
      <?php
        for($i = 0;$i < 4;$i++) {
          echo '<div class="col-6 col-md-3">
                  <img src="gfx/office.jpg" alt="Stort møterom med bord og stoler" class="rounded-small w-100" />
                </div>';
        }
      ?>
    </div>
  </div>
  <div class="mb-5">
    <p class="mb-3 text-bold">VARSEL</p>
    <div class="search-result__warning w-100 bg-red rounded-large px-8 py-4">
      Ishallen er for tiden under oppussing, det kan derfor oppleves noe støy i perioden 01.01.2022 - 01.09.2022
    </div>
  </div>
  <div class="mb-5">
    <div class="search-result__data w-100">
      <p class="mb-3 text-bold">INFORMASJON</p>
      <h4 class="text-purple text-bolder heading-underline">Kontaktinformasjon</h4>
      <div class="row mb-4">
        <div class="col-12 col-sm-6 col-md-4">
          <p class="text-bold mb-2">Daglig leder</p>
          <ul class="list-unstyled">
            <li>Olav Hansen</li>
            <li>+47 12 34 56 78</li>
            <li>navn@aktivkommune.non</li>
          </ul>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
          <p class="text-bold mb-2">Bookingansvarlig</p>
          <ul class="list-unstyled">
            <li>Sanna Smith</li>
            <li>+47 12 34 56 78</li>
            <li>navn@aktivkommune.non</li>
          </ul>
        </div>
      </div>
      <div class="row gx-5 gy-4 mb-4">
        <div class="col-12 col-sm-6">
          <h4 class="text-purple text-bolder heading-underline">Beskrivelse</h4>
          <p>
            Møterommet er i 3.etasje (ved kampsport matte 1), og er 22,3 m2. Det er bord og stoler.
          </p>
        </div>
        <div class="col-12 col-sm-6">
          <h4 class="text-purple text-bolder heading-underline">Adresse</h4>
          <p>
            Gunnar Warebergs gate 3 </br>
            4009 Stavanger
          </p>
        </div>
      </div>
      <div class="row gx-5 gy-4">
        <div class="col-12 col-sm-6 col-md-4">
          <h4 class="text-purple text-bolder heading-underline">Åpningstider</h4>
          <ul class="list-unstyled">
            <?php
              $weekDays = ['Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag', 'Søndag' ];

              foreach ($weekDays as $weekDay) {
                echo '<li class="opening-hours">
                        <span class="opening-hours-day">'.$weekDay.':</span>
                        <span class="opening-hours-time">00:00-00:00</span>
                      </li>';
              }
            ?>
          </ul>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
          <h4 class="text-purple text-bolder heading-underline">Fasiliteter</h4>
          <ul class="list-unstyled">
            <li>- Prosjektor</li>
            <li>- Høytaleranlegg</li>
            <li>- Discokule</li>
            <li>- Kjøkken</li>
            <li>- Oppvaskmaskin</li>
            <li>- Tallerkner, glass og bestikk</li>
          </ul>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
          <h4 class="text-purple text-bolder heading-underline">Priser</h4>
          <ul class="list-unstyled">
            <li>Pris i timen: 123,-</li>
            <li>Pris for halv dag: 123,-</li>
            <li>Pris for hel dag: 123,-</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>