# LXD Mosaic

This is an application you can use to do basic management for multiple instances
of LXD


<img src="https://i.imgur.com/92xYQIZ.png" width="428"> <img src="https://i.imgur.com/vbEKP4r.png" width="428">

<img src="https://i.imgur.com/pYHnBjt.png" width="428"><img src="https://i.imgur.com/1aSwRWy.png" width="428">

## Documentation

Please refer to the documentation <a href="https://lxdmosaic.readthedocs.io/en/latest/"> here </a>

## Installation

### Prep LXD Instances

You need to enable access from the network on your LXD hosts first, you can do this by logging onto your hosts and executing the following (make sure to change the password from "some-secret_string")

```bash
lxc config set core.https_address [::]
lxc config set core.trust_password some-secret-string #remember this you will be asked later
```

## Launching LXDMosaic

The preferred installation method is using a ubuntu container.

### Install script
**Warning this installs apache, docker, mysql-server, php, git and other
dependencies its best to run in a container or an empty VM to avoid cluttering
your system**

In examples you will find an bash script called install_with_clone.sh this will
handle the installation of dependencies and setup this program.

It handles the cloning of the repository so you can just do;
#### Ubuntu
```bash
# Launch a ubuntu container
lxc launch ubuntu: lxdMosaic
# Connect to ubuntu console
lxc exec lxdMosaic bash
# Download the script
curl https://raw.githubusercontent.com/turtle0x1/LxdMosaic/master/examples/install_with_clone.sh >> installLxdMosaic.sh
# Then give the script execution permissions
chmod +x installLxdMosaic.sh
# Then run bellow to setup the program
./installLxdMosaic.sh
```
#### Centos 7
```bash
# Launch a centos 7 container
lxc launch images:centos/7/amd64 lxdMosaic
# Connect to centos console
lxc exec lxdMosaic bash
# Download the script
curl https://raw.githubusercontent.com/turtle0x1/LxdMosaic/master/examples/install_with_clone_centos7.sh >> installLxdMosaic.sh
# Then give the script execution permissions
chmod +x installLxdMosaic.sh
# Then run bellow to setup the program
./installLxdMosaic.sh
```

Once installation is complete you need to go to into your browser and goto;

`https://host_ip_address`

## Built With

Use lots of composer libraries and an extended [coreui](https://coreui.io/) for the frontend
