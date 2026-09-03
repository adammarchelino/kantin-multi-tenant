Windows PowerShell
Copyright (C) Microsoft Corporation. All rights reserved.

PS D:\POLIWANGI\Semester 3\Pemrograman Web Lanjut\Tugas\kantin-multi-tenant> php artisan test

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                0.01s

   PASS  Tests\Feature\Auth\AuthenticationTest
  ✓ login screen can be rendered                                     3.06s
  ✓ users can authenticate using the login screen                    0.05s
  ✓ users can not authenticate with invalid password                 0.02s
  ✓ users with two factor enabled are redirected to two factor chal… 0.02s
  ✓ users can logout                                                 0.02s

   PASS  Tests\Feature\Auth\EmailVerificationTest
  ✓ email verification screen can be rendered                        0.26s
  ✓ email can be verified                                            0.02s
  ✓ email is not verified with invalid hash                          0.09s
  ✓ already verified user visiting verification link is redirected…  0.02s

   PASS  Tests\Feature\Auth\PasswordConfirmationTest
  ✓ confirm password screen can be rendered                          0.75s

   PASS  Tests\Feature\Auth\PasswordResetTest
  ✓ reset password link screen can be rendered                       0.66s
  ✓ reset password link can be requested                             0.22s
  ✓ reset password screen can be rendered                            0.85s
  ✓ password can be reset with valid token                           0.22s

   PASS  Tests\Feature\Auth\RegistrationTest
  ✓ registration screen can be rendered                              0.02s
  ✓ new users can register                                           0.02s

   PASS  Tests\Feature\Auth\TwoFactorChallengeTest
  ✓ two factor challenge redirects to login when not authenticated   0.02s
  ✓ two factor challenge can be rendered                             0.01s

   PASS  Tests\Feature\DashboardTest
  ✓ guests are redirected to the login page                          0.01s
  ✓ authenticated users can visit the dashboard                      0.02s

   PASS  Tests\Feature\ExampleTest
  ✓ returns a successful response                                    0.02s

   PASS  Tests\Feature\Settings\ProfileUpdateTest
  ✓ profile page is displayed                                        3.19s
  ✓ profile information can be updated                               0.11s
  ✓ email verification status is unchanged when email address is un… 0.04s
  ✓ user can delete their account                                    0.02s
  ✓ correct password must be provided to delete account              0.18s

   PASS  Tests\Feature\Settings\SecurityTest
  ✓ security settings page can be rendered                           5.30s
  ✓ security settings page requires password confirmation when enab… 0.02s
  ✓ security settings page renders without two factor when feature…  0.03s
  ✓ two factor authentication disabled when confirmation abandoned…  0.02s
  ✓ password can be updated                                          0.04s
  ✓ correct password must be provided to update password             0.04s

  Tests:    33 passed (81 assertions)
  Duration: 15.99s

PS D:\POLIWANGI\Semester 3\Pemrograman Web Lanjut\Tugas\kantin-multi-tenant> ./vendor/bin/pint --test

  .........⨯......................................

  ──────────────────────────────────────────────────────────────── Laravel
    FAIL   ....................................... 48 files, 1 style issue
  ⨯ bootstrap\app.php                                          line_ending

PS D:\POLIWANGI\Semester 3\Pemrograman Web Lanjut\Tugas\kantin-multi-tenant> ./vendor/bin/pint

  .........✓......................................

  ──────────────────────────────────────────────────────────────── Laravel
    FIXED   ................................ 48 files, 1 style issue fixed
  ✓ bootstrap\app.php                                          line_ending

PS D:\POLIWANGI\Semester 3\Pemrograman Web Lanjut\Tugas\kantin-multi-tenant> ./vendor/bin/pint --test

  ................................................

  ──────────────────────────────────────────────────────────────── Laravel
    PASS   ...................................................... 48 files

PS D:\POLIWANGI\Semester 3\Pemrograman Web Lanjut\Tugas\kantin-multi-tenant> npm run build

> build
> vite build

vite v8.2.2 building client environment for production...
[plugin laravel:fonts] Optimized font fallbacks require the optional "fontaine" package. Install it, or set "optimizedFallbacks: false" on your fonts to disable the feature.
✓ 25 modules transformed.
computing gzip size...
public/build/manifest.json                                       1.64 kB │ gzip:  0.36 kB
public/build/fonts-manifest.json                                 5.74 kB │ gzip:  0.71 kB
public/build/assets/instrument-sans-400-normal-DRC__1Mx.woff2   16.86 kB
public/build/assets/instrument-sans-500-normal-Dk9ku72i.woff2   17.23 kB
public/build/assets/instrument-sans-600-normal-B7fBEWYG.woff2   17.40 kB
public/build/assets/instrument-sans-400-normal-D1W7dsQl.woff    21.24 kB
public/build/assets/instrument-sans-500-normal-Z6ESRlEs.woff    21.65 kB
public/build/assets/instrument-sans-600-normal-B9e8oLYv.woff    21.67 kB
public/build/assets/fonts-C9MNnjVw.css                           2.35 kB │ gzip:  0.38 kB
public/build/assets/app-CgWKrUcr.css                           255.36 kB │ gzip: 34.54 kB
public/build/assets/passkeys-ZWACIUjA.js                        12.08 kB │ gzip:  3.90 kB
public/build/assets/app-D1i-PwPQ.js                             73.33 kB │ gzip: 20.85 kB

✓ built in 577ms
PS D:\POLIWANGI\Semester 3\Pemrograman Web Lanjut\Tugas\kantin-multi-tenant>