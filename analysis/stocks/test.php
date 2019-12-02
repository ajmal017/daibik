<?php
$info = file_get_contents('live_feed.json');
$info = json_decode($info);

print '<pre>';
print_r($info);

?>
