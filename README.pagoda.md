# Deploying OpenSkedge on Pagoda Box

OpenSkedge supports the use of the PaaS provider Pagoda Box and I have included a Boxfile and a Boxfile.install for the creation of OpenSkedge instances. Pagoda Box has a lot of limitations compared to a tradition style deployment platform, so deploying to Pagoda Box can be considered <em>beta</em> and <em>unstable</em>. Deployment may break occasionally. You should check for any changes to this before updating a Pagoda Box instance as manual intervention may be required to keep the instance functioning.

Due Pagoda Box's read-only nature, there are few environmental variables that need to be set:

*  SYMFONY__BRANDING__NAME should contain the desired name for the application branding. - <em>(Default: "OpenSkedge")</em>
*  SYMFONY__ADMIN__EMAIL should contain the email address of the default administrative user - <em>(Default: "admin@yourdomain.com")</em>
*  SYMFONY__WEEK__START__DAY should contain the day of the week which is considered the start of the week in your region - <em>(Default: Sunday)</em>
*  SYMFONY__WEEK__START__DAY__CLOCK should contain the day of the week which is considered the start of the week as far as time clock functionality is concerned. This will likely be the same as above. Use the same format as your paper time sheets. -  <em>(Default: Sunday)</em>
*  OPENSKEDGE_SECRET is set to an insecure secret by default. This needs to be changed. OPENSKEDGE_SECRET is used for CSRF validation. Set this to some random characters. An ideal value would be a random sha256 hash. - <em>(Default: ThisIsASecretChangeIt)</em>

Any changes to the above will take effect the next time you deploy. You can also force a redeploy of the current commit by clicking "Pick a Git Commit to Deploy" and clicking "Deploy" next to the commit that is already deployed.

Suggested Reading for more information:
http://help.pagodabox.com/customer/portal/articles/175128-symfony2
