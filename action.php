<?php

/**
 * Require
 */
if (file_exists(realpath(dirname(__FILE__)) . '/config.php')) {
  $config = require_once(realpath(dirname(__FILE__)) . '/config.php');
} else {
  $error = 'Config file missing. Check README for instructions.';
}

/**
 * Functions
 */
function getUrlContent($url, $user_agent_lang): string {
    $content = '';
    
    // Check if url is valid
    $headers = @get_headers($url);
    if($headers === FALSE || strpos($headers[0], '200') === FALSE) {
        return $content;
    }

    // Randomly select user agent
    $user_agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:95.0) Gecko/20100101 Firefox/95.0',
        'Mozilla/5.0 (iPad; CPU OS 10_3_4 like Mac OS X) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.0 Mobile/14G61 Safari/602.1',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36 OPR/82.0.4227.50',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36 Edg/96.0.1054.62'
    ];
    $user_agent = $user_agents[rand(0,count($user_agents)-1)];

    // Create header
    $header = [
        'GET /1575051 HTTP/1.1',
        'Host:' . parse_url($url)['host'],
        'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language:' . $user_agent_lang . ';q=0.8',
        'Cache-Control:max-age=0',
        'Connection:keep-alive',
        'Host:adfoc.us',
        'User-Agent:' . $user_agent,
    ];

    // Parse url with curl and handle cookies
    $cookies = 'cookies.txt';
    fopen($cookies, 'w');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    $content = htmlspecialchars(curl_exec($ch));
    curl_close($ch);
    unlink($cookies);

    // Check curl response
    if (trim($content) == '' || strpos($content, '400') || mb_stripos($content, 'bad request') || mb_stripos($content, 'validateCaptcha')) {
        // Parse with file_get_contents if response is faulty
        $context = stream_context_create(['http' => ['header' => 'User-Agent:' . $user_agent]]);
        $content = htmlspecialchars(file_get_contents($url, false, $context));
    }
    return $content;
}

/**
 * Action Logic
 */
$success = '';
$warning = '';
$status = '';
$details = '';

// Make action keys list
$action_keys = [];
foreach ($config['actions'] as $a) {
    array_push($action_keys, $a['key']);
}

// Check url parameter and script paramter
$key = null;
if (isset($_GET['key']) && in_array($_GET['key'], $action_keys)) {
    $key = $_GET['key'];
} elseif (isset($argv) && in_array($argv[1], $action_keys)) {
    $key = $argv;
}

if ($key != null) {
    // Select action
    $action = $config['actions'][array_search($key, $action_keys)];

    // Parse
    foreach ($action['checks'] as $check) {
        $url = $check[0];
        $phrase = $check[1];
        $contain = $check[2];
        $domain = str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
        $content = getUrlContent($url, $config['main']['user_agent_lang']);
        $details .= "<b>Parse results for " . $domain . ":</b></br>" . substr($content, 0, 5000) . " …</br></br>";

        // Check content
        if ($content === false || trim($content) == "") {
            $warning .= "<b>$domain</b> could not be parsed. Potential reasons: URL invalid, IP blocked or parsing blocked.</br>";
        } else {
            // Check phrase
            $ok = false;
            if ((strpos($content, $phrase) || mb_stripos($content, $phrase)) == $contain) {
                $ok = true;
            }

            // Check result
            if ($ok) {
                $success .= $action['success'] . " - <b>$domain</b> | <a href='$url' target='_blank'>$url</a></br>";
            } else {
                $warning .= $action['error'] . " - <b>$domain</b> | <a href='$url' target='_blank'>$url</a></br>";
            }
        }
    }

    // Send mail on success
    if ($success != "") {
        $mail_from = $config['main']['mail_from'];
        $mails_to = $config['main']['mail_to'];

        if ($mail_from != "" && $mails_to[0] != "") {
            $message = '<html>
                            <body>
                                <h1>' . $action['success'] . '</h1>
                                <p>' . $success . '</p>
                                <p>© ' . date('Y') . ' MailSnitch</p>
                            </body>
                        </html>';
            $subject = $action['success'];
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";

            foreach ($mails_to as $mail_to) {
                $headers .= 'From: ' . $mail_from . "\r\n" .
                            'Reply-To: ' . $mail_to . "\r\n" .
                            'X-Mailer: PHP/' . phpversion();
                if (@mail($mail_to, $subject, $message, $headers)) {
                    $status .= "Mail has been sent to $mail_to.</br>";
                }
            }
        }
    }
}
