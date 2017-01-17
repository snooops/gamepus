# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.
  config.vm.box = "debian/jessie64"
  
  config.vm.network :private_network, ip: "192.168.3.10"
  config.vm.hostname = "ts3api.development.de"  
  
  
  config.vm.synced_folder ".", "/vagrant", disabled: true
   
  
  config.vm.provision "shell", path: "setup.sh"
end
