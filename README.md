# API Documentation for Reservations

Welcome to the Reservations API documentation. This API allows you to create, read, update, and delete reservations for movie screenings.

## Getting Started

To start using this API, please follow the instructions below.

### Prerequisites

- PHP >= 8.3
- Composer
- Docker
- Laravel
- Postman (optional)

### Installation

1. Clone the repository to your local machine.
2. Run `composer install` and `npm i` to install the dependencies.
3. Copy `.env.example` to `.env` and configure your database.
4. Run `php artisan key:generate` to generate the application key.
6. Run `php artisan migrate:refresh --seed` to create the database tables and seed fake data.
7. Run `php artisan serve` to start the development server.

### Usage

The API provides the following endpoints:

#### Reservations

- `GET /api/reservations`: Retrieves all reservations.
- `GET /api/reservations/{id}`: Retrieves a reservation by its ID.
- `POST /api/reservations`: Creates a new reservation.
- `PUT /api/reservations/{id}`: Updates an existing reservation.
- `DELETE /api/reservations/{id}`: Deletes a reservation.

### Validation

Requests to create and update a **reservation** must adhere to the following validation rules:

- `seance_id`: UUID, required, must be a valid foreign key reference to `seances` table
- `seat`: integer, required
- `rank`: integer, required
- `status`: enum ('open', 'expired', 'confirmed'), required

### Test

To test the API, you can use the following methods:

#### Swagger UI
You can access the API documentation and test the endpoints directly via Swagger UI by visiting the following route:

- `/api/documentation`

#### Postman 

Download and implement in Postman folder : `reservations.postman.json` file in Postman to test the API.

- `Postman/reservations.postman.json`


### Utilities

