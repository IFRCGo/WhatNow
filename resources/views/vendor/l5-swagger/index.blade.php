<html>
<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.0.1/swagger-ui.css" rel="stylesheet" />
</head>
<body>
    <div id="swagger-ui"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.0.1/swagger-ui-bundle.js"></script>
    <script>
        const ui = SwaggerUIBundle({
            url: "{{ asset('api-docs/api-docs.json') }}",
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIBundle.presets.classic
            ],
            layout: "BaseLayout"
        });
    </script>
</body>
</html>
