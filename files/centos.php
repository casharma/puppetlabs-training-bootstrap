<?php
header("Content-Type: text/plain");
echo("# host is " . $_SERVER['SERVER_NAME'] . "\n");
?>
# product: centos
# version: 5
# arch: x64

# System authorization information
auth  --useshadow  --enablemd5  --enablecache
# System bootloader configuration
bootloader --location=mbr
# Clear the Master Boot Record
zerombr
# Partition clearing information
clearpart --all --initlabel
# Disk partitioning information
part /boot --fstype ext2 --size=768
part pv.01 --size=1 --grow
volgroup vg00 pv.01
logvol / --name=rootvol --vgname=vg00 --size=1 --grow --fstype ext3
logvol swap --name=swapvol --vgname=vg00 --size=1024
logvol /tmp --name=tmpvol --vgname=vg00 --size=1024 --fstype ext3
logvol /home --name=homevol --vgname=vg00 --size=5120 --fstyp ext3
# Use text mode install
text
# Firewall configuration
firewall --disabled
# Run the Setup Agent on first boot
firstboot --disable
# System keyboard
keyboard us
# System language
lang en_US
# Installation logging level
logging --level=info
# Use network installation
url --url=http://<? echo($_SERVER['SERVER_NAME']); ?>/dvd/centos/5/os/x86_64
# Network information
network --bootproto=dhcp --device=eth0 --onboot=on
# Reboot after installation
reboot
#Root password
rootpw --iscrypted $1$jrm5tnjw$h8JJ9mCZLmJvIxvDLjw1M/

# SELinux configuration
selinux --disabled
# Do not configure the X Window System
skipx
# System timezone
timezone UTC
# Install OS instead of upgrade
install

#Packages
%packages
@base
@editors
@server-cfg
@system-tools
ntp
curl
tar
-sysreport

%post
set -x
exec > /root/post.log 2>&1
#sed -i "s/HOSTNAME.*/HOSTNAME=centos64/" /etc/sysconfig/network
rpm -Uvh http://<? echo($_SERVER['SERVER_NAME']); ?>/ptb/epel-release-5-4.noarch.rpm
yum -y install git
yum -y upgrade
mkdir /usr/src
cd /usr/src
git clone git://github.com/puppetlabs/puppet.git
git clone git://github.com/puppetlabs/facter.git
git clone git://github.com/puppetlabs/marionette-collective.git mcollective
git clone git://github.com/puppetlabs/puppetlabs-training-bootstrap.git
cd /root
RUBYLIB=/usr/src/puppet/lib:/usr/src/facter/lib
export RUBYLIB
/usr/src/puppet/bin/puppet apply --modulepath=/usr/src/puppetlabs-training-bootstrap/modules --verbose /usr/src/puppetlabs-training-bootstrap/manifests/site.pp
#curl -s http://host.vm.lan/~ody/enterprise/puppet-enterprise-1.1-ubuntu-10.04-amd64.tar | tar xf -
