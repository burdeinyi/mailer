FROM vetal2409/nginx-php:7.1

MAINTAINER Andrii Burdeinyi <holden1853caulfield@gmail.com>

RUN apt-get update && apt-get -y install sendmail sendmail-cf m4

RUN rm -rf /etc/nginx/sites/*

ADD ./docker/nginx/sites /etc/nginx/sites
ADD ./docker/scripts/after /var/scripts/after

RUN echo "\n[program:mailer]" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "command=sendmail -bD" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "priority=20" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "startsecs=0" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stdout_logfile=/dev/stdout" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stdout_logfile_maxbytes=0" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stderr_logfile=/dev/stderr" >> /etc/supervisor/conf.d/supervisord.conf && \
    echo "stderr_logfile_maxbytes=0" >> /etc/supervisor/conf.d/supervisord.conf

WORKDIR /opt/app

COPY composer.json composer.lock ./
RUN composer install -no --no-scripts

COPY . .
RUN composer install -no