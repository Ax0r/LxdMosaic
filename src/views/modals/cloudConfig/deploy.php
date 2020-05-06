    <!-- Modal -->
<div class="modal fade" id="modal-cloudConfig-deploy" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Deploy Cloud Config</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

          <div class="form-group">
              <label> Instance Name </label>
              <input class="form-control" name="containerName" />
          </div>
          <div class="form-group">
              <label> Hosts </label>
              <input class="form-control" id="deployCloudConfigHosts" />
          </div>
          <div class="form-group">
              <label> Image </label>
              <input class="form-control" id="deployCloudConfigImage" />
              <div class="alert alert-info">
                  Currently an image needs to have been imported into atleast
                  one server on the network to use it here!
              </div>
          </div>
          <div class="form-group">
              <label> Profile Name (Optional) </label>
              <input class="form-control" name="profileName" />
          </div>

          <div class="form-group">
              <label> Additonal Profiles </label>
              <input class="form-control" id="deployCloudConfigProfiles"/>
              <div class="alert alert-info">
                  Only profiles on all hosts will appear
                  <br/>
                  Remember the default profile usually contains storage information &
                  network details!
              </div>
          </div>
          <div class="form-group">
              <label> GPU's (Optional) </label>
              <select class="form-control" id="deployContainerGpu" multiple>
                  <option value="">Please select a host </option>
              </select>
              <div id="deployContainerGpuWarning" class="alert alert-danger">
                  We currently only support adding gpu's when creating a contaienr
                  on one host.
              </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="deployCloudConfig">Create</button>
      </div>
    </div>
  </div>
</div>
<script>

var deployCloudConfigObj = {
    cloudConfigId : null
}

$("#deployCloudConfigProfiles").tokenInput(globalUrls.profiles.search.getCommonProfiles, {
    queryParam: "profile",
    propertyToSearch: "profile",
    theme: "facebook",
    tokenValue: "Profile_ID"
});

$("#deployCloudConfigHosts").tokenInput(globalUrls.hosts.search.search, {
    queryParam: "host",
    propertyToSearch: "host",
    tokenValue: "hostId",
    preventDuplicates: false,
    theme: "facebook",
    onAdd: function(token){
        let h = $("#deployCloudConfigHosts").tokenInput("get")
        if(h.length > 1){
            $("#deployContainerGpuWarning").show();
            $("#deployContainerGpu").hide();
        }else{
            let x = {hostId: h[0].hostId}
            ajaxRequest(globalUrls.hosts.gpu.getAll, x, (data)=>{
                data =  $.parseJSON(data);
                //TODO if len == 0
                let gpus = "";
                $.each(data, function(i, item){
                    gpus += `<option value="${item.pci_address}">${item.product}</option>`
                });
                $("#deployContainerGpu").empty().append(gpus);
            });
        }
    },
    onDelete: function(){
        let h = $("#deployCloudConfigHosts").tokenInput("get")
        if(h.length > 1){
            $("#deployContainerGpuWarning").show();
            $("#deployContainerGpu").hide();
        }else{
            if(h.length == 0){
                $("#deployContainerGpu").empty().append("<option value=''>Please select a host</option>");
            }
            $("#deployContainerGpuWarning").hide();
            $("#deployContainerGpu").show();
        }
    }
});

$("#deployCloudConfigImage").tokenInput(globalUrls.images.search.searchAllHosts, {
    queryParam: "image",
    tokenLimit: 1,
    propertyToSearch: "description",
    theme: "facebook",
    tokenValue: "details"
});


$("#modal-cloudConfig-deploy").on("hide.bs.modal", function(){
    $("#modal-cloudConfig-deploy input").val("");
    $("#deployCloudConfigProfiles").tokenInput("clear");
    $("#deployCloudConfigHosts").tokenInput("clear");
    $("#deployCloudConfigImage").tokenInput("clear");
});

$("#modal-cloudConfig-deploy").on("shown.bs.modal", function(){
    if(!$.isNumeric(deployCloudConfigObj.cloudConfigId)){
        makeToastr(JSON.stringify({state: "error", message: "Developer fail - set cloud config id to open this modal"}));
        return false;
    }
});

$("#modal-cloudConfig-deploy").on("click", "#deployCloudConfig", function(){
    let profileIds = mapObjToSignleDimension($("#deployCloudConfigProfiles").tokenInput("get"), "profile");
    let hosts = mapObjToSignleDimension($("#deployCloudConfigHosts").tokenInput("get"), "hostId");

    let containerNameInput = $("#modal-cloudConfig-deploy input[name=containerName]");
    let containerName = containerNameInput.val();
    let profileNameInput = $("#modal-cloudConfig-deploy input[name=profileName]");
    let profileName = profileNameInput.val();
    let image = $("#deployCloudConfigImage").tokenInput("get");

    if(containerName == ""){
        makeToastr(JSON.stringify({state: "error", message: "Please provide instance name"}));
        containerNameInput.focus()
        return false;
    } else if(hosts.length == 0){
        makeToastr(JSON.stringify({state: "error", message: "Please provide atleast one host"}));
        $("#deployCloudConfigHosts").focus();
        return false;
    } else if(image.length == 0 || !image[0].hasOwnProperty("details")){
        makeToastr(JSON.stringify({state: "error", message: "Please select image"}));
        return false;
    }

    let gpus = [];

    if(hosts.length == 1){
        gpus = $("#deployContainerGpu").val();
    }

    let x = {
        hosts: hosts,
        containerName: containerName,
        cloudConfigId: deployCloudConfigObj.cloudConfigId,
        profileName: profileName,
        additionalProfiles: profileIds,
        imageDetails: image[0].details,
        gpus: gpus
    };

    ajaxRequest(globalUrls.cloudConfig.deploy, x, (response)=>{
        response = makeToastr(response);
        if(response.state == "error"){
            return false;
        }
        $("#modal-cloudConfig-deploy").modal("toggle");
    });
});
</script>
