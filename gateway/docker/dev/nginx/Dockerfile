FROM nginx:1.17-alpine

COPY ./common/nginx/snippets /etc/nginx/snippets
COPY ./dev/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app