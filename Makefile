php-fpm: ##@other port: 8001
	cd "docker/$@/" && docker-compose up -d

php-preload: ##@other port: 8002
	cd "docker/$@/" && docker-compose up -d

react-php: ##@other port: 8003
	cd "docker/$@/" && docker-compose up -d

road-runner: ##@other port: 8004
	cd "docker/$@/" && docker-compose up -d

swoole: ##@other port: 8005
	cd "docker/$@/" && docker-compose up -d

all: php-fpm php-preload react-php road-runner swoole

php-fpm-stop:
	cd "docker/php-fpm/" && docker-compose down

php-preload-stop:
	cd "docker/php-preload/" && docker-compose down

react-php-stop:
	cd "docker/react-php/" && docker-compose down

road-runner-stop:
	cd "docker/road-runner/" && docker-compose down

swoole-stop:
	cd "docker/swoole/" && docker-compose down

all-stop: php-fpm-stop php-preload-stop react-php-stop road-runner-stop swoole-stop
