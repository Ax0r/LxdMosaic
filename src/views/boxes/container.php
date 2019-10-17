<div id="containerBox" class="boxSlide">
    <div class="col-md-12 text-center">
        <h4> <u>
            <span id="container-currentState"></span>
            <span id="container-containerNameDisplay"></span>
            <span id="container-imageDescription"></span>
        </u></h4>
    </div>
    <div class="row" id="containerViewBtns">
        <div class="col-md-6 text-center">
            <div class="card bg-primary card-hover-primary text-center toggleCard" id="goToDetails">
                <div class="card-body">
                    Details
                </div>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <div class="card card-hover-primary text-center toggleCard" id="goToConsole">
                <div class="card-body">
                    <i class="fas fa-terminal"></i>Console
                </div>
            </div>
        </div>
    </div>
<div id="containerDetails">
<div class="row">
    <div class="col-md-6">
        <div class="card text-white bg-deepblue">
          <div class="card-body">
              <h5> <u> Container Details <i class="fas float-right fa-info-circle"></i> </u> </h5>
              Host: <span id="container-hostNameDisplay"></span>
              <br/>
              <a
                  href="https://github.com/lxc/pylxd/issues/242#issuecomment-323272318"
                  target="_blank">
                  CPU Time:
              </a><span id="container-cpuTime"></span>
              <br/>
              Created: <span id="container-createdAt"></span>
              <br/>
              Up Time: <span id="container-upTime"></span>
          </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-white bg-primary">
          <div class="card-body">
            <h5> <u> Network Information <i class="fas float-right fa-network-wired"></i> </u> </h5>
                <div class="col-md-12" id="networkDetails">
                </div>

          </div>
        </div>
    </div>

</div>
<br/>
<div class="row">
<!-- <h4> Container: <`span id="containerName"></span> </h4> -->
<div class="col-md-3">
    <div class="card card-accent-danger">
      <div class="card-header bg-info" role="tab" id="container-actionsHeading">
        <h5>
          <a class="text-white" data-toggle="collapse" data-parent="#accordion" href="#actionsCollapse" aria-expanded="true" aria-controls="container-actionsCollapse">
            Actions
            <i class="fas fa-edit float-right"></i>
          </a>
        </h5>
      </div>

      <div id="actionsCollapse" class="collapsed show" aria-expanded="true" role="tabpanel" aria-labelledby="container-actionsHeading">
        <div class="card-block bg-dark">
            <div class="form-group">
                <label><u> Change State </u></label>
                <select class="form-control" id="container-changeState">
                    <option value="" selected="selected">  </option>
                    <option value="startContainer"> Start </option>
                    <option value="stopContainer"> Stop </option>
                    <option value="restartContainer"> Restart </option>
                    <option value="freezeContainer"> Freeze </option>
                    <option value="unfreezeContainer"> Unfreeze </option>
                </select>
            </div>
            <hr/>
            <button class="btn btn-block btn-primary editContainerSettings">
                Settings
            </button>
            <button class="btn btn-block btn-success takeSnapshot">
                Snapshot
            </button>
            <hr/>
            <button class="btn btn-block btn-info copyContainer">
                Copy
            </button>
            <button class="btn btn-block btn-primary migrateContainer">
                Migrate
            </button>
            <button class="btn btn-block btn-warning renameContainer">
                Rename
            </button>
            <button class="btn btn-block btn-danger deleteContainer">
                Delete
            </button>
        </div>
      </div>
    </div>
</div>
<div class="col-md-3">
    <div class="card border-primary">
      <div class="card-header bg-info" role="tab">
        <h5 class="text-white">
            Profiles
             <i class="fas fa-users float-right"></i>
        </h5>
      </div>
      <div class="collapse show" role="tabpanel" >
        <div class="card-block bg-dark table-responsive">
            <table class="table table-dark table-bordered"id="profileData">
                  <thead class="thead-inverse">
                      <tr>
                          <th> Name </th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
            </table>
        </div>
      </div>
    </div>
    <br/>
    <div class="card border-primary">
      <div class="card-header bg-info" role="tab">
        <h5 class="text-white">
            Snapshots
            <i class="fas fa-images float-right"></i>
        </h5>
      </div>
      <div class="collapse show" role="tabpanel" >
        <div class="card-block bg-dark table-responsive">
            <table class="table table-dark table-bordered"id="snapshotData">
                  <thead class="thead-inverse">
                      <tr>
                          <th> Name </th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
            </table>
        </div>
      </div>
    </div>
</div>
<div class="col-md-6">
      <div class="card card-accent-success">
        <div class="card-header bg-info" role="tab" >
          <h5>
            <a class="text-white" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              Memory Details
              <i class="fas fa-memory float-right"></i>
            </a>
          </h5>
        </div>

        <div id="collapseOne" class="collapse show" role="tabpanel" >
          <div class="card-block bg-dark">
              <table class="table table-dark table-bordered" id="memoryData">
                  <thead class="thead-inverse">
                      <tr>
                          <th> Family </th>
                          <th> Ussage </th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
          </div>
        </div>
      </div>
</div>
</div>
</div>
<div id="containerConsole">
    <div id="terminal-container"></div>
</div>
</div>
<script src="/assets/dist/xterm.js"></script>
<!-- <script src="/assets/dist/xterm.attach.js"></script> -->
<script>

var term = new Terminal();
var consoleSocket;
var currentTerminalProcessId;

function loadContainerViewAfter(data = null, milSeconds = 2000)
{
    setTimeout(function(){
        let p = currentContainerDetails;
        if($.isPlainObject(data)){
            p = data;
        }
        loadContainerView(p);
    }, 2000);
}

function loadContainerTreeAfter(milSeconds = 2000)
{
    setTimeout(function(){
        createContainerTree();
    }, milSeconds);
}

function deleteContainerConfirm(hostId, hostAlias, container)
{
    $.confirm({
        title: 'Delete Container ' + hostAlias + '/' + container,
        content: 'Are you sure you want to delete this container ?!',
        buttons: {
            cancel: function () {},
            delete: {
                btnClass: 'btn-danger',
                action: function () {
                    let x = {
                        hostId: hostId,
                        container: container
                    }
                    ajaxRequest(globalUrls.containers.delete, x, function(data){
                        let r = makeToastr(data);
                        if(r.state == "success"){
                            loadContainerTreeAfter();
                        }
                        currentContainerDetails = null;
                        $("#overviewBox").show();
                        $("#containerBox").hide();
                    });
                }
            }
        }
    });
}

function renameContainerConfirm(hostId, container, reloadView)
{
    $.confirm({
        title: 'Rename Container!',
        content: `
            <div class="form-group">
                <label> New Name </label>
                <input class="form-control validateName" maxlength="63" name="name"/>
            </div>`,
        buttons: {
            cancel: function(){},
            rename: {
                text: 'Rename',
                btnClass: 'btn-blue',
                action: function () {
                    let modal = this;
                    let btn  = $(this);

                    let newName = this.$content.find('input[name=name]').val();

                    if(newName == ""){
                        $.alert('provide a new name');
                        return false;
                    }

                    modal.buttons.rename.setText('<i class="fa fa-cog fa-spin"></i>Renaming..'); // let the user know
                    modal.buttons.rename.disable();
                    modal.buttons.cancel.disable();

                    let x = {
                        newContainer: newName,
                        hostId: hostId,
                        container: container
                    }

                    ajaxRequest(globalUrls.containers.rename, x, function(data){
                        let x = makeToastr(data);
                        if(x.state == "error"){
                            return false;
                        }
                        modal.close();
                        createContainerTree();
                        if(reloadView){
                            currentContainerDetails.container = newName;
                            loadContainerView(currentContainerDetails);
                        }

                    });
                    return false;
                }
            },
        }
    });
}

function snapshotContainerConfirm(hostId, container)
{
    $.confirm({
        title: 'Snapshot Container - ' + container,
        content: `
            <div class="form-group">
                <label> Snapshot Name </label>
                <input class="form-control validateName" maxlength="63" name="name"/>
            </div>`,
        buttons: {
            cancel: function(){},
            rename: {
                text: 'Take Snapshot',
                btnClass: 'btn-blue',
                action: function () {
                    let modal = this;
                    let btn  = $(this);

                    let snapshotName = this.$content.find('input[name=name]').val();

                    if(snapshotName == ""){
                        $.alert('provide a snapshot name');
                        return false;
                    }

                    modal.buttons.rename.setText('<i class="fa fa-cog fa-spin"></i>Renaming..'); // let the user know
                    modal.buttons.rename.disable();
                    modal.buttons.cancel.disable();

                    let x = {
                        hostId: hostId,
                        container: container,
                        snapshotName: snapshotName
                    }

                    ajaxRequest(globalUrls.containers.snapShots.take, x, function(data){
                        let x = makeToastr(data);
                        if(x.state == "error"){
                            return false;
                        }
                        modal.close();
                    });
                    return false;
                }
            },
        }
    });
}

function copyContainerConfirm(hostId, container) {
    $.confirm({
        title: 'Copy Container!',
        content: `
            <div class="form-group">
                <label> New Host </label>
                <input class="form-control" maxlength="63" name="newHost"/>
            </div>
            <div class="form-group">
                <label> Name </label>
                <input class="form-control validateName" maxlength="63" name="name"/>
            </div>`,
        buttons: {
            cancel: function(){},
            copy: {
                text: 'Copy',
                btnClass: 'btn-blue',
                action: function () {
                    let modal = this;
                    let d = this.$content.find("input[name=newHost]").tokenInput("get");
                    let btn  = $(this);

                    modal.buttons.copy.setText('<i class="fa fa-cog fa-spin"></i>Copying..'); // let the user know
                    modal.buttons.copy.disable();
                    modal.buttons.cancel.disable();

                    if(d.length == 0){
                        return false;
                    }

                    let x = {
                        newContainer: modal.$content.find("input[name=name]").val(),
                        newHostId: d[0].hostId,
                        hostId: hostId,
                        container: container
                    };

                    ajaxRequest(globalUrls.containers.copy, x, function(data){
                        let x = makeToastr(data);
                        if(x.state == "error"){
                            return false;
                        }
                        loadContainerTreeAfter();
                        modal.close();
                    });
                    return false;
                }
            },
        },
        onContentReady: function () {
            // bind to events
            var jc = this;
            this.$content.find('input[name=newHost]').tokenInput(globalUrls.hosts.search.search, {
                queryParam: "host",
                propertyToSearch: "host",
                tokenValue: "hostId",
                preventDuplicates: false,
                tokenLimit: 1,
                theme: "facebook"
            });
        }
    });
}

function loadContainerView(data)
{
    $("#containerConsole").hide();
    $("#containerDetails").show();
    $("#goToDetails").trigger("click");
    if(consoleSocket !== undefined && currentTerminalProcessId !== null){
        consoleSocket.emit("close", currentTerminalProcessId);
        currentTerminalProcessId = null;
    }
    
    ajaxRequest(globalUrls.containers.getDetails, data, function(result){
        let x = $.parseJSON(result);

        if(x.state == "error"){
            makeToastr(result);
            return false;
        }

        $("#sidebar-ul").find(".text-info").removeClass("text-info");

        addBreadcrumbs([data.alias, data.container ], ["viewHost lookupId", "active"]);

        let disableActions = x.state.status_code !== 102;

        $(".renameContainer").attr("disabled", disableActions);
        $(".deleteContainer").attr("disabled", disableActions);

        $("#container-currentState").html(`<i class="` + statusCodeIconMap[x.state.status_code] +`"></i>`);
        $("#container-changeState").val("");

        //NOTE Read more here https://github.com/lxc/pylxd/issues/242
        let containerCpuTime = nanoSecondsToHourMinutes(x.state.cpu.usage);

        let os = x.details.config.hasOwnProperty("image.os") ? x.details.config["image.os"] : "<b style='color: #ffc107'>Can't find OS</b>";
        let version = "<b style='color: #ffc107'>Cant find verison</b>";
        if(x.details.config.hasOwnProperty("image.version")){
            version = x.details.config["image.version"];
        }else if(x.details.config.hasOwnProperty("image.release")){
            version = x.details.config["image.release"];
        }

        $("#container-hostNameDisplay").text(currentContainerDetails.alias);
        $("#container-containerNameDisplay").text(data.container);
        $("#container-imageDescription").html(` - ${os} (${version})`);
        $("#container-cpuTime").text(containerCpuTime);
        $("#container-createdAt").text(moment(x.details.create_at).format("MMM DD YYYY h:mm A"));

        if(x.details.hasOwnProperty("last_used_at")){
            let last_used_at = moment(x.details.last_used_at);
            if(last_used_at.format("YYYY") == "1970"){
                $("#container-upTime").text("Not Started Yet");
            }else if(!disableActions){
                $("#container-upTime").text("Not Running");
            }else{
                let now = moment(new Date());

                var ms = now.diff(last_used_at);
                var d = moment.duration(ms);
                var s = Math.floor(d.asHours()) + moment.utc(ms).format(":mm:ss")
                $("#container-upTime").text(s);
            }
        }else{
            $("#container-upTime").text("LXD Extension Missing");
        }

        let snapshotTrHtml = "";

        if(x.snapshots.length == 0){
            snapshotTrHtml = "<tr><td colspan='999' class='text-center'> No snapshots </td></tr>"
        }else{
            $.each(x.snapshots, function(i, item){
                snapshotTrHtml += `<tr><td><a href='#' id='${item}' class='viewSnapsnot'> ${item} </a></td></tr>`;
            });
        }

        $("#snapshotData >  tbody").empty().append(snapshotTrHtml);

        let profileTrHtml = "";

        if(x.details.profiles.length == 0){
            profileTrHtml = "<tr><td colspan='999' class='text-center'> No Profiles </td></tr>"
        }else{
            $.each(x.details.profiles, function(i, item){
                profileTrHtml += `<tr><td><a href='#' class='toProfile'>${item}</a></td></tr>`;
            });
        }

        $("#profileData >  tbody").empty().append(profileTrHtml);

        let networkData = "";

        $.each(x.state.network,  function(i, item){
            if(i == "lo"){
                return;
            }
            networkData += `<div class='padding-bottom: 2em;'><b>${i}:</b><br/>`;
            let lastKey = item.addresses.length - 1;
            $.each(item.addresses, function(i, item){
                networkData += `<span style='padding-left:3em'>${item.address}<br/></span>`;
            });
            networkData += "</div>";
        });
        $("#networkDetails").empty().append(networkData);

        let memoryHtml = "";

        $.each(x.state.memory, function(i, item){
            memoryHtml += `<tr><td>${i}</td><td>${formatBytes(item)}</tr>`;
        });

        $("#memoryData > tbody").empty().append(memoryHtml);

        $(".boxSlide").hide();
        $("#containerBox").show();
        $('html, body').animate({scrollTop:0},500);
    });
}

$("#containerBox").on("click", ".renameContainer", function(){
    renameContainerConfirm(currentContainerDetails.hostId, currentContainerDetails.container);
});

$("#containerBox").on("click", ".toggleCard", function(){
    $("#containerViewBtns").find(".bg-primary").removeClass("bg-primary");
    $(this).addClass("bg-primary");
});


$("#containerBox").on("click", "#goToDetails", function(){
    $("#containerDetails").show();
    $("#containerConsole").hide();
});

$("#containerBox").on("click", "#goToConsole", function() {
    Terminal.applyAddon(attach);

    $("#containerDetails").hide();
    $("#containerConsole").show();


    if(!$.isNumeric(currentTerminalProcessId)){
        const terminalContainer = document.getElementById('terminal-container');
        // Clean terminal
        while (terminalContainer.children.length) {
            terminalContainer.removeChild(terminalContainer.children[0]);
        }

        $.confirm({
            title: 'Container Shell!',
            content: `
                <div class="form-group">
                    <label> Shell </label>
                    <input class="form-control" value="bash" maxlength="63" name="shell"/>
                </div>
                `,
            buttons: {
                cancel: function(){},
                go: {
                    text: "Go!",
                    btnClass: "btn-primary",
                    action: function(){


                        let shell = this.$content.find("input[name=shell]").val();

                        if(shell == ""){
                            $.alert("Please input a shell");
                            return false;
                        }

                        term = new Terminal({});

                        term.open(terminalContainer);

                        // fit is called within a setTimeout, cols and rows need this.
                        setTimeout(() => {
                            $.ajax({
                                type: "POST",
                                url: '/terminals?cols=' + term.cols + '&rows=' + term.rows,
                                data: {
                                    hello: "hello"
                                },
                                success: function(processId) {
                                    currentTerminalProcessId = processId;
                                    consoleSocket = io.connect("/terminals", {
                                        query: $.extend({
                                            pid: processId,
                                            shell: shell
                                        }, currentContainerDetails)
                                    });
                                    consoleSocket.on('data', function(data) {
                                        term.write(data);
                                    });

                                    // Browser -> Backend
                                    term.on('data', function(data) {
                                        consoleSocket.emit('data', data);
                                    });
                                    // consoleSocket = new WebSocket(consoleSocketURL);
                                    consoleSocket.onopen = function() {
                                        term.attach(consoleSocket);
                                        term._initialized = true;
                                    };
                                },
                                error: function(){
                                    makeNodeMissingPopup();
                                },
                                dataType: "json"
                            });
                        }, 0);
                    }
                }
            }
        });
    }

});

$("#containerBox").on("click", ".toProfile", function(){
    let profile = $(this).text();
    loadProfileView(profile, currentContainerDetails.hostId, function(){
        viewProfile(profile, currentContainerDetails.alias, currentContainerDetails.hostId);
    });

});

$("#containerBox").on("click", ".copyContainer", function(){
    copyContainerConfirm(currentContainerDetails.hostId, currentContainerDetails.container);
});

$("#containerBox").on("click", ".migrateContainer", function(){
    $("#modal-container-migrate").modal("show");
});

$("#containerBox").on("click", ".takeSnapshot", function(){
    $("#modal-container-snapshot").modal("show");
});

$("#containerBox").on("click", ".editContainerSettings", function(){
    $("#modal-container-editSettings").modal("show");
});

$("#containerBox").on("click", ".deleteContainer", function(){
    deleteContainerConfirm(currentContainerDetails.hostId, currentContainerDetails.alias, currentContainerDetails.container);
});

$("#containerBox").on("change", "#container-changeState", function(){
    let url = globalUrls.containers.state[$(this).val()];
    ajaxRequest(url, currentContainerDetails, function(data){
        let result = makeToastr(data);
        loadContainerTreeAfter();
        loadContainerViewAfter();
    });
});

$("#containerBox").on("click", ".viewSnapsnot", function(){
    snapshotDetails.snapshotName = $(this).attr("id");
    $("#modal-container-restoreSnapshot").modal("show");
});
</script>
<?php
    require __DIR__ . "/../modals/containers/migrateContainer.php";
    require __DIR__ . "/../modals/containers/takeSnapshot.php";
    require __DIR__ . "/../modals/containers/restoreSnapshot.php";
    require __DIR__ . "/../modals/containers/createContainer.php";
    require __DIR__ . "/../modals/containers/editSettings.php";
?>
