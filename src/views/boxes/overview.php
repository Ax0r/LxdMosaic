<div id="overviewBox" class="boxSlide">
    <div class="row">
      <!-- /.col-->
      <div class="card-columns cols-2" id="serverOverviewGraphs">
          <div class="card">
              <div class="card-body">
                  <div class="alert alert-warning text-center notEnoughData">
                      <b> <u> Memory Ussage Chart </u> </b><br/>
                      Not enough data check again in 10 minutes
                  </div>
                  <canvas id="memoryUsage" height="200"></canvas>
              </div>
          </div>
          <div class="card">
              <div class="card-body">
                  <div class="alert alert-warning text-center notEnoughData">
                      <b> <u> Active Containers Chart </u> </b><br/>
                      Not enough data check again in 10 minutes
                  </div>
                  <canvas id="activeContainers" height="200"></canvas>
              </div>
          </div>
      </div>
      <!-- /.col-->
    </div>
    <div class="row">
      <!-- /.col-->
      <div class="col-sm-6 col-lg-12" id="serverOverviewDetails">
      </div>
      <!-- /.col-->
    </div>
</div>

<script>

var emptyServerBox = function(){
    return $(`
    <div class="brand-card">
      <div class="brand-card-header bg-twitter">
        <h4 class='host'></h4>
      </div>
      <div class="brand-card-body">
        <div>
          <div class="text-value">CPU</div>
          <div class="text-uppercase text-muted cpuDetails">CPU Details</div>
        </div>
        <div>
          <div class="text-value">Memory</div>
          <div class="text-uppercase text-muted memory"></div>
        </div>
        <div>
            <div class="text-value">Project</div>
            <div class="form-group projectFormGroup">
                <select class="form-control projects changeHostProject"></select>
            </div>

        </div>
      </div>
    </div>`);
}

$(document).on("click", "#createContainer", function(){
    $("#modal-container-create").modal("show");
});
</script>
