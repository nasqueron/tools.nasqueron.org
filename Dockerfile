#
# Nasqueron Tools image
#

FROM nasqueron/nginx-php-fpm:novolume

LABEL org.nasqueron.repository.callsign="TOOLS"
LABEL org.opencontainers.image.authors="dereckson@nasqueron.org"

#
# Prepare the container
#

WORKDIR /var/wwwroot/default

RUN git clone https://devcentral.nasqueron.org/source/tools.git . && \
    apt-get update && \
    apt-get install -y nodejs npm make libfl-dev fortunes && \
    npm install -g browserify && \
    make && \
    chown -R app:app /var/wwwroot/default && \
    cd /opt && git clone https://github.com/dereckson/valspeak.git && cd valspeak && \
    make && ln -s /opt/valspeak/valspeak /usr/bin/valspeak && \
    cd /opt && git clone https://devcentral.nasqueron.org/source/jive.git && cd jive && \
    make && ln -s /opt/jive/jive /usr/bin/jive
