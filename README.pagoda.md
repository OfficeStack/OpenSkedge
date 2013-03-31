# Deploying OpenSkedge on Pagoda Box

OpenSkedge supports the use of the PaaS provider Pagoda Box and I have included a Boxfile and a Boxfile.install for the creation of OpenSkedge instances. Pagoda Box has a lot of limitations compared to a tradition style deployment platform, so deploying to Pagoda Box can be considered <em>beta</em> and <em>unstable</em>. Deployment may break occasionally. You should check for any changes to this before updating a Pagoda Box instance as manual intervention may be required to keep the instance functioning.

Due Pagoda Box's read-only nature, there are few environmental variables that need to be set:

*  SYMFONY__SENDER__EMAIL should contain the email address of the no-reply email account from which automated emails should be sent. - <em>(Default: "no-reply@yourdomain.com")</em>
*  OPENSKEDGE_SECRET is set to an insecure secret by default. This needs to be changed. OPENSKEDGE_SECRET is used for CSRF validation. Set this to some random characters. An ideal value would be a random sha256 hash. - <em>(Default: ThisIsASecretChangeIt)</em>
*  SYMFONY__MEMCACHE__EXPIRE should contain the number of seconds a memcache session should last. - <em>(Default: 3600)</em>

Any changes to the above will take effect the next time you deploy. You can also force a redeploy of the current commit by clicking "Pick a Git Commit to Deploy" and clicking "Deploy" next to the commit that is already deployed.

Suggested Reading for more information:
http://help.pagodabox.com/customer/portal/articles/175128-symfony2
