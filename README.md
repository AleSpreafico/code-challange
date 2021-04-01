## Readme
After cloning repo run `cd repo && cp .env.example .env`

### Install Dependencies
```bash
docker run --rm \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php80-composer:latest \
    composer install
```

### Run Application & Tests
```bash
vendor/bin/sail up -d
vendor/bin/sail artisan migrate:fresh

# optional, just create a user
vendor/bin/sail artisan db:seed

# run tests
vendor/bin/sail artisan test
```
