<p  align="center"><a  href="https://support.pacom.com"  target="_blank"><img  src="https://pacom.com/wp-content/uploads/2024/08/PACOM_Main-Black_-Blue-1.svg"  width="400"  alt="Pacom Portal"></a></p>

## Pacom Portal
A Laravel-based web application developed for internal use.


### Requirements

- `Laragon` - Development environment for PHP
	 -- Installation guide - https://laragon.org/docs/install
	 
- `Composer` - A Dependency Manager for PHP
	 -- Direct Download Link - https://getcomposer.org/Composer-Setup.exe

- `VS Code`
	-- Direct Download Link - https://code.visualstudio.com/sha/download?build=stable&os=win32-x64-user

###  Installation
```bash
# Open Command Prompt

# Navigate to your laragon "www" directory
cd C:/laragon/www

# Clone the project
git clone https://github.com/jayflores129/pacomportal_production.git

# Navigate to "pacomportal_production" directory
cd pacomportal_production

# Install PHP independencies
composer install

# Copy .env.example and generate app key
cp .env.example .env
php artisan key:generate

# Open the project in VS Code
code .
```

### Configuration 

Create database in Laragon and name it `pacom_db`

```bash 
# Update database credentials in ".env" file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=pacom_db
DB_USERNAME=root
DB_PASSWORD=
```
### Serve the App

In VS code open terminal or `Ctrl + J`

```bash
php artisan serve
```
Access the app at [http://localhost:8000](http://localhost:8000)

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).