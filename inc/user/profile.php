<div class="container">
    <div class="cw-banner-img"></div>
    <div class="cw-profile-header-bar">
        <div class="cw-profile-header">
            <img src="<?php echo $cwGlobal->plugin_url; ?>img/profile-img-default.jpg" />
            <div class="cw-profile-header-info">
                <h1><?php echo $u; ?></h1>
                <p>This is where a player's status might go...</p>
            </div>
        </div>
        <?php if($owner) : ?>
            <div class="cw-actions">
                <a class="btn btn-primary" href="/u/<?php echo $u; ?>/edit">Edit Profile</a>
                <a class="btn btn-secondary" href="/u/<?php echo $u; ?>/account">Edit Account</a>
            </div>
        <?php endif; ?>
    </div>
</div>