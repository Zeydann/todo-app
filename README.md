# To-Do List App

Simple to-do list web app built with PHP and MySQL, containerized with Docker, and deployed behind Nginx + Tailscale Funnel.

## Tech Stack
- PHP 8.2 (Apache)
- MySQL 8.0
- Docker & Docker Compose
- Nginx (reverse proxy)
- Tailscale Funnel (public HTTPS access)

## Features
- Add, complete, and delete tasks
- Persistent storage via MySQL
- Fully containerized setup

## Running Locally

1. Clone this repo
2. Copy `.env.example` to `.env` and fill in a database password:
```bash
   cp .env.example .env
```
3. Build and run:
```bash
   docker compose up -d --build
```
4. Access at `http://localhost:8080`

## Project Structure
- `index.php` — main app logic & UI
- `config.php` — database connection
- `init.sql` — database schema
- `Dockerfile` — PHP + Apache container definition
- `docker-compose.yml` — multi-container orchestration (web + db)
