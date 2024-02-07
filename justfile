###
# Simplify development of the Laravel application using Homestead!
# (your milage on Windows may vary…)
#
# Prerequisites:
# - vagrant
# - yq
# - Homestead (https://laravel.com/docs/10.x/homestead)
#   - features mariadb & minio must be installed and enabled
#   - additional port forwards:
#     - 5173:5173 (vite)
#     - 9600:9600 (MinIO)
#     - 9601:9601 (MinIO console)
#   - ensure that both a database and a writeable bucket are available
# - tailspin (optional; but you'll love it!)
#
# The up command will patch your Homestead.yaml to mount the correct directory
# to your Homestead VM.
#
# Create a .just.env file to configure just how you need it:
# - HOMESTEAD_PATH: path to the directory of your local Homestead repo
# - HOMESTEAD_APP_PATH: path at which you expect your app directory (this one)
#                       to be mounted within the Homestead VM (must be one of
#                       the folders mapped in Homestead.yaml at folders[].to)
###

set dotenv-load := true
set dotenv-path := '.just.env'

homestead_path := env_var_or_default('HOMESTEAD_PATH', '~/homestead')
homestead_app_path := env_var_or_default('HOMESTEAD_APP_PATH', '/home/vagrant/code')
tspin := `command -v tspin || true`

# list commands
default:
  just --list

_homestead_activate APP_DIR: (_homestead "yq -i e '(.folders[] | select(.to == \""+homestead_app_path+"\").map) |= \""+APP_DIR+"\"' Homestead.yaml; vagrant reload --provision")

_homestead COMMAND:
  pushd {{homestead_path}};\
  {{COMMAND}};\
  popd

# patch Homestead.yaml with app directory and reload VM with provisioning to apply
up: (_homestead_activate invocation_directory())

# resume Homestead instance
resume: (_homestead "vagrant resume")

# suspend Homestead instance (gnite!)
suspend: (_homestead "vagrant suspend")

# run COMMAND on Homestead instance via ssh
ssh COMMAND='': (_homestead "vagrant ssh"+(if COMMAND != '' { " -c '"+COMMAND+"'" } else { "" }))

# bring up vite on Homestead
dev: (ssh 'cd app; npm run dev -- --host 0.0.0.0')

# open MySQL shell on Homestead
mysql: (ssh 'mysql')

# tail laravel logs (using tailspin if available)
log:
  just ssh 'tail -n100 -f {{homestead_app_path}}/storage/logs/laravel.log'{{if tspin != '' { ' | '+tspin } else { '' } }}
