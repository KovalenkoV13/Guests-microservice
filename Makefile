run:
	docker-compose up -d && docker exec app php artisan l5-swagger:generate
build:
	docker-compose up --build -d && docker exec app php artisan migrate && docker exec app php artisan l5-swagger:generate
delete with volumes:
	docker-compose down -v
stop:
	docker-compose down
