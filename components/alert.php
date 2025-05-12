<?php 
    $alert = Session::getInstance()->getAlert();
    Session::getInstance()->removeAlert();
?>

<?php if ($alert['alert']): ?>
    <div class="alert alert-<?php echo $alert['alert_type']; ?>">
        <?php echo $alert['alert_text']; ?>
    </div>
<?php endif; ?>
