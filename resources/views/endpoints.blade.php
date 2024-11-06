<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>API Endpoints</title>
  <style>
    /* General styling for dark theme */
    body {
      background-color: #121212;
      color: #e0e0e0;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
    }

    /* Search box styling */
    .search-container {
      margin-bottom: 20px;
    }
    .search-input {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      background-color: #1e1e1e;
      color: #e0e0e0;
      border: 1px solid #333;
      border-radius: 4px;
    }

    /* Card styling */
    .card {
      background-color: #1e1e1e;
      border: 1px solid #333;
      border-radius: 8px;
      padding: 20px;
    }
    .card-header {
      font-size: 22px;
      font-weight: bold;
      color: #ffffff;
      margin-bottom: 15px;
    }

    /* Endpoint list styling */
    .endpoint-list {
      list-style-type: none;
      padding: 0;
      margin: 0;
    }
    .endpoint-item {
      border-bottom: 1px solid #333;
      padding: 10px 0;
    }
    .endpoint-info {
      display: flex;
      align-items: center;
      margin-bottom: 5px;
    }

    /* Method badge styles */
    .endpoint-method {
      padding: 6px 10px;
      border-radius: 4px;
      font-weight: bold;
      color: #ffffff;
      margin-right: 10px;
      text-transform: uppercase;
    }
    .get { background-color: #4caf50; }
    .post { background-color: #2196f3; }
    .put { background-color: #ff9800; }
    .delete { background-color: #f44336; }

    /* URL and description styles */
    .endpoint-url {
      font-weight: bold;
      color: #e0e0e0;
    }
    .endpoint-description {
      color: #b0b0b0;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="search-container">
    <input type="text" class="search-input" placeholder="Search endpoints..." />
  </div>

  <div class="card">
    <div class="card-header">API Endpoints</div>
    <ul class="endpoint-list">
      @foreach ($routeData as $route)
        <li class="endpoint-item">
          <div class="endpoint-info">
            <div class="endpoint-method {{ strtolower($route['method']) }}">{{ $route['method'] }}</div>
            <div class="endpoint-url">{{ $route['url'] }}</div>
          </div>
          <div class="endpoint-description">{{ $route['name'] }} - {{ $route['action'] }}</div>
        </li>
      @endforeach
    </ul>
  </div>
</div>

<script>
  const searchInput = document.querySelector('.search-input');
  const endpointItems = document.querySelectorAll('.endpoint-item');

  searchInput.addEventListener('input', () => {
    const searchTerm = searchInput.value.toLowerCase();
    endpointItems.forEach(item => {
      const url = item.querySelector('.endpoint-url').textContent.toLowerCase();
      const description = item.querySelector('.endpoint-description').textContent.toLowerCase();
      if (url.includes(searchTerm) || description.includes(searchTerm)) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    });
  });
</script>
</body>
</html>
