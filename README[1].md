# LEAPHIGHER Faculty Attendance System (PHP + MySQL)

## Included
- Admin and Faculty login
- Admin can create time-limited QR attendance sessions
- Admin can configure the authorized campus GPS coordinates and radius
- Faculty scans the session QR code using the device camera
- Server validates login, QR token, server time, GPS distance and duplicate attendance
- Admin can view attendance records

## Requirements
- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Apache (XAMPP is suitable)
- Browser camera and location permission; use HTTPS in production (localhost is okay for development)

## Setup with XAMPP
1. Copy the `LEAPHIGHER_Faculty_Attendance_System` folder into `C:\xampp\htdocs\`.
2. Start Apache and MySQL in XAMPP.
3. Open `http://localhost/phpmyadmin`.
4. Import `database.sql`.
5. Edit `config.php` if your MySQL username/password/database differ.
6. Open `http://localhost/LEAPHIGHER_Faculty_Attendance_System/setup.php` once.
7. Setup creates:
   - Admin ID: `admin`
   - Admin password: `Admin@123`
   - Demo faculty ID: `FAC001`
   - Demo faculty password: `Faculty@123`
8. **Immediately change these demo passwords** before using the system.
9. Delete `setup.php` after successful setup.
10. Open `http://localhost/LEAPHIGHER_Faculty_Attendance_System/`.

## Configure campus location
Sign in as admin. Use the campus settings form to save your actual campus latitude, longitude and permitted radius in metres. Do not leave the default coordinates (0, 0).

## Create and use attendance
1. Admin creates a QR session with a subject, start time and end time.
2. The QR appears on the admin dashboard.
3. Faculty signs in, opens Scan QR and scans the displayed QR code.
4. Faculty must allow camera and location access.
5. The server checks the current server time against the session time and checks distance from the configured campus coordinates.
6. A successful scan is recorded once per faculty member per session.

## Notes
- QR attendance is for faculty check-in. Student attendance marking is not included in this starter version.
- Browser GPS is a location signal, not strong anti-spoofing proof. For higher assurance, combine it with campus Wi-Fi/network controls or managed devices.
- QR session tokens are random, but production deployment should use HTTPS, backups, rate limiting and account-management workflows.
