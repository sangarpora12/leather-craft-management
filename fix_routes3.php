<?php
$file = __DIR__ . '/routes/web.php';
$content = file_get_contents($file);

// Remove any git merge conflict markers (handle various line endings)
$content = str_replace(">>>>>>>\n", '', $content);
$content = str_replace(">>>>>>>\r\n", '', $content);
$content = str_replace(">>>>>>>", '', $content);
$content = str_replace("<<<<<<<\n", '', $content);
$content = str_replace("<<<<<<<\r\n", '', $content);
$content = str_replace("<<<<<<<", '', $content);
$content = str_replace("=======\n", '', $content);
$content = str_replace("=======\r\n", '', $content);
$content = str_replace("=======", '', $content);

// Clean up multiple consecutive blank lines
$content = preg_replace("/\n{3,}/", "\n\n", $content);

file_put_contents($file, $content);

// Verify
$content = file_get_contents($file);
if (strpos($content, '>>>>>>>') !== false || strpos($content, '<<<<<<<') !== false || strpos($content, '=======') !== false) {
    echo "WARNING: Merge conflict markers still present!" . PHP_EOL;
} else {
    echo "Verification: No merge conflict markers found." . PHP_EOL;
}

// Check PHP syntax
$output = shell_exec("php -l " . escapeshellarg($file) . " 2>&1");
echo "PHP syntax check: " . $output;
