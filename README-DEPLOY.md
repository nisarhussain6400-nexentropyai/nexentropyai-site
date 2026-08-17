# Deploying this to nexentropyai.ca on Hostinger

## Files in this folder
- `index.html` — your site, with the booking form now wired to a real backend
- `process-booking.php` — handles form submissions, creates the invite link, sends the email
- `session.php` — the page the invite link opens (where the avatar call will live)
- `leads/` — where booking submissions are stored (as JSON files) + `.htaccess` blocking public access to them
- `hero-background.png` — your existing hero image (re-add this from your current repo; not duplicated here)

## Steps

1. **Log in to Hostinger → hPanel → File Manager** (or use FTP/FileZilla with your Hostinger FTP credentials).
2. Go to `public_html` (this is the root folder that serves nexentropyai.ca).
3. Upload these files:
   - `index.html` (overwrite the existing one)
   - `process-booking.php`
   - `session.php`
   - the `leads/` folder (including its `.htaccess`)
   - keep your existing `hero-background.png` where it already is
4. Set folder permissions on `leads/` to `755` (File Manager → right-click → Permissions) so PHP can write new lead files into it.
5. **Test it:**
   - Go to `https://nexentropyai.ca/#book`, fill out the form with your own email, and submit.
   - Check your inbox for the invite email.
   - Click the link — it should open `session.php?token=...` and show "Welcome, [your name]".

## About email delivery
Hostinger shared hosting sends mail from your domain by default via PHP's `mail()` function, so this should work out of the box. If emails land in spam or don't arrive:
- Check Hostinger's hPanel → Emails → make sure your domain's mail/DNS (SPF/DKIM) records are set up (Hostinger usually configures this automatically for domains bought through them).
- If delivery is unreliable, switch to an SMTP-based sender like **PHPMailer + Resend/SendGrid SMTP** instead of PHP's built-in `mail()` — I can build that version too if you want.

## Next step: the actual avatar
`session.php` currently has a placeholder box where the live avatar call goes. Once you pick a provider (Tavus, D-ID, or HeyGen are the main options for a real-time conversational video avatar), their embed snippet drops into that marked spot, initialized with the visitor's name/email/industry so your avatar can open the conversation with context.
