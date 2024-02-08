
##############################################################################################
###> New Dockerfile  -> Richard Eric Walts as eric ---> 2024-01-24_15:16:37 init <<<
##############################################################################################
#_#>

FROM wordpress

WORKDIR /var/www/html

VOLUME /data0/docker/wordpress

ARG WORDPRESS_DB_USER
ARG WORDPRESS_DB_PASSWORD
ARG WORDPRESS_DB_DATABASE
ARG WORDPRESS_DB_PORT
ARG WORDPRESS_DB_HOST

#ENV DEBIAN_FRONTEND=$DEBIAN_FRONTEND
#ENV WORDPRESS_DB_ROOT_PASSWORD=$WORDPRESS_DB_ROOT_PASSWORD
ENV WORDPRESS_DB_USER=$WORDPRESS_DB_USER
ENV WORDPRESS_DB_PASSWORD=$WORDPRESS_DB_PASSWORD
ENV WORDPRESS_DB_NAME=$WORDPRESS_DB_DATABASE
ENV WORDPRESS_DB_HOST=172.18.0.1

###>  expose ports
EXPOSE 80 443
 
