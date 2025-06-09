---
[![SonarQube Cloud](https://sonarcloud.io/images/project_badges/sonarcloud-light.svg)](https://sonarcloud.io/summary/new_code?id=hpmouton_web-dev-hpmouton)

[!](https://res.cloudinary.com/dvol642yc/image/upload/v1749460036/rate_zykui3.png)
# Rate Checker App

This app helps users easily check accommodation rates. Just pick a unit, your dates, and how many people are staying, and it'll show you the price!

---

##  What It Does

* **Checks Rates:** Gets prices for different rooms or units.
* **Handles Guests:** You can add multiple people and their ages.
* **Shows Details:** Displays total cost, unit info, dates, and a breakdown of charges.
* **Easy to Use:** Simple forms and clear results.

---

##  How It Works

* **Backend:** Built with **Laravel** (PHP) to handle requests and talk to an external rate API.
* **Frontend:** Uses **Alpine.js** (JavaScript) for a responsive and interactive user interface, styled with **Tailwind CSS**.

---

##  Get Started

Here's how to set up the app on your computer:

1.  **Clone the project:**
    ```bash
    git clone your-repository-url.git
    cd your-project-folder # Adjust this to your folder name
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Set up your environment:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    Then, open `.env` and set `APP_RATES_API_ENDPOINT` to your actual external rate API URL.

4.  **Install frontend stuff:**
    ```bash
    npm install
    ```

5.  **Build frontend assets:**
    ```bash
    npm run dev # or npm run build for production
    ```

6.  **Start the server:**
    ```bash
    php artisan serve
    ```
    Now, visit `http://localhost:8000` (or your configured URL) in your browser.

---

##  How to Use

1.  **Select a unit** (e.g., "Dessert Whisperer").
2.  **Choose your arrival and departure dates.**
3.  **Enter the total number of occupants.**
4.  **Input each guest's age.**
5.  Click **"Get Rates"**.

The app will then show you the estimated total charge and a detailed breakdown.

---

##  Testing

You can run the tests to make sure everything's working correctly:

```bash
./vendor/bin/pest
```

---
