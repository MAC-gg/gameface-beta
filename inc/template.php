<!-- Template View -->
<!-- /inc/template.php -->
<?php 
// get data
// $current_user setup in user-route.php
$cuid = get_query_var('cw-admin-cuid', $current_user->ID);
$profileData = $UserDB->getProfile($cuid);
$accountData = $UserDB->getAccount($cuid);
?>
<?php $cwGlobal->dev_only_options($cuid, "/u/$u/notis"); ?>
<div class="cw-util-bar">
    <?php $cwGlobal->getUserBreadcrumbs($profileData, "Notifications"); ?>
    <?php $cwGlobal->getUserTray($cuid); ?>
</div>
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
