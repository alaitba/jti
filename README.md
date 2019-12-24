[![build status](https://gitlab.ibecsystems.kz/web/jti/badges/master/build.svg)](https://gitlab.ibecsystems.kz/web/jti/commits/master)
[![Bugs](https://sonarqube.ibecsystems.kz:9001/api/project_badges/measure?project=jti&metric=bugs)](https://sonarqube.ibecsystems.kz:9001/dashboard?id=jti)
[![Code Smells](https://sonarqube.ibecsystems.kz:9001/api/project_badges/measure?project=jti&metric=code_smells)](https://sonarqube.ibecsystems.kz:9001/dashboard?id=jti)
[![Coverage](https://sonarqube.ibecsystems.kz:9001/api/project_badges/measure?project=jti&metric=coverage)](https://sonarqube.ibecsystems.kz:9001/dashboard?id=jti)
[![Duplicated Lines (%)](https://sonarqube.ibecsystems.kz:9001/api/project_badges/measure?project=jti&metric=duplicated_lines_density)](https://sonarqube.ibecsystems.kz:9001/dashboard?id=jti)
[![Lines of Code](https://sonarqube.ibecsystems.kz:9001/api/project_badges/measure?project=jti&metric=ncloc)](https://sonarqube.ibecsystems.kz:9001/dashboard?id=jti)
[![Maintainability Rating](https://sonarqube.ibecsystems.kz:9001/api/project_badges/measure?project=jti&metric=sqale_rating)](https://sonarqube.ibecsystems.kz:9001/dashboard?id=jti)
[![Quality Gate Status](https://sonarqube.ibecsystems.kz:9001/api/project_badges/measure?project=jti&metric=alert_status)](https://sonarqube.ibecsystems.kz:9001/dashboard?id=jti)
[![Reliability Rating](https://sonarqube.ibecsystems.kz:9001/api/project_badges/measure?project=jti&metric=reliability_rating)](https://sonarqube.ibecsystems.kz:9001/dashboard?id=jti)
[![Security Rating](https://sonarqube.ibecsystems.kz:9001/api/project_badges/measure?project=jti&metric=security_rating)](https://sonarqube.ibecsystems.kz:9001/dashboard?id=jti)
[![Technical Debt](https://sonarqube.ibecsystems.kz:9001/api/project_badges/measure?project=jti&metric=sqale_index)](https://sonarqube.ibecsystems.kz:9001/dashboard?id=jti)
[![Vulnerabilities](https://sonarqube.ibecsystems.kz:9001/api/project_badges/measure?project=jti&metric=vulnerabilities)](https://sonarqube.ibecsystems.kz:9001/dashboard?id=jti)

# [jti](https://jti.kz)


### Application

Backend

|  Т            |    В       |
| ---------     | -----:     |
| Laravel       |   6.6.2    |
| StarterKit    |   3.2.x    |

### Серверное ПО

|  Т            |    В       |
| ---------     | -----:     |
| PHP           |   7.3.9    |
| MySQL         |   5.7.27   |
| Nginx         |   1.16.1   |

# УСТАНОВКА
#### 1. Клонируем проект
```code
git clone git@gitlab.ibecsystems.kz:skritku/jti.git
```

#### 2. Копируем env.example и создаем .env
```code
cp .env.example .env
```
#### 3. Заходим в проект и устанавливаем зависимости
```code
composer install && php artisan key:generate
```
#### 4. Запускаем миграции
```code
php artisan migrate
```

#### 5. Запускаем сиды если нужны демо данные.
```code
php artisan db:seed
```

#### P.S
[Документация к проекту](docs)

*DevOps last audit [24.11.2019][13:00] - Rishat Sultanov*