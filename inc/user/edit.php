<?php 
// get data
$profileData = $UserDB->getProfile($current_user->ID);

// default values
if (!empty($profileData)) {
    $color_1_default = empty($profileData->color_1) ? "#0d6efd" : $profileData->color_1;
    $color_2_default = empty($profileData->color_2) ? "#303030" : $profileData->color_2;
    $nickname_default = empty($profileData->nickname) ? $u : $profileData->nickname;
    $status_default = empty($profileData->status) ? "" : $profileData->status;
    $discord_username_default = empty($profileData->discord_username) ? "" : $profileData->discord_username;
    $banner_img_default = empty($profileData->cover_img) ? "" : $profileData->cover_img;
    $profile_img_default = empty($profileData->prof_img) ? "" : $profileData->prof_img;
} else {
    // No profile data yet
    // set default values
    $color_1_default = "#0d6efd";
    $color_2_default = "#303030";
    $nickname_default = $u;
    $status_default = "";
    $discord_username_default = "";
    $banner_img_default = "";
    $profile_img_default = "";
}
?>

<div class="cw-header">
    <div class="flex items-center justify-between">
        <h1>Edit Profile: <?php echo $nickname_default; ?></h1>
        <div class="cw-actions">
            <a class="btn btn-secondary" href="/u/<?php echo $u; ?>">Cancel</a>
        </div>
    </div>
    <?php $cwGlobal->process_svr_status("profile"); ?>
</div>
<div class="cw-edit-profile">
    <div class="form-box">
        <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="onsubmit-valid-check">
            <input type="hidden" name="action" value="updateprofile"><!-- creates hook for php plugin -->
            <input type="hidden" name="WPID" value="<?php echo get_current_user_id(); ?>">
            <div class="field-box">
                <label for="field-banner-img-url" class="form-label">Profile Banner Image URL</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                    <input type="text" name="field-banner-img-url" class="urlimg form-control" id="field-banner-img-url" value="<?php echo $banner_img_default; ?>">
                </div>
            </div>
            <div class="field-box">
                <label for="field-profile-img-url" class="form-label">Profile Image URL</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                    <input type="text" name="field-profile-img-url" class="url form-control" id="field-profile-img-url" value="<?php echo $profile_img_default; ?>">
                </div>
            </div>
            <div class="field-box">
                <label for="field-color-1" class="form-label">Primary Color</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-paint-bucket"></i></span>
                    <input type="color" name="field-color-1" class="form-control" id="field-color-1" value="<?php echo $color_1_default; ?>">
                </div>
            </div>
            <div class="field-box">
                <label for="field-color-2" class="form-label">Secondary Color</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-paint-bucket"></i></span>
                    <input type="color" name="field-color-2" class="form-control" id="field-color-2" value="<?php echo $color_2_default; ?>">
                </div>
            </div>
            <div class="field-box">
                <label for="field-display-name" class="form-label">Display Name</label>
                <input type="text" name="field-display-name" class="form-control display-name" id="field-display-name" value="<?php echo $nickname_default; ?>" data-username="<?php echo $u; ?>">
            </div>
            <div class="field-box">
                <label for="field-status" class="form-label">Status</label>
                <input type="text" name="field-status" class="form-control" id="field-status" value="<?php echo $status_default; ?>">
            </div>
            <div class="field-box">
                <label for="field-discord-username" class="form-label">Discord Username</label>
                <input type="text" name="field-discord-username" class="discord form-control" id="field-discord-username" value="<?php echo $discord_username_default; ?>">
            </div>

            <div class="action-box">
                <a class="btn btn-secondary" href="/u/<?php echo $u; ?>">Cancel</a>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>