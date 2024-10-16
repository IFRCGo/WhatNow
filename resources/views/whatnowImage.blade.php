<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="http://fonts.googleapis.com/css?family=Amiri&display=swap&subset=arabic" rel="stylesheet" type="text/css">

    <style>
    html, body {
      padding: 0px;
      margin: 0px;
      font-family: arial, sans-serif;
      background-color: white;
    }

    body {
      width: 1200px;
      min-height: 630px;
    }

    .rtl {
      font-family: 'Amiri', cursive !important;
      direction: rtl;
    }

    .p-24 {
      padding: 24px;
    }

    .mr-24 {
      margin-right: 24px;
    }

    .ml-24 {
      margin-left: 24px;
    }

    .header-main-title-container {
      display:-webkit-box;  /* wkhtmltoimage workaround */
      display: flex;
      flex-direction: row;
      -webkit-flex-direction: row;
      align-items: center;
      -webkit-flex-align: center;
    }

    .title-text {
      width: 1068px;  /* wkhtmltoimage workaround:
                         body width (1200),
                         minus .p24 padding (48),
                         minus img width (84) */
    }

    .header-society-title-container {
      margin-top: 24px; /* wkhtmltoimage workaround */
      border-top: 2px solid #e7e7e7;
      border-bottom: 2px solid #e7e7e7;
    }

    .header-main-title-container img {
      width: 84px;
      height: 84px;
      -o-object-fit: contain;
      object-fit: contain;
    }

    .header-main-title-container h1 {
      font-size: 30px;
    }
    .header-main-title-container h2 {
      font-size: 24px;
    }

    .header-society-title-container h3 {
      margin: 0;
      font-size: 24px;
      letter-spacing: 1.4px;
    }

    .urgency-pill {
      background-color: #857d7a;
      text-transform: uppercase;
      color: white;
      border-radius: 16px;
      padding: 3px 16px;
      letter-spacing: 4.4px;
    }

    .urgency-pill-mitigation {
      background-color: #006a72;
    }
    .urgency-pill-seasonalForecast {
      background-color: #00bcd6;
      color: black;
    }
    .urgency-pill-watch {
      background-color: #c5d86d;
      color: black;
    }
    .urgency-pill-warning {
      background-color: #ffc200;
      color: black;
    }
    .urgency-pill-immediate {
      background-color: #ee3224;
    }
    .urgency-pill-recover {
      background-color: #5c3160;
    }

    main ol {
      line-height: 1.7;
    }

    .font-sm {
      font-size: 24px;
    }

    .font-md {
      font-size: 28px;
    }

    .font-lg {
      font-size: 34px;
    }

    .relative-right-24 {
      position: relative;
      right: 24px;
    }

    .relative-left-24 {
      position: relative;
      left: 24px;
    }
    </style>
  </head>
  <body class="@if ($rtl) rtl @endif">
    <header>
      <hgroup class="header-main-title-container p-24">
        <img alt="Header Logo" src="{{ $eventIcon }}"/>
        <div class="@if ($rtl) relative-right-24 @else relative-left-24 @endif ">
          <h1 class="title-text">{{ $title }}</h1>
          <h2>
            <span class="urgency-pill urgency-pill-{{ $stageRef }}">
                @switch($stageRef)
                    @case('mitigation')
                    Mitigation
                    @break

                    @case('seasonalForecast')
                    Seasonal Forecast
                    @break

                    @default
                    {{ $stageRef }}
                  @endswitch
          </span>
          </h2>
        </div>
      </hgroup>
      <hgroup class="header-society-title-container p-24">
        <h3>
          {{ $society }}
        </h3>
      </hgroup>
    </header>
    <main class="p-24">
      <section>

        <ol class="
        @if (count($items) <= 4)
          font-lg
        @elseif (count($items) >=7)
          font-sm
        @else
          font-md
        @endif
        @if ($rtl) mr-24 @else ml-24 @endif">
          @foreach ($items as $item)
            <li>
              {{ $item }}
            </li>
          @endforeach
        </ol>
      </section>
    </main>
  </body>
</html>
