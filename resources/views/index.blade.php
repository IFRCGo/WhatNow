<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>{{ config('app.name') }}</title>
  <link rel="icon" type="image/png" href="{{ URL::asset('/favicon/icon-16.png') }}" sizes="16x16">
  <link rel="icon" type="image/png" href="{{ URL::asset('/favicon/icon-32.png') }}" sizes="32x32">
  <link rel="icon" type="image/png" href="{{ URL::asset('/favicon/icon-64.png') }}" sizes="64x64">
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>

<body>
  <div id="app"></div> @include('scripts')
</body>

</html>