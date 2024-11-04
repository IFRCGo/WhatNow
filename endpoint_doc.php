<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Endpoints</title>
    <style>
        /* Dark theme styles */
        body {
            background-color: #1f2937;
            color: #f3f4f6;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .card {
            background-color: #374151;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .search-input {
            width: 100%;
            max-width: 400px;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.25rem;
            background-color: #4b5563;
            color: #f3f4f6;
        }

        .search-input::placeholder {
            color: #9ca3af;
        }

        .endpoint-list {
            list-style-type: none;
            padding: 0;
        }

        .endpoint-item {
            border-bottom: 1px solid #4b5563;
            padding: 1rem 0;
        }

        .endpoint-item:last-child {
            border-bottom: none;
        }

        .endpoint-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .endpoint-method {
            font-weight: bold;
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
        }

        .endpoint-method.post {
            background-color: #16a34a;
        }

        .endpoint-method.put {
            background-color: #2563eb;
        }

        .endpoint-method.delete {
            background-color: #dc2626;
        }

        .endpoint-url {
            font-size: 1.1rem;
            color: #9ca3af;
        }

        .endpoint-description {
            margin-top: 0.5rem;
            color: #d1d5db;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="text-center mb-8">API Endpoints</h1>

    <div class="search-container">
        <input type="text" class="search-input" placeholder="Search endpoints..." id="searchInput">
    </div>

    <div class="endpoint-cards">
        <div class="card">
            <div class="card-header">Instructions Endpoints</div>
            <ul class="endpoint-list">
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method post">POST</span>
                            <span class="endpoint-url">/instructions</span>
                        </div>
                        <div class="endpoint-description">Creates a new instruction.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method put">PUT</span>
                            <span class="endpoint-url">/instructions/{id}</span>
                        </div>
                        <div class="endpoint-description">Updates an existing instruction by ID.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method delete">DELETE</span>
                            <span class="endpoint-url">/instructions/{id}</span>
                        </div>
                        <div class="endpoint-description">Deletes an instruction by ID.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method post">POST</span>
                            <span class="endpoint-url">/instructions/{countryCode}/publish</span>
                        </div>
                        <div class="endpoint-description">Publishes translations for a specific country code.</div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">Applications Endpoints</div>
            <ul class="endpoint-list">
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/apps</span>
                        </div>
                        <div class="endpoint-description">Lists all applications.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method post">POST</span>
                            <span class="endpoint-url">/apps</span>
                        </div>
                        <div class="endpoint-description">Creates a new application.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/apps/{id}</span>
                        </div>
                        <div class="endpoint-description">Retrieves an application by ID.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method delete">DELETE</span>
                            <span class="endpoint-url">/apps/{id}</span>
                        </div>
                        <div class="endpoint-description">Deletes an application by ID.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method patch">PATCH</span>
                            <span class="endpoint-url">/apps/{id}</span>
                        </div>
                        <div class="endpoint-description">Updates an application by ID.</div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="card">
            <div class="card-header">History Endpoints</div>
            <ul class="endpoint-list">
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/history</span>
                        </div>
                        <div class="endpoint-description">Lists all history records.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/history/{id}</span>
                        </div>
                        <div class="endpoint-description">Retrieves a history record by ID.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/users/{id}/history</span>
                        </div>
                        <div class="endpoint-description">Lists history records for a specific user by ID.</div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">Import/Export Endpoints</div>
            <ul class="endpoint-list">
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method post">POST</span>
                            <span class="endpoint-url">/import/{countryCode}/{languageCode}</span>
                        </div>
                        <div class="endpoint-description">Imports data for a specific country and language code.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/template/{country_code}/{language_code}</span>
                        </div>
                        <div class="endpoint-description">Exports a template for a specific country and language code.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/template/{country_code}</span>
                        </div>
                        <div class="endpoint-description">Exports a blank template for a specific country code.</div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">Terms Endpoints</div>
            <ul class="endpoint-list">
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/terms</span>
                        </div>
                        <div class="endpoint-description">Lists all terms.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/terms/all</span>
                        </div>
                        <div class="endpoint-description">Lists all terms without pagination.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method post">POST</span>
                            <span class="endpoint-url">/terms</span>
                        </div>
                        <div class="endpoint-description">Creates new terms.</div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">Regions Endpoints</div>
            <ul class="endpoint-list">
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/regions/{country_code}</span>
                        </div>
                        <div class="endpoint-description">Retrieves all regions for a specific organisation by country code.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/regions/{country_code}/{code}</span>
                        </div>
                        <div class="endpoint-description">Retrieves a region for a specific country code and region code.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method post">POST</span>
                            <span class="endpoint-url">/regions</span>
                        </div>
                        <div class="endpoint-description">Creates a new region.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method put">PUT</span>
                            <span class="endpoint-url">/regions/region/{regionId}</span>
                        </div>
                        <div class="endpoint-description">Updates a region by ID.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method delete">DELETE</span>
                            <span class="endpoint-url">/regions/region/{regionId}</span>
                        </div>
                        <div class="endpoint-description">Deletes a region by ID.</div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">Authentication Endpoints</div>
            <ul class="endpoint-list">
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method post">POST</span>
                            <span class="endpoint-url">/login</span>
                        </div>
                        <div class="endpoint-description">Logs in a user.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method post">POST</span>
                            <span class="endpoint-url">/login/{driver}</span>
                        </div>
                        <div class="endpoint-description">Logs in a user using OAuth.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method post">POST</span>
                            <span class="endpoint-url">/register</span>
                        </div>
                        <div class="endpoint-description">Registers a new user.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method post">POST</span>
                            <span class="endpoint-url">/password/email</span>
                        </div>
                        <div class="endpoint-description">Sends a password reset link to the user's email.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method post">POST</span>
                            <span class="endpoint-url">/password/reset</span>
                        </div>
                        <div class="endpoint-description">Resets the user's password.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/confirm/{token}</span>
                        </div>
                        <div class="endpoint-description">Confirms a user's email using a token.</div>
                    </div>
                </li>
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method post">POST</span>
                            <span class="endpoint-url">/confirm/password/set</span>
                        </div>
                        <div class="endpoint-description">Sets a password for a user after email confirmation.</div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">Translation Endpoints</div>
            <ul class="endpoint-list">
                <li class="endpoint-item">
                    <div class="endpoint-info">
                        <div>
                            <span class="endpoint-method get">GET</span>
                            <span class="endpoint-url">/translations/{locale}</span>
                        </div>
                        <div class="endpoint-description">Shows translations for a specific locale.</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
        <!-- Add more endpoint cards for other categories -->

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const endpointItems = document.querySelectorAll('.endpoint-item');

        searchInput.addEventListener('input', () => {
            const searchTerm = searchInput.value.toLowerCase();
            endpointItems.forEach((item) => {
                const endpointInfo = item.querySelector('.endpoint-info').textContent.toLowerCase();
                if (endpointInfo.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</script>
</body>
</html>
