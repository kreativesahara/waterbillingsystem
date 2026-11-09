<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Response;

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            if (Auth::isAdmin()) {
                Response::redirect('/admin/dashboard');
            } else {
                Response::redirect('/tenant/dashboard');
            }
        }

        $error = $_GET['error'] ?? null;
        $success = $_GET['success'] ?? null;

        Response::view('auth/login', [
            'error' => $error,
            'success' => $success,
        ], 'auth');
    }

    public function login(): void
    {
        $phone = trim($_POST['phonenumber'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'TENANT'; // 'ADMIN' or 'TENANT'

        if (empty($phone) || empty($password)) {
            Response::redirect('/login?error=' . urlencode('Please provide both phone number and password.'));
        }

        // Look up consumer
        $user = Database::fetchOne(
            "SELECT * FROM consumers WHERE phonenumber = ? AND role = ? LIMIT 1",
            [$phone, $role]
        );

        if (!$user || !Auth::verifyPassword($password, $user['password_hash'])) {
            Response::redirect('/login?error=' . urlencode('Invalid credentials for ' . strtolower($role) . ' profile.'));
        }

        Auth::login($user);

        if ($user['role'] === 'ADMIN') {
            Response::redirect('/admin/dashboard');
        } else {
            Response::redirect('/tenant/dashboard');
        }
    }

    public function logout(): void
    {
        Auth::logout();
        Response::redirect('/login?success=' . urlencode('You have been logged out successfully.'));
    }

    public function showRegister(): void
    {
        // Fetch vacant houses or houses without tenants
        $houses = Database::fetchAll("SELECT * FROM houses ORDER BY CAST(house_number AS UNSIGNED) ASC");

        $error = $_GET['error'] ?? null;
        Response::view('auth/register', [
            'houses' => $houses,
            'error' => $error,
        ], 'auth');
    }

    public function register(): void
    {
        $houseId = (int)($_POST['house_id'] ?? 0);
        $firstName = trim($_POST['firstname'] ?? '');
        $lastName = trim($_POST['lastname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phonenumber'] ?? '');
        $nationalId = trim($_POST['national_id'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($firstName) || empty($lastName) || empty($phone) || empty($password)) {
            Response::redirect('/register?error=' . urlencode('All required fields must be filled.'));
        }

        if ($password !== $confirmPassword) {
            Response::redirect('/register?error=' . urlencode('Passwords do not match.'));
        }

        // Check for phone duplicate
        $existing = Database::fetchOne("SELECT id FROM consumers WHERE phonenumber = ?", [$phone]);
        if ($existing) {
            Response::redirect('/register?error=' . urlencode('A user with this phone number already exists.'));
        }

        $passwordHash = Auth::hashPassword($password);

        Database::query(
            "INSERT INTO consumers (house_id, firstname, lastname, email, phonenumber, national_id, password_hash, role)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'TENANT')",
            [$houseId ?: null, $firstName, $lastName, $email, $phone, $nationalId, $passwordHash]
        );

        $newUserId = Database::lastInsertId();
        $user = Database::fetchOne("SELECT * FROM consumers WHERE id = ?", [$newUserId]);

        Auth::login($user);
        Response::redirect('/tenant/dashboard');
    }
}
