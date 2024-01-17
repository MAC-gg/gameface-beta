<?php 
// get data
// $current_user setup in user-route.php
$cuid = get_query_var('cw-admin-cuid', $current_user->ID);
$profileData = $UserDB->getProfile($cuid);
$accountData = $UserDB->getAccount($cuid);

// default values
if (!empty($profileData)) {
    $color_1_default = empty($profileData->color_1) ? "#0d6efd" : $profileData->color_1;
    $color_2_default = empty($profileData->color_2) ? "#303030" : $profileData->color_2;
    $displayName_default = empty($profileData->displayName) ? $u : $profileData->displayName;
    $status_default = empty($profileData->status) ? "" : $profileData->status;
    $discord_username_default = empty($profileData->discord_username) ? "" : $profileData->discord_username;
    $need_mask = 0;

    $banner_img_styles = "border-bottom-color:" . $color_2_default . ";";
    if (!empty($profileData->cover_img)) {
        $banner_img_styles .= "background-image:url(" . $profileData->cover_img . ");";
    }

    if(!empty($profileData->prof_img)) {
        $profile_img_styles = "background-image:url(" . $profileData->prof_img . ");";
    } else {
        $profile_img_styles = "background-color:" . $color_1_default . ";";
        $need_mask = 1;
    }

    $profile_img_box_styles = "border-color:" . $color_1_default . "; background-color:" . $color_2_default . ";";
} else {
    // No profile data yet
    // set default values
    $color_1_default = "#0d6efd";
    $color_2_default = "#303030";
    $displayName_default = $u;
    $status_default = "";
    $discord_username_default = "";

    // set default styles
    $banner_img_styles = "border-bottom-color:" . $color_2_default . ";";
    $profile_img_styles = "background-color:" . $color_1_default . ";";
    $need_mask = 1;
    $profile_img_box_styles = "border-color:" . $color_1_default . "; background-color:" . $color_2_default . ";";
}

$cwGlobal->dev_only_options($cuid, "/u/$u"); ?>
<div class="cw-util-bar">
    <?php $cwGlobal->getUserBreadcrumbs($profileData, " "); ?>
    <?php $cwGlobal->getUserTray($cuid); ?>
</div>
<?php $cwGlobal->process_svr_status("profile"); ?>
<div class="cw-profile">
    <div class="cw-banner-img" style="<?php echo $banner_img_styles; ?>"></div>
    <div class="cw-profile-header-bar">
        <div class="cw-profile-img-box" style="<?php echo $profile_img_box_styles; ?>">
            <div class="cw-profile-img<?php if($need_mask) echo ' mask'; ?>" style="<?php echo $profile_img_styles; ?>"></div>
        </div>
        <div class="cw-profile-header">
            <div class="cw-profile-header-info">
                <h1><?php echo $displayName_default; ?></h1>
                <?php if(!empty($status_default)) : ?>
                    <p><?php echo $status_default; ?></p>
                <?php endif; ?>
                <?php if(!empty($discord_username_default)) : ?>
                    <div class="cw-profile-socials">
                        <p title="Click to Copy"><i class="bi bi-discord"></i> <strong>Discord:</strong> <?php echo $discord_username_default; ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <?php if($owner) : ?>
                <div class="cw-actions">
                    <a class="btn btn-primary" href="/u/<?php echo $u; ?>/edit">Edit Profile</a>
                    <a class="btn btn-secondary" href="/u/<?php echo $u; ?>/account">Edit Account</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div style="background-color:#efefef;/*padding:2.5rem;*/">
    </div>
</div>