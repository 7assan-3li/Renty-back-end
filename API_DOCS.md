# Renty Back-End API Documentation

BASE URL: `http://localhost:8000/api` (or your server URL)

All responses are in JSON format.
Errors generally return `{ "message": "Error description" }` with appropriate status codes (400, 401, 404, 500).

## 1. Authentication (Public)

### Register

- **Method**: `POST`
- **Endpoint**: `/register`
- **Token Required**: No
- **Body**:
    ```json
    {
        "name": "John Doe",
        "email": "john@example.com",
        "password": "password123",
        "password_confirmation": "password123",
        "phone": "0501234567"
    }
    ```

* **Validation Rules**:
    - `name`: `required`, `string`, `max:255`
    - `email`: `required`, `string`, `email`, `max:255`, `unique:users`
    - `password`: `required`, `string`, `min:8`, `confirmed` (must match `password_confirmation`)
    - `phone`: `required`, `string`, `max:255`

- **Description**: Creates a new user account and sends an OTP to the email. user status will be unverified.

### Verify OTP

- **Method**: `POST`
- **Endpoint**: `/verify-otp`
- **Token Required**: No
- **Body**:
    ```json
    {
        "email": "john@example.com",
        "otp": "123456"
    }
    ```

* **Validation Rules**:
    - `email`: `required`, `email`
    - `otp`: `required`, `string`

- **Description**: Verifies the email address. Needed after registration.

### Resend OTP

- **Method**: `POST`
- **Endpoint**: `/resend-otp`
- **Token Required**: No
- **Body**:
    ```json
    {
        "email": "john@example.com"
    }
    ```
- **Description**: Resends a new OTP to the email.

### Login

- **Method**: `POST`
- **Endpoint**: `/login`
- **Token Required**: No
- **Body**:
    ```json
    {
        "email": "john@example.com",
        "password": "password123"
    }
    ```

* **Validation Rules**:
    - `email`: `required`, `email`
    - `password`: `required`, `string`

- **Description**: Authenticates the user and returns a Bearer Token.
- **Response**:
    ```json
    {
      "access_token": "1|abcdef...",
      "token_type": "Bearer",
      "user": { ... }
    }
    ```

### Forgot Password

- **Method**: `POST`
- **Endpoint**: `/forgot-password`
- **Token Required**: No
- **Body**:
    ```json
    {
        "email": "john@example.com"
    }
    ```
- **Description**: Sends a password reset OTP to the email.

### Reset Password

- **Method**: `POST`
- **Endpoint**: `/reset-password`
- **Token Required**: No
- **Body**:
    ```json
    {
        "email": "john@example.com",
        "otp": "123456",
        "password": "newpassword123",
        "password_confirmation": "newpassword123"
    }
    ```

* **Validation Rules**:
    - `email`: `required`, `email`, `exists:users`
    - `otp`: `required`, `string`
    - `password`: `required`, `string`, `min:8`, `confirmed`

- **Description**: Resets the user's password using the OTP.

---

## 2. User Profile (Protected)

**Requires Header**: `Authorization: Bearer <token>`

### Get User Details

- **Method**: `GET`
- **Endpoint**: `/user`
- **Token Required**: Yes
- **Description**: Returns the currently authenticated user's details.

### Update Profile

- **Method**: `PUT`
- **Endpoint**: `/profile`
- **Token Required**: Yes
- **Body**: (All fields optional)
    ```json
    {
        "name": "John Doe Updated",
        "phone": "0509876543",
        "email": "john.new@example.com"
    }
    ```

* **Validation Rules**:
    - `name`: `sometimes`, `string`, `max:255`
    - `phone`: `sometimes`, `string`, `max:255`
    - `email`: `sometimes`, `email`, `unique:users,email,{id}`

- **Description**: Updates user profile information.

### Change Password

- **Method**: `POST`
- **Endpoint**: `/profile/password`
- **Token Required**: Yes
- **Body**:
    ```json
    {
        "current_password": "oldpassword",
        "new_password": "newpassword123",
        "new_password_confirmation": "newpassword123"
    }
    ```

* **Validation Rules**:
    - `current_password`: `required`
    - `new_password`: `required`, `string`, `min:8`, `confirmed`

- **Description**: Changes the logged-in user's password.

### Logout

- **Method**: `POST`
- **Endpoint**: `/logout`
- **Token Required**: Yes
- **Description**: Invalidates the current token.

---

## 3. Categories (Protected)

**Requires Header**: `Authorization: Bearer <token>`

### List Categories

- **Method**: `GET`
- **Endpoint**: `/categories`
- **Token Required**: Yes
- **Description**: Returns a list of all vehicle categories.

### Show Category

- **Method**: `GET`
- **Endpoint**: `/categories/{id}`
- **Token Required**: Yes
- **Description**: Returns details of a specific category.

---

## 4. Cars (Protected)

**Requires Header**: `Authorization: Bearer <token>`

### List Cars (With Filters)

- **Method**: `GET`
- **Endpoint**: `/cars`
- **Token Required**: Yes
- **Query Parameters** (Optional):
    - `category_id`: Filter by category ID (e.g., `?category_id=1`).
    - `search`: Search name/model (e.g., `?search=Camry`).
    - `min_price`: Filter by minimum daily price.
    - `max_price`: Filter by maximum daily price.
- **Description**: Returns a list of cars matching the filters. Includes `is_favorited` boolean.

### Show Car

- **Method**: `GET`
- **Endpoint**: `/cars/{id}`
- **Token Required**: Yes
- **Description**: Returns details of a specific car, including description, features, and images.

### Get Favorites

- **Method**: `GET`
- **Endpoint**: `/cars/favorites`
- **Token Required**: Yes
- **Description**: Returns a list of cars favorited by the user.

### Toggle Favorite

- **Method**: `POST`
- **Endpoint**: `/cars/{id}/favorite`
- **Token Required**: Yes
- **Description**: Adds or removes the car from the user's favorites.
- **Response**: `{ "attached": [], "detached": [1] }` (Laravel toggle response structure, or simple success message).

---

## 5. Bookings & Payments (Protected)

**Requires Header**: `Authorization: Bearer <token>`

### Create Booking (Initiate Payment)

- **Method**: `POST`
- **Endpoint**: `/book`
- **Token Required**: Yes
- **Body**:
    ```json
    {
        "car_id": 1,
        "start_date": "2024-05-01",
        "end_date": "2024-05-05",
        "latitude": 24.7136, // Optional user location
        "longitude": 46.6753 // Optional user location
    }
    ```

* **Validation Rules**:
    - `car_id`: `required`, `exists:cars,id`
    - `start_date`: `required`, `date`, `after_or_equal:today`
    - `end_date`: `required`, `date`, `after:start_date`
    - `latitude`: `nullable`, `numeric`
    - `longitude`: `nullable`, `numeric`

- **Description**: Creates a pending booking and initiates a Stripe PaymentIntent.
- **Response**:
    ```json
    {
        "message": "Booking initiated successfully",
        "booking_id": 123,
        "client_secret": "pi_1234567890_secret_abcdef", // Needed for Stripe Frontend SDK to confirm payment
        "amount": 500.0,
        "currency": "usd"
    }
    ```
    _Note: The Frontend must use `client_secret` with Stripe SDK to confirm payment. Backend handles webhook/confirmation logic._
<!-- 
### Confirm Payment (Manual)

- **Method**: `POST`
- **Endpoint**: `/bookings/{id}/confirm`
- **Token Required**: Yes
- **Body**:
    ```json
    {
        "payment_intent_id": "pi_1234567890..." // ID received from Stripe SDK after success
    }
    ```
- **Validation Rules**:
    - `payment_intent_id`: `required`, `string`
- **Description**: Verifies the payment with Stripe and updates the booking status to PAID. Call this after Stripe SDK returns success. -->

### List My Bookings

- **Method**: `GET`
- **Endpoint**: `/bookings`
- **Token Required**: Yes
- **Description**: Returns a list of all bookings made by the current user, ordered by latest.

### Rate Booking

- **Method**: `POST`
- **Endpoint**: `/bookings/{id}/rate`
- **Token Required**: Yes
- **Body**:
    ```json
    {
        "rating": 5 // Integer 1-5
    }
    ```

* **Validation Rules**:
    - `rating`: `required`, `integer`, `min:1`, `max:5`

- **Description**: Submits a rating for a specific booking. Can only be done once per booking.
