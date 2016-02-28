# RabbitMQ Silex example

## Setup
### Install VirtualBox
https://www.virtualbox.org/wiki/Downloads

### Install Vagrant
https://www.vagrantup.com/downloads.html

```bash
$ vagrant -v
Vagrant 1.5.4
```

### Install Ansible
http://docs.ansible.com/ansible/intro_installation.html

```bash
$ ansible --version
ansible 2.0.0.2
```

### Download example

```bash
$ mkdir /path/to/example
$ cd /path/to/example
$ git clone git@github.com:imunew/silex-rabbitmq-example.git .
```

### Provisioning (Automatically）

```bash
$ cd /path/to/example
$ vagrant up
```

## Run example

### Start built-in web server
1. Start console
1. SSH login to virtual machine

    ```
    $ vagrant ssh
    ```
1. Start built-in web server

    ```
    [vagrant@vagrant-centos65 ~]$ cd /apps
    [vagrant@vagrant-centos65 apps]$ php -S 0.0.0.0:8001 -t web/ web/index_dev.php
    PHP 5.6.18 Development Server started at Thu Feb 18 16:05:58 2016
    Listening on http://0.0.0.0:8001
    Document root is /apps/web
    Press Ctrl-C to quit.
    ```

### View example page.
1. Start browser
1. Access url
    > http://192.168.101.11:8001/example
1. Click `Execute Synchronously` or `Execute Asynchronously` button.



