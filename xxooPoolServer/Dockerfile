FROM centos:7


RUN yum install -y epel-release \
    && rpm -ivh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm \
    && yum install -y php72w php72w-pdo php72w-mysql php72w-pecl-redis php72w-opcache php72w-fpm \
    && yum install -y nginx crontabs \
    && mkdir -p /var/www/xxoo

COPY ./ /var/www/xxoo
COPY ./nginx.conf /etc/nginx/nginx.conf
CMD cd /var/www/xxoo \
    && chmod +x ./env.sh \
    && ./env.sh \
    && php data.php init \
    && /usr/sbin/php-fpm \
    && /usr/sbin/nginx -g "daemon off;"