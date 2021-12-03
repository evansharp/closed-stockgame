<main class="col-md-10" style="display: <?php echo $admin_nav == 'settings' ? 'block':'none'; ?>">
    <div class="justify-content-between align-items-center">
        <h1>Settings</h1>
    </div>
    <form action="<?php echo base_url() . 'admin/settings'; ?>" method="post" class="pure-form">

    <div class="container">

        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-6">Game Online</div>
            <div class="col-md-5">

                <div class="form-check form-check-inline">

                    <input class="form-check-input" type="radio" name="set_setting_game_online" value="1" <?php if($game_online == true){ echo "checked"; }?> >
                    <label for="set_setting_game_online" class="form-check-label">Online</label>
                </div>

                <div class="form-check form-check-inline">

                    <input class="form-check-input" type="radio" name="set_setting_game_online" value="0" <?php if($game_online == false){ echo "checked"; }?> >
                    <label for="set_setting_game_online" class="form-check-label">Offline</label>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-6">Show worth on Leaderboard</div>
            <div class="col-md-5">

                <div class="form-check form-check-inline">
                    <input id="set_setting_show_worth_1" class="form-check-input" type="radio" name="set_setting_show_worth" value="1" <?php if($show_worth == true){ echo "checked"; }?>>
                    <label for="set_setting_show_worth" class="form-check-label">Yes</label>
                </div>

                <div class="form-check form-check-inline">
                    <input id="set_setting_show_worth_0" class="form-check-input" type="radio" name="set_setting_show_worth" value="0" <?php if($show_worth == false){ echo "checked"; }?>>
                    <label for="set_setting_show_worth" class="form-check-label">No</label>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-6">Game Running? <span class="tag <?php echo ($game_running == "active") ? "tag_green" : "tag_red" ?>"><?php echo ($game_running == true) ? "Yes" : "No"; ?></span>
            </div>

            <div class="col-md-5">

                <div class="form-check form-check-inline">

                    <input id="set_setting_game_running_1" class="form-check-input" type="radio" name="set_setting_game_running" value="1" <?php if($game_running == true){ echo "checked"; }?>>
                    <label for="set_setting_game_running" class="form-check-label">Yes</label>
                </div>

                <div class="form-check form-check-inline">

                    <input id="set_setting_game_running_0" class="form-check-input" type="radio" name="set_setting_game_running" value="0" <?php if($game_running == false){ echo "checked"; }?>>
                    <label for="set_setting_game_running" class="form-check-label">No</label>
                </div>


            </div>
        </div>

        <div class="row">
            <div class="col-8"></div>
            <div class="col-4">
                <button class="btn btn-primary text-end" type="submit"> Save Settings </button>
            </div>
        </div>

        </form>


        <h3>Reset Game</h3>

        <div class="row">

            <div class="col">
                <form action="<?php echo base_url() . 'admin/settings'; ?>" method="post">
                    <input type="hidden" name="manual_hs" value="showmepotatosalad">
                    <button class="btn btn-secondary text-end" type="submit">Check for Highscores</button>
                </form>

                <form action="<?php echo base_url() . 'admin/settings'; ?>" method="post">
                    <input type="hidden" name="reset" value="tsarbomba">
                    <button class="btn btn-danger text-end" type="submit"> Reset Game</button>
                </form>
            </div>
        </div>
    </div>
</main>
