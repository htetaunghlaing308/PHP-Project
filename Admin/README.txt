SIGNAGEPRO BUSINESS SYSTEM
==========================

Requirements:
- XAMPP (Apache + MySQL)
- PHP 8.x recommended

INSTALL:
1. Extract the "signage-business" folder into:
   C:\xampp\htdocs\

2. Start Apache and MySQL in XAMPP.

3. Open phpMyAdmin:
   http://localhost/phpmyadmin

4. Import:
   signage.sql

5. Open:
   http://localhost/signage-business/login.php

Default login:
Username: admin
Password: password

The login page includes a show/hide password button.
The interface uses a premium white + green business theme.
Company Settings is available in the sidebar.

FEATURES:
- Dashboard
- Customer CRUD
- Material and price management
- Multiple signage items per quotation
- Width x Height x Quantity x Price/sq.ft
- Installation
- Delivery
- Discount
- Tax / other percentage
- Automatic quotation number
- Quotation database/history
- View quotation
- Print / Save as PDF
- Responsive interface

IMPORTANT:
Change the default admin password before using this system for real business data.
For production hosting, update database.php with your hosting database credentials and use HTTPS.


Branding:
The uploaded Bridge Advertising & Printing logo is installed at bridge-logo.png and is shown on the login page and sidebar.


Theme:
Light/Dark mode is available from the top-right Theme button and the selection is remembered in the browser. The interface uses a richer green accent palette.

RECENT UPDATES
- Quotation header now displays the company logo, company name, phone, and address from Company Settings.
- Company Settings includes a Login Account section for changing the username and password from the UI.
- Sign In page is available at /signage-business/login.php and uses the updated account credentials.
- Password changes require the current password and new passwords are stored securely with password_hash().
- Dashboard theme toggle remains available on the Dashboard only.
