<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <meta http-equiv="refresh" content="180">
</head>
<body>
<section class="section">
    <div class="container">
        <h1 class="title">Status</h1>
        <table class="table is-striped is-hoverable">
            <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php

            $url = 'http://[::1]:3001/metrics';
            $apiKey = 'your-api-key';

            $data = file_get_contents($url, false, stream_context_create([
                'http' => [
                    'header' => "Authorization: Basic " . base64_encode($apiKey) . "\r\n"
                ]
            ]));

            
            $lines = explode("\n", $data);
            $monitors = array();
            foreach ($lines as $line) {
                if (preg_match('/monitor_status\{([^}]*)\}\s(\d+)/', $line, $matches)) {
                    $labels = $matches[1];
                    $status = $matches[2];

                    // Den Namen aus den Labels extrahieren
                    preg_match('/monitor_name="([^"]+)"/', $labels, $name_match);
                    $name = $name_match[1];

                    $monitors[] = array('name' => $name, 'status' => $status);
                }
            }

            foreach ($monitors as $monitor) {
                echo '<tr ';
                echo 'title="' . htmlspecialchars($monitor['name']) . ' ';
                echo  ($monitor['status'] == 1 ? 'up' : ($monitor['status'] == 0 ? 'down' : ($monitor['status'] == 2 ? 'pending' : 'maintenance'))) . '" >';
                echo '<td>' . htmlspecialchars($monitor['name']) . '</td>';
                echo '<td>' . ($monitor['status'] == 1 ? '✅' : ($monitor['status'] == 0 ? '❌' : ($monitor['status'] == 2 ? '🕐' : '🪛'))) . '</td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</section>
</body>
</html>
