# Микросервис для работы с гостями
Данный микросервис реализует API для CRUD операций над гостями. Использовалась DDD архитектура с паттерном "Репозиторий". Ниже приведена структура приложения. 

```
└── app/                   
    └── Domain/           
    |    └── Guest/
    |        └── Application/ 
    |        |    ├── GuestService.php/     
    |        └── Domain/
    |        |    ├── Guest.php
    |        |    └── GuestRepositoryInterface.php                 
    |        └── Infrastructure/
    |            └── GuestRepository.php             
    └── Infrastructure/        
        ├── Http/           
        ├── Models/     
        └── Providers/             
```

Данная структура позволяет расширять приложение и разделять логику приложения.

## Используемые технологии
- ЯП - PHP 8.3
- Фреймворк - Laravel 11
- База данных - PostgresSQL

Также присутсвует веб-сервер Nginx, который взаимодействует с приложением с помощью FastCGI сервера. Веб-сервер проксирует запросы на микросервис.

## Сборка и запуск

1. Чтобы собрать и запустить микросервис:
```
make build
```
Данная комманда поднимает докер контейнер с базой данных, веб-сервером и приложение.

2. Чтобы запустить микросервис без сборки:
```
make run
```
Данная команда поднимает уже собранные образы.

3. Чтобы опустить контейнеры без удаление томов:
```
make stop
```
4. Чтобы опустить контейнеры и удалить тома:
```
make delete with volumes
```

## Swagger

Чтобы посмотреть API документацию нужно перейте по адресу:
```
http://127.0.0.1:8080/api/documentation
```
## Валидация данных
Данные валидируется с помощью встроенных методов Laravel

Пример: валидация данных для записи информации о госте:
```php
class StoreGuestRequest extends FormRequest
{
    //Указываются правила
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => 'required|email|unique:guest,email',
            'phone'      => ['required', new Phone, 'unique:guest,phone'],
        ];
    }

    //Сообщение при нарушение правила 
    public function messages()
    {
        return [
            'first_name.required' => ['required' => 'First name is require'],
            'last_name.required' => ['required' => 'Last name is require'],
            'email.required' => ['required' => 'Email is require'],
            'email.email' => ['email' => 'Email is invalid format'],
            'email.unique' => ['unique' => 'Email is already exist'],
            'phone.required' => ['required' => 'Phone is require'],
            'phone.unique' => ['unique' => 'Phone is already exist'],
            'phone' => ['phone' => ['phone' => 'Phone is invalid format']]
        ];
    }

    // Ответ, если валидация провалилась
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 400)
        );
    }
}
```

Обработка случая, когда не указана страна, происходит в бизнесс-логике приложения:
```php
// На вход получаем номер телефона
 private function getCountryByPhoneCode(string $phone): string
    {
        // С помощью библиотеки реализуем поиск страны
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        $numberProto = $phoneNumberUtil->parse($phone, null);
        $regionCode = $phoneNumberUtil->getRegionCodeForNumber($numberProto); // Получаем код страны "RU"
    
        return \Locale::getDisplayRegion('-' . $regionCode, 'en'); // Преобразуем код страны в название страны "RU" => "Russia"
    }
```

## Docker
Dockerfile с многостадийной сборкой. Образ приложения для сборки: php:8.3-fpm. Созданы тома и сеть для нормального функционирования.