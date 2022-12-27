# figuren.theater | Security

Security related components for a WordPress Multisite plattform like [figuren.theater](https://figuren.theater).

---

## Plugins included

This package contains the following plugins. 
Thoose are completely managed by code and lack of their typical UI.

* [Email Address Encoder](https://wordpress.org/plugins/email-address-encoder/#developers)
* [Limit Login Attempts Reloaded](https://wordpress.org/plugins/limit-login-attempts-reloaded/#developers)
* [Passwords Evolved](https://wordpress.org/plugins/passwords-evolved/#developers)
* [WP Author Slug](https://wordpress.org/plugins/wp-author-slug/#developers)
* [WPS Hide Login](https://wordpress.org/plugins/wps-hide-login/#developers)

## What this does?

Accompaniying the core functionality of the mentioned plugins, theese **best practices** are included with this package.

- Do not allow weak passwords!
- Require strong passwords, with
  - At least 8 characters in length.
  - The user name and password can’t be the same.
  - Passwords must have at least one number, one lowercase character, one uppercase and one symbol.
- Send Emails via SMTP.

