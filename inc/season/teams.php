<div class="cw-header">
    <div class="flex items-center justify-between">
        <div class="cw-title-box">
            <div class="cw-breadcrumbs">
                <a href="/"><i class="bi bi-house-fill"></i></a> > 
                <a href="<?php echo $SeasonDB->breadcrumbURL; ?>">Season List</a> > 
                <a href="/s/<?php echo $s; ?>"><?php echo $season->title; ?></a> > 
                <span>Create Teams</span>
            </div>
            <h1>Create Teams</h1>
        </div>
        <div class="cw-actions">
            
        </div>
    </div>
    <?php $cwGlobal->process_svr_status("season"); ?>