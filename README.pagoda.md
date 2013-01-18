# Deploying OpenSkedge on Pagoda Box

OpenSkedge supports the use of the PaaS provider Pagoda Box and I have included a Boxfile and a Boxfile.install for the creation of OpenSkedge instances.

Due Pagoda Box's read-only nature, there are few environmental variables that need to be set:

SYMFONY__BRANDING__NAME should contain the desired name for the application branding. (Default: "OpenSkedge")
SYMFONY__ADMIN__EMAIL should contain the email address of the default administrative user (Default: "admin@yourdomain.com")

parameters:secret is set to an insecure secret. This needs to be changed. `secret` is used for CSRF validation. Set this to some random characters. An ideal value would be a random sha256 hash.

Suggested Reading for more information:
http://help.pagodabox.com/customer/portal/articles/175128-symfony2
