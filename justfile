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

# mount app directory to Homestead and reload VM with provisioning
up: (_homestead_activate invocation_directory())

# resume Homestead instance
resume: (_homestead "vagrant resume")

# suspend Homestead instance (gnite!)
suspend: (_homestead "vagrant suspend")

# run COMMAND on Homestead instance via ssh
ssh COMMAND='': (_homestead "vagrant ssh"+(if COMMAND != '' { " -c '"+COMMAND+"'" } else { "" }))

# bring up vite on Homestead
dev: (ssh 'cd app; yarn dev --host 0.0.0.0')

# open MySQL shell on Homestead
mysql: (ssh 'mysql')

# tail laravel logs (using tailspin if available)
log:
  just ssh 'tail -n100 -f {{homestead_app_path}}/storage/logs/laravel.log'{{if tspin != '' { ' | '+tspin } else { '' } }}
