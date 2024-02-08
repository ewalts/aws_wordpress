# aws_wordpress
Ansible deployment WordPress Docker MySQL EC2

#rewbin - AWS Wordpress Site deployment 2.0.4
Wordpress-Docker/MysQL

## Ansible playbook for docker and mysql server deployment
- Ubuntu instance 22.04
- MySQL 8.x
- Docker 6.23

## Latest version 2.0.4 
This version of rewbin wordpress deployment uses one instance. Wordpress is containerized with Docker, MySQL runs natively.
This deployment will create everything in AWS.

# Changes to this version:
- Docker's data root space has been moved to the second disk.

- Mounts a second partition where the following are stored.
  - MySQL data
  - Docker data root *New*
  - Docker build docs
  - Wordpress site content


## Running the deployment
```$ ansible-playbook -i inventory.yml playbook/deployment_install.yml
```
  - which will import tasks from:
      tasks/aws_iam.yml

## deployment_install.yml also includes three other plays

- ubuntu_server_setup.yml
  - which will import tasks from:
      tasks/packages.yml,
      tasks/configure_docker.yml

- install_mysql.yml
  - which will import tasks from:
      tasks/configure_mysql.yml

- wordpress_compose.yml 


# Mysql checks ---
- Is mysql server and client installed?
- Is mysql server running and accepting connections?
- Has the data volume been created?
- Has data volume been attached/mounted to the instance?
- Has the original data been migrated?
- Has the new db been created?
- Has the new db been populated?
- Has the user been created for the new db?

# Wordpress checks ---
- Is it responding normally?
- Is the container running?
- Is the security group associated/configured?
- Local firewall rules?

