FROM nginx:alpine

MAINTAINER Nguyen Ngoc Vinh <ngocvinh.nnv@gmail.com>

COPY ./mime.types /ect/nginx/mime.types
COPY ./nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

STOPSIGNAL SIGTERM

CMD ["nginx", "-g", "daemon off;"]
