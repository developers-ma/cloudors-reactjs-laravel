<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
       <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Vite + React</title>
    <script type="module" crossorigin src="/cloudors/dist/assets/index-B0SRNT4X.js"></script>
    <link rel="stylesheet" crossorigin href="/cloudors/dist//assets/index-B9gS5PRV.css">
  </head>
  <body>
    <div id="root"></div>
  <script>
        window.INITIAL_DATA = @json($initialData ?? []);
    </script>
  </body>
</html>
