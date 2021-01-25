<!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="./swagger-ui.css">
    <link rel="icon" type="image/png" href="./favicon-32x32.png" sizes="32x32"/>
    <link rel="icon" type="image/png" href="./favicon-16x16.png" sizes="16x16"/>
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }

        *,
        *:before,
        *:after {
            box-sizing: inherit;
        }

        body {
            margin: 0;
            background: #fafafa;
        }

        .topbar {
            display: none;
        }

        code {
            white-space: pre-wrap !important;
        }
    </style>
</head>

<body>
<div id="swagger-ui"></div>

<script src="./swagger-ui-bundle.js"></script>
<script src="./swagger-ui-standalone-preset.js"></script>
<script>
    window.onload = function () {
        // Begin Swagger UI call region
        const ui = SwaggerUIBundle({
            url: "/specification.yaml?v=<?=time()?>",
            dom_id: '#swagger-ui',
            deepLinking: true,
            persistAuthorization: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: "StandaloneLayout"
        });
        // End Swagger UI call region

        window.ui = ui
    }
</script>
</body>
</html>
