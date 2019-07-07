FROM alpine:latest
RUN apk update --no-cache
RUN apk upgrade --no-cache
RUN apk add --no-cache php7 php7-fpm php7-opcache php7-gd php7-mysqli php7-zlib php7-curl php7-bcmath php7-phar php7-json php7-mbstring php7-ctype php7-xml php7-dom php7-iconv php7-simplexml php7-xmlreader php7-xmlwriter php7-zip php7-phar php7-session php7-fileinfo
RUN apk add --no-cache git curl

CMD ["php-fpm7","-F","-R"]
