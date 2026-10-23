# syntax=docker/dockerfile:1

# ---- Tahap 1: build dengan Node.js ----
FROM node:22-alpine AS build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
ARG VITE_API_URL=/api
ENV VITE_API_URL=$VITE_API_URL
RUN npm run build

# ---- Tahap 2: sajikan hasil build dengan Nginx ----
FROM nginx:1.27-alpine
LABEL org.opencontainers.image.source="https://github.com/rizwanhamka/uts-pl-24-539823-sv-24684"
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=build /app/dist /usr/share/nginx/html
EXPOSE 80
HEALTHCHECK --interval=10s --timeout=5s --retries=5 \
  CMD wget -q -O /dev/null http://127.0.0.1/ || exit 1
