# Setup and run tests with docker compose

## Install
- Install Docker CE
- Install docker-compoe
- Add your user to docker group `sudo usermod -aG docker ${USER}`
- Relogin to apply new group

## Run
- From `phpgwapi/doc/docker/` run `docker-compose up --force-recreate`
- On first initial run, download and startup will take 5 mins