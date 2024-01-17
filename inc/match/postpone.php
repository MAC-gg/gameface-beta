<!-- Postpone View -->
<!-- /inc/match/postpone.php -->
<?php 
// get data
$cuid = get_query_var('cw-admin-cuid', $current_user->ID);
$profileData = $UserDB->getProfile($cuid);
$accountData = $UserDB->getAccount($cuid);
?>
<?php $cwGlobal->dev_only_options($cuid, "/s/$s/m/$m"); ?>
<div class="cw-util-bar">
    <?php $cwGlobal->getBreadcrumbs($season, "Match: $m", $match, "Postpone"); ?>
    <?php $cwGlobal->getUserTray($cuid); ?>
</div>
<?php $cwGlobal->process_svr_status("match"); ?>
<div class="cw-header">
    <div class="flex items-center justify-between">
        <div class="cw-title-box">
            <h1>Postpone Match</h1>
        </div>
        <div class="cw-actions">
            <a class="btn btn-secondary" href="/s/<?php echo $s; ?>/m/<?php echo $m; ?>">Cancel</a>
        </div>
    </div>
</div>
<div class="row cw-row">
    <div class="col-6">
        <div class="cw-box">
            <h2>New Match Settings</h2>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                <input type="hidden" name="action" value="postponematch"><!-- creates hook for php plugin -->
                <input type="hidden" name="redirect" value="/s/<?php echo $s; ?>/m/<?php echo $m; ?>">
                <input type="hidden" name="field-match" value="<?php echo $match->id; ?>">

                <div class="field-box">
                    <label for="field-date" class="form-label">New Date</label>
                    <select name="field-date" id="field-date" class="form-select">
                        <?php $tempMatchDate = strtotime($match->matchDatetime);
                        for( $i = 1; $i <= 10; $i++ ) {
                            echo "<option value='" . date("Y-m-d", $tempMatchDate) . "'>" . date("D M j", $tempMatchDate) . "</option>";
                            $tempMatchDate = strtotime("+1 days", $tempMatchDate);
                        } ?>
                    </select>
                </div>

                <div class="field-box">
                    <label for="field-hours" class="form-label">New Time</label>
                    <div class="cw-time-select">
                        <select name="field-hours" id="field-hours" class="form-select">
                            <option>10</option>
                            <option>11</option>
                            <option>12</option>
                            <option>13</option>
                            <option>14</option>
                            <option>15</option>
                            <option>16</option>
                            <option>17</option>
                            <option>18</option>
                            <option>19</option>
                            <option>20</option>
                            <option>21</option>
                            <option>22</option>
                        </select>
                        <select name="field-mins" id="field-mins" class="form-select">
                            <option>00</option>
                            <option>30</option>
                        </select>
                    </div>
                </div>

                <div class="cw-action-box">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>