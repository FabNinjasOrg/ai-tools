FROM node:20-slim AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM nginx:stable-alpine

WORKDIR /var/www/html

COPY --from=assets /app/public /var/www/html/public

RUN rm -rf /var/www/html/public/storage \
    && ln -s /var/www/html/storage/app/public /var/www/html/public/storage

COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
