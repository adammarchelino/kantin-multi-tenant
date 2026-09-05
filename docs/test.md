PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                                0.01s  

   PASS  Tests\Feature\Auth\AuthenticationTest
  ✓ login screen can be rendered                                                     4.99s  
  ✓ users can authenticate using the login screen                                    0.08s  
  ✓ users can not authenticate with invalid password                                 0.02s  
  ✓ users with two factor enabled are redirected to two factor challenge             0.02s  
  ✓ users can logout                                                                 0.02s  

   PASS  Tests\Feature\Auth\EmailVerificationTest
  ✓ email verification screen can be rendered                                        0.10s  
  ✓ email can be verified                                                            0.02s  
  ✓ email is not verified with invalid hash                                          0.06s  
  ✓ already verified user visiting verification link is redirected without firing e… 0.02s  

   PASS  Tests\Feature\Auth\PasswordConfirmationTest
  ✓ confirm password screen can be rendered                                          0.56s  

   PASS  Tests\Feature\Auth\PasswordResetTest
  ✓ reset password link screen can be rendered                                       0.43s  
  ✓ reset password link can be requested                                             0.24s  
  ✓ reset password screen can be rendered                                            0.65s  
  ✓ password can be reset with valid token                                           0.24s  

   PASS  Tests\Feature\Auth\RegistrationTest
  ✓ registration screen can be rendered                                              0.47s  
  ✓ new users can register                                                           0.02s  

   PASS  Tests\Feature\Auth\TwoFactorChallengeTest
  ✓ two factor challenge redirects to login when not authenticated                   0.02s  
  ✓ two factor challenge can be rendered                                             0.02s  

   PASS  Tests\Feature\DashboardTest
  ✓ guests are redirected to the login page                                          0.02s  
  ✓ authenticated users can visit the dashboard                                      0.01s  

   PASS  Tests\Feature\ExampleTest
  ✓ returns a successful response                                                    0.13s  

   PASS  Tests\Feature\Settings\ProfileUpdateTest
  ✓ profile page is displayed                                                        2.12s  
  ✓ profile information can be updated                                               0.08s  
  ✓ email verification status is unchanged when email address is unchanged           0.04s  
  ✓ user can delete their account                                                    0.03s  
  ✓ correct password must be provided to delete account                              0.34s  

   PASS  Tests\Feature\Settings\SecurityTest
  ✓ security settings page can be rendered                                           3.39s  
  ✓ security settings page requires password confirmation when enabled               0.02s  
  ✓ security settings page renders without two factor when feature is disabled       0.03s  
  ✓ two factor authentication disabled when confirmation abandoned between requests  0.03s  
  ✓ password can be updated                                                          0.06s  
  ✓ correct password must be provided to update password                             0.05s  

   PASS  Tests\Feature\TenantAccessTest
  ✓ guest redirected to login on tenant route                                        0.02s  
  ✓ verified user can open tenant dashboard                                          0.54s  

  Tests:    35 passed (84 assertions)
  Duration: 15.11s


C:\ServBay\www\kantin-multi-tenant>