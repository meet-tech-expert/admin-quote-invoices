<?php
$key = 'aqi_admin_notices_'. get_current_user_id();
$notices = get_transient($key);
//print_r($notices);
if ($notices) {
    foreach ($notices as $notice)
        echo '<div class="', $notice['class'], '" style="padding:11px 15px; margin:5px 15px 2px 0;">', $notice['message'], '</div>' . PHP_EOL;
}
delete_transient( $key );
?>
