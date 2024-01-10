<?php 
// get data
$WPAcct = get_user_by("slug", $u); // WORDPRESS PROFILE ACCT
$profileData = $UserDB->getProfile($WPAcct->ID);
$accountData = $UserDB->getAccount($WPAcct->ID);
?>
<?php $cwGlobal->userBreadcrumbs($profileData, "Notifications"); ?>
<?php $cwGlobal->process_svr_status("user"); ?>
<div class="cw-header">
    <div class="flex items-center">
        <div class="cw-title-box">
            <h1><?php echo $profileData->displayName; ?>: Notifications</h1>
        </div>
        <div class="cw-actions">
            
        </div>
    </div>
</div>
<div class="row cw-row">
    <?php // Get all notifications 
    $allNotis = $NotiDB->getNotiList($WPAcct->ID); 
    foreach( $allNotis as $noti ) { 
        ?>
        <a class="<?php echo $noti->type; ?><?php echo !$noti->isRead ? ' new' : ''; ?>" 
           href="<?php echo NotiDB::linkBuilder($noti->ref, $noti->refID); ?>">
            <?php echo $noti->msg; ?>
        </a>
    <?php } ?>
</div>