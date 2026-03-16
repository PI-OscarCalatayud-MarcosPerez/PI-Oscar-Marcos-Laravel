# MOKeys Technical Documentation

## 1. Project Architecture

MOKeys is a decoupled E-commerce application built with:

-   **Backend**: Laravel 11 API (PHP 8.2+)
-   **Frontend**: Vue.js 3 + Pinia + Vite
-   **Database**: MySQL 8.0
-   **Infrastructure**: Dockerized for local development, deployable to AWS (EC2/RDS).

### Key Components
-   **Auth**: Laravel Sanctum (Token-based) + Google OAuth2 (Socialite).
-   **API**: RESTful endpoints with standard resource controllers.
-   **Validation**: Backend (`FormRequest`) and Frontend (`Vee-Validate`).
-   **Storage**: Local storage for development, S3-compatible for production (configurable).

## 2. Local Development Setup

### Prerequisites
-   Docker & Docker Compose
-   Node.js 20+ (optional, if running frontend locally without Docker)

### Installation
1.  **Clone the repository**:
    ```bash
    git clone https://github.com/your-repo/mokeys.git
    cd mokeys
    ```

2.  **Environment Setup**:
    -   Copy `.env.example` to `.env` in both root (if applicable) and `frontend/`.
    -   Configure `DB_`, `GOOGLE_`, and `VITE_API_BASE_URL` variables.

3.  **Start with Docker**:
    ```bash
    docker compose up -d --build
    ```

4.  **Run Migrations & Seed**:
    ```bash
    docker compose run --rm backend php artisan migrate:fresh --seed
    ```

5.  **Access**:
    -   Frontend: `http://localhost:5173`
    -   Backend API: `http://localhost:8000`
    -   Phpmymyadmin: `http://localhost:8080`

## 3. Features Implementation

### C1. External Integration (OAuth2)
-   Implemented using `laravel/socialite`.
-   Routes: `/api/auth/google/redirect` & `/callback`.
-   Frontend handles the token callback and stores it in Pinia.

### C2. API Documentation
-   *Note: Swagger generation encountered configuration issues in the final sprint but the structure is prepared for `l5-swagger`.*

### C3. Advanced Frontend
-   **Validation**: Implemented using `vee-validate` and `yup` schemas.
-   **State Management**: Pinia stores for `Product`, `Cart`, `Auth`, `Ui`.

### C5. Recommendations
-   **Logic**: Suggests 3 random products from the same category.
-   **Endpoint**: `GET /api/products/{id}/recommendations`.

### C6. Sustainability
-   **Eco Badge**: Products have an `is_eco` boolean flag.
-   **UI**: Displays "🌿 Eco" badge on eco-friendly products.

## 4. Deployment Pipeline (CI/CD)

The project includes GitHub Actions workflows:

-   **Backend (`deploy-backend.yml`)**:
    -   Runs PHPUnit tests.
    -   Builds Docker image -> AWS ECR.
    -   Deploys to AWS EC2 via SSH (pulls image, runs migrations).

-   **Frontend (`deploy-frontend.yml`)**:
    -   Builds static assets (`npm run build`).
    -   Deploys to AWS S3 (Static Website) OR copies to EC2 Nginx webroot.
