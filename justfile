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

# run PHP commmands on Sail
php *ARGS:
  {{sail_path}} php {{ARGS}}

# run composer commmands on Sail
composer *ARGS:
  {{sail_path}} composer {{ARGS}}

# run artisan commmands on Sail
artisan *ARGS:
  {{sail_path}} artisan {{ARGS}}

# run NPM commmands on Sail
npm *ARGS:
  {{sail_path}} npm {{ARGS}}

# run node commmands on Sail
node *ARGS:
  {{sail_path}} node {{ARGS}}

# bring up Vite on Sail
dev: (npm "i") (npm "run dev")

