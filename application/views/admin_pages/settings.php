<main class="col-md-10" style="display: <?php echo $admin_nav == 'settings' ? 'block':'none'; ?>">
    <div class="justify-content-between align-items-center">
        <h1>Settings</h1>
    </div>
    <div class="pure-g">
        <form action="<?php echo base_url() . 'admin/settings'; ?>" method="post" class="pure-form">

            <div class="pure-u-1-4">Game Online</div>
            <div class="pure-u-3-4">
                <label for="set_setting_game_online" class="pure-radio">
                    <input id="set_setting_game_online_1" type="radio" name="set_setting_game_online" value="1" <?php if($game_online_selected == true){ echo "checked"; }?> >
                    Online
                </label>
                <label for="set_setting_game_online" class="pure-radio">
                    <input id="set_setting_game_online_0" type="radio" name="set_setting_game_online" value="0" <?php if($game_online_selected == false){ echo "checked"; }?> >
                    Offline
                </label>
            </div>

            <div class="pure-u-1"><button type="submit"> Save Settings</button></div>

        </form>


        <div class="pure-u-1"><h3>Reset Game</h3></div>

        <div class="pure-u-1-5">
            <form action="<?php echo base_url() . 'admin/settings'; ?>" method="post">
                <input type="hidden" name="reset" value="tsarbomba">
                <button type="submit" class="button-red">Reset Game</button>
            </form>
        </div>
        
        <div class="pure-u-1-5"></div>
        <div class="pure-u-1-5"></div>
        <div class="pure-u-1-5"></div>
    </div>
</main>
