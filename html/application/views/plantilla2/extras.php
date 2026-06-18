<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!-- CSS -->
<!-- Extra css -->
<?php if (isset($css)): ?>
    <?php foreach ($css as $extra => $url): ?>
        <link rel="stylesheet" href="<?= base_url().$url;?>">
    <?php endforeach; ?>
<?php endif; ?>
<!-- JS -->
<!-- Extra js -->
<?php if (isset($js)): ?>
    <?php foreach ($js as $extra => $url): ?>
        <script src="<?= base_url().$url;?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
