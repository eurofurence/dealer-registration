###
# Simplify development of the Laravel application using Sail!
#
# Prerequisites:
# - docker & compose
# - yq
# - tailspin (optional; but you'll love it!)
#
###

set dotenv-load := true
sail_path := env_var_or_default('SAIL_PATH', './vendor/bin/sail')
tspin := `command -v tspin || true`

# list commands
default:
  just --list

# bring Sail up (make sure to run `dev` in a separate shell for Vite)
up:
  {{sail_path}} up

# rebuild Sail images without caching
build:
  {{sail_path}} build --no-cache

# run PHP commands on Sail
php *ARGS:
  {{sail_path}} php {{ARGS}}

# run composer commands on Sail
composer *ARGS:
  {{sail_path}} composer {{ARGS}}

# run artisan commands on Sail
artisan *ARGS:
  {{sail_path}} artisan {{ARGS}}

# run NPM commands on Sail
npm *ARGS:
  {{sail_path}} npm {{ARGS}}

# run node commands on Sail
node *ARGS:
  {{sail_path}} node {{ARGS}}

# bring up Vite on Sail
dev: (npm "i") (npm "run dev")

# build production-ready container
nightly-build *ARGS:
  docker build --tag ghcr.io/eurofurence/dealer-registration:nightly {{ARGS}} .

# bring up stack using nightly container build
nightly-up *ARGS:
  docker compose -f docker-compose.nightly.yml up {{ARGS}}

# bring up stack using nightly container build
nightly-down *ARGS:
  docker compose -f docker-compose.nightly.yml down {{ARGS}}

