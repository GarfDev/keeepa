<?php
require_once 'autoload.php';
//AMember

//Check Session

$account = getKeepaAccount($db);

$json = $account['localstorage'];


$data = @json_decode($json);

$setItem = '';

foreach($data as $name => $value) {
    $setItem = $setItem.'window.localStorage.setItem(\''.$name.'\', \''.$value.'\');'."\n";
}

?>
<h1>Logging In, Please Wait...</h1>
<script>
    window.addEventListener('DOMContentLoaded', (event) => {
        <?= $setItem; ?>
        setTimeout(() => {
            window.location.href = '/';
        }, 2000);
    });
</script>