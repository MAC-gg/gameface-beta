<?php 
// get data
$accountData = $UserDB->getAccount($current_user->ID);

// default values
if (!empty($accountData)) {
    $fname_default = empty($accountData->fname) ? "" : $accountData->fname;
    $lname_default = empty($accountData->lname) ? "" : $accountData->lname;
    $address1_default = empty($accountData->address1) ? "" : $accountData->address1;
    $address2_default = empty($accountData->address2) ? "" : $accountData->address2;
    $city_default = empty($accountData->city) ? "" : $accountData->city;
    $state_default = empty($accountData->state) ? "" : $accountData->state;
    $zip_default = empty($accountData->zip) ? "" : $accountData->zip;
} else {
    // No profile data yet
    // set default values
    $fname_default = "";
    $lname_default = "";
    $address1_default = "";
    $address2_default = "";
    $city_default = "";
    $state_default = "";
    $zip_default = "";
}
?>

<div class="cw-header">
    <div class="flex items-center justify-between">
        <h1>Edit Account: <?php echo $u; ?></h1>
        <div class="cw-actions">
            <a class="btn btn-secondary" href="/u/<?php echo $u; ?>">Cancel</a>
        </div>
    </div>
    <?php $cwGlobal->process_svr_status("account"); ?>
</div>
<div class="container cw-edit-account">
    <div class="cw-form-box">
        <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="updateaccount"><!-- creates hook for php plugin -->
            <input type="hidden" name="WPID" value="<?php echo get_current_user_id(); ?>">
            <div class="field-box">
                <label for="field-fname" class="form-label">First Name</label>
                <input type="text" name="field-fname" class="form-control" id="field-fname" value="<?php echo $fname_default; ?>">
            </div>
            <div class="field-box">
                <label for="field-lname" class="form-label">Last Name</label>
                <input type="text" name="field-lname" class="form-control" id="field-lname" value="<?php echo $lname_default; ?>">
            </div>
            <div class="field-box">
                <label for="field-address-1" class="form-label">Address</label>
                <input type="text" name="field-address-1" class="form-control" id="field-address-1" value="<?php echo $address1_default; ?>">
            </div>
            <div class="field-box">
                <label for="field-address-2" class="form-label">Address(2)</label>
                <input type="text" name="field-address-2" class="form-control" id="field-address-2" value="<?php echo $address2_default; ?>">
            </div>
            <div class="field-box">
                <label for="field-city" class="form-label">City</label>
                <input type="text" name="field-city" class="form-control" id="field-city" value="<?php echo $city_default; ?>">
            </div>
            <div class="field-box">
                <label for="field-state" class="form-label">State</label>
                <input type="text" name="field-state" class="form-control" id="field-state" value="<?php echo $state_default; ?>">
            </div>
            <div class="field-box">
                <label for="field-zip" class="form-label">Zip</label>
                <input type="text" name="field-zip" class="form-control" id="field-zip" value="<?php echo $zip_default; ?>">
            </div>

            <div class="action-box">
                <a class="btn btn-secondary" href="/u/<?php echo $u; ?>">Cancel</a>
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>