<?php
header("Content-Type: text/html; charset=iso-8859-1");
header("Cache-Control: no-cache, must-revalidate");

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$char_limit = 350;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;

echo "<html><head><title>WAP</title></head><body>";

if (empty($query)) {
    echo "<b>WAP Trazilica</b><br/>";
    echo "<form action='index.php' method='get'>";
    echo "<input type='text' name='q' value=''/><br/>";
    echo "<input type='submit' value='Trazi'/>";
    echo "</form>";
} else {
    $search_url = "http://frogfind.com/?q=" . urlencode($query);
    $raw_html = @file_get_contents($search_url);

    if ($raw_html === FALSE) {
        $raw_html = @file_get_contents("http://html.duckduckgo.com/html/?q=" . urlencode($query));
    }

    if ($raw_html === FALSE) {
        echo "Greska pri dohvacanju.";
    } else {
        $clean_text = preg_replace('/<(script|style)\b[^>]*>(.*?)<\/script>/is', "", $raw_html);
        $text = strip_tags($clean_text);
        $text = preg_replace('/\s+/', ' ', $text);
        
        $total_length = strlen($text);
        $total_pages = ceil($total_length / $char_limit);
        
        $start = ($page - 1) * $char_limit;
        $chunk = substr($text, $start, $char_limit);

        echo "<b>Rezultati za: " . htmlspecialchars($query) . "</b><br/>---<br/>";
        echo htmlspecialchars($chunk) . "<br/>---<br/>";
        echo "Str. $page od $total_pages<br/>";

        if ($page > 1) {
            $prev_page = $page - 1;
            echo "<a href='index.php?q=" . urlencode($query) . "&p=$prev_page'>[<-Nazad]</a> ";
        }
        if ($page < $total_pages) {
            $next_page = $page + 1;
            echo "<a href='index.php?q=" . urlencode($query) . "&p=$next_page'>[Dalje->]</a>";
        }
    }
    echo "<br/><a href='index.php'>[Nova pretraga]</a>";
}

echo "</body></html>";
?>
