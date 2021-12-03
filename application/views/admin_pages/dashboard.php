<main class="col-md-10" style="display: <?php echo $admin_nav == 'dashboard' ? 'block':'none'; ?>">

    <div class="justify-content-between align-items-center">
        <h1>Dashboard</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-6">
            <div class="card">
              <div class="card-header">
                Stats
              </div>
              <div class="card-body">
                Days Running: <em><?php echo $days_running;?></em><br>
                Trades Made: <em><?php echo $total_trades;?></em><br>
              </div>
            </div>
        </div>
        <div class="col-6">

            <div class="card">
                <div class="card-header">
                    Stock Updates Status
                </div>
              <div class="card-body">
                  <?php echo $auto_update_info; ?>

              </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Player Logins
                </div>
                <div class="card-body">
                    <div id="player_activity_chart_container">
                        <canvas></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
