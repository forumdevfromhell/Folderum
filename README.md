# 📁 Folderum — Everything Is A Folder Edition

> **A forum engine for people who looked at MySQL, PostgreSQL and SQLite and said:**
>
> **"what if `mkdir` was the database?"**

Welcome to **Folderum**, a PHP forum I apparently wrote after deciding databases were a conspiracy invented by Big Table.

There is no SQL. There is no SQLite. There is no JSON database. There are no `.txt` files containing application data.

There are only **folders**.

Users? Folders. Passwords? Folder names. Roles? Folders. Bans? Folders. Forums? Folders. Threads? Folders. Posts? More folders. Votes? Believe it or not, folders. Post contents? Encoded, chopped into pieces, and turned into **even more fucking folders**.

I have not solved data storage. I have **weaponized ext4**.

---

## ⚠️ BEFORE YOU INSTALL THIS ABOMINATION

**THIS IS A JOKE PROJECT.**

Do not expose this edition to the public Internet with real users. Do not use a password you have ever used anywhere else. Do not use this for your company, school, nuclear reactor, banking platform, or anything whose continued existence you value.

This edition **INTENTIONALLY STORES PASSWORDS IN PLAINTEXT DIRECTORY NAMES.**

If Alice registers with:

```text
username: alice
password: hunter2
```

Folderum proudly creates:

```text
forum-data/
└── users/
    └── alice/
        ├── password_hunter2/
        ├── role_user/
        └── created_2026-09-12_00-30-00/
```

That is not a typo. That is the authentication system.

Login essentially asks:

> "Does `password_hunter2/` exist?"

If yes: welcome back, Alice.

I am deeply sorry.

---

## 🧠 The Entire Database Schema

There isn't one.

The filesystem **is** the schema.

A user might look like:

```text
forum-data/
└── users/
    └── drunkadmin/
        ├── password_beer123/
        ├── role_admin/
        └── created_2026-09-12_00-30-00/
```

Being administrator means `role_admin/` exists.

Being banned means `banned/` exists.

Votes:

```text
votes/
├── up/
│   ├── alice/
│   ├── bob/
│   └── greg/
└── down/
    └── steve/
```

Score:

```text
number of directories in up/
-
number of directories in down/
```

Post bodies are base64url-encoded, chopped into chunks, and stored as ordered directory names.

Yes.

**I implemented a string datatype using directories.**

---

## 🍺 Dependencies

Folderum wants a boring Linux + Apache + PHP setup so the application itself can be as stupid as possible.

Recommended:

- Debian/Ubuntu
- Apache 2.4+
- PHP 8.0+ — required because the code uses `str_starts_with()`
- `libapache2-mod-php`
- `unzip`
- optionally `tree`
- a filesystem with many innocent inodes
- poor judgment

Install the actual software:

```bash
sudo apt update
sudo apt install -y apache2 php libapache2-mod-php unzip
```

Optional, but extremely useful for admiring the damage:

```bash
sudo apt install -y tree
```

Start Apache:

```bash
sudo systemctl enable --now apache2
```

Check PHP:

```bash
php -v
```

Check Apache:

```bash
sudo systemctl status apache2
```

If both are alive, congratulations. We may now begin abusing directories.

---

## 🚀 Install Folderum At `/var/www/html/folderum`

This build expects:

```text
/var/www/html/folderum/
```

and therefore:

```text
http://YOUR-SERVER/folderum/
```

Extract the ZIP:

```bash
cd /var/www/html
sudo unzip /path/to/folderum-everything-is-folders.zip
```

It extracts as:

```text
/var/www/html/folderum-everything-is-folders/
```

Rename it:

```bash
sudo mv /var/www/html/folderum-everything-is-folders /var/www/html/folderum
```

You should now have roughly:

```text
/var/www/html/folderum/
├── index.php
├── login.php
├── register.php
├── logout.php
├── forum.php
├── thread.php
├── vote.php
├── profile.php
├── admin/
├── includes/
├── assets/
├── forum/
└── forum-data/
```

---

## 🔐 Permissions — Give PHP Permission To Commit The Crime

The PHP source does **not** need to be writable.

Start sane:

```bash
sudo chown -R root:www-data /var/www/html/folderum
sudo find /var/www/html/folderum -type d -exec chmod 755 {} \;
sudo find /var/www/html/folderum -type f -exec chmod 644 {} \;
```

Folderum needs write access to the two locations where its directory civilization reproduces:

```text
/var/www/html/folderum/forum/
/var/www/html/folderum/forum-data/
```

Give those to Apache/PHP:

```bash
sudo chown -R www-data:www-data /var/www/html/folderum/forum
sudo chown -R www-data:www-data /var/www/html/folderum/forum-data

sudo find /var/www/html/folderum/forum -type d -exec chmod 750 {} \;
sudo find /var/www/html/folderum/forum-data -type d -exec chmod 750 {} \;

sudo find /var/www/html/folderum/forum -type f -exec chmod 640 {} \;
sudo find /var/www/html/folderum/forum-data -type f -exec chmod 640 {} \;
```

Do **not** fix permission problems with:

```bash
chmod -R 777 /var/www/html/folderum
```

Folderum may be drunk, but it is not *that* drunk.

---

## 🧪 Test The Database Connection

There is no database server.

Therefore our enterprise database connectivity test is:

```bash
sudo -u www-data mkdir /var/www/html/folderum/forum-data/fuck_yeah_it_writes
```

Check it:

```bash
ls -la /var/www/html/folderum/forum-data/
```

Rollback our transaction:

```bash
sudo -u www-data rmdir /var/www/html/folderum/forum-data/fuck_yeah_it_writes
```

Test forum storage too:

```bash
sudo -u www-data mkdir /var/www/html/folderum/forum/test_category
sudo -u www-data rmdir /var/www/html/folderum/forum/test_category
```

If those work, the "database" is operational.

God help us.

---

## 🌐 First Launch

Open:

```text
http://YOUR-SERVER/folderum/
```

Register:

```text
http://YOUR-SERVER/folderum/register.php
```

The **first registered account automatically becomes admin**.

For:

```text
username: bro
password: beer123
```

you should eventually see:

```text
forum-data/
└── users/
    └── bro/
        ├── password_beer123/
        ├── role_admin/
        └── created_.../
```

Folderum has appointed a database administrator without having a database.

---

## 🔑 Password Rules

Because the password is literally part of a directory name, passwords accept only:

```text
A-Z
a-z
0-9
_
-
```

Length:

```text
6–64 characters
```

Works:

```text
beer123
correct_horse
password-but-worse
```

Does not:

```text
what/the/fuck
hello world
../../etc/passwd
```

The restriction is not because I suddenly developed standards.

It is because `/` has already suffered enough.

---

## 🗂️ Creating Forum Categories Manually

The forum hierarchy literally lives at:

```text
/var/www/html/folderum/forum/
```

Create nested categories:

```bash
sudo -u www-data mkdir -p "/var/www/html/folderum/forum/Technology/Programming/PHP"
```

Folderum sees:

```text
Technology
└── Programming
    └── PHP
```

There is no migration.

There is no `INSERT INTO categories`.

You ran `mkdir`.

You are now a backend engineer.

The admin UI at:

```text
/folderum/admin/forums.php
```

does approximately the same thing, but with buttons so it looks employable.

---

## 👑 Make Somebody Admin By Hand

Inspect Alice:

```bash
ls -la /var/www/html/folderum/forum-data/users/alice/
```

If she currently has:

```text
role_user/
```

remove it:

```bash
sudo -u www-data rmdir /var/www/html/folderum/forum-data/users/alice/role_user
```

Promote her using our cutting-edge RBAC platform:

```bash
sudo -u www-data mkdir /var/www/html/folderum/forum-data/users/alice/role_admin
```

Refresh Folderum.

The Admin link appears.

We have implemented privilege escalation using `mkdir`.

To demote Alice:

```bash
sudo -u www-data rmdir /var/www/html/folderum/forum-data/users/alice/role_admin
sudo -u www-data mkdir /var/www/html/folderum/forum-data/users/alice/role_user
```

The civilized version is:

```text
/folderum/admin/users.php
```

but SSH makes the architecture look much more upsetting.

---

## 🔨 Ban A User

Ban Alice:

```bash
sudo -u www-data mkdir /var/www/html/folderum/forum-data/users/alice/banned
```

That's it.

The existence of:

```text
banned/
```

means:

```text
BANNED = TRUE
```

There is no boolean.

There is only ontology.

---

## 🕊️ Unban A User

Delete the concept of being banned:

```bash
sudo -u www-data rmdir /var/www/html/folderum/forum-data/users/alice/banned
```

Alice has been forgiven by the filesystem.

---

## 🔒 Lock A Thread

Threads live under:

```text
forum/
└── Technology/
    └── PHP/
        └── _threads/
            └── THREAD_ID/
```

Find `_threads` directories:

```bash
find /var/www/html/folderum/forum -type d -name "_threads" -print
```

Inspect one:

```bash
ls -la "/var/www/html/folderum/forum/Technology/PHP/_threads/"
```

Suppose the ID is:

```text
0123456789abcdef0123456789abcdef
```

Lock it:

```bash
sudo -u www-data mkdir "/var/www/html/folderum/forum/Technology/PHP/_threads/0123456789abcdef0123456789abcdef/locked"
```

Folderum sees `locked/` and refuses new replies.

No `locked = 1`.

No UPDATE query.

Just a folder standing in the doorway saying **no**.

---

## 🔓 Unlock A Thread

```bash
sudo -u www-data rmdir "/var/www/html/folderum/forum/Technology/PHP/_threads/0123456789abcdef0123456789abcdef/locked"
```

Democracy has been restored.

---

## 👍 Manually Upvote Someone Because Apparently We Can

A post contains:

```text
votes/
├── up/
└── down/
```

An upvote from Alice is literally:

```text
votes/up/alice/
```

Given the correct post path:

```bash
sudo -u www-data mkdir "/path/to/post/votes/up/alice"
```

Remove it:

```bash
sudo -u www-data rmdir "/path/to/post/votes/up/alice"
```

Turn it into a downvote:

```bash
sudo -u www-data rmdir "/path/to/post/votes/up/alice"
sudo -u www-data mkdir "/path/to/post/votes/down/alice"
```

The revolutionary scoring algorithm:

```text
count(up directories) - count(down directories)
```

I expect my ACM award in the mail.

---

## 📝 How Post Bodies Work

"But wait," you scream, "a directory name cannot hold an entire forum post."

Correct.

Folderum solves this problem in the least responsible way possible.

Text is:

1. base64url encoded
2. split into chunks
3. numbered
4. each chunk becomes a directory

Conceptually:

```text
body/
├── 000000_SGVsbG8gZnJvbSBGb2xkZXJ1bQ/
├── 000001_bW9yZSB0ZXh0IGhlcmU/
└── 000002_YW5kIGV2ZW4gbW9yZQ/
```

PHP scans those directories in order, joins the encoded chunks, decodes them, and displays the post.

Folderum has reinvented:

```text
VARCHAR
```

as:

```text
a suspicious number of inodes
```

---

## ⚙️ Settings

Settings can also be represented by existence:

```text
forum-data/settings/maintenance_mode/
forum-data/settings/registration_disabled/
```

The admin settings page creates/removes these flags.

**Important:** in this joke build those flags are stored by the admin panel, but the public request flow does not currently enforce both of them everywhere. They demonstrate the storage model; do not treat them as production security controls.

That may be the most professionally honest sentence in this README.

---

## 🛡️ Apache Protection

The project ships `.htaccess` files under:

```text
forum/
forum-data/
```

with:

```apache
Require all denied
```

Those are server configuration files, not application data.

If your Apache configuration ignores `.htaccess`, edit:

```bash
sudo nano /etc/apache2/apache2.conf
```

For `/var/www/`, make sure overrides are permitted:

```apache
<Directory /var/www/>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

Check Apache configuration:

```bash
sudo apache2ctl configtest
```

Reload:

```bash
sudo systemctl reload apache2
```

Trying to browse:

```text
http://YOUR-SERVER/folderum/forum-data/
```

should result in **Forbidden**, not a gorgeous public directory listing containing your catastrophic authentication system.

For anything remotely serious, storage should live outside the web root.

For this edition, anything remotely serious should use different software.

---

## 🩺 HTTP 500? Excellent. Here Is Where The Fire Is.

Watch Apache's error log:

```bash
sudo tail -f /var/log/apache2/error.log
```

Lint every PHP file:

```bash
find /var/www/html/folderum -name '*.php' -print0 | xargs -0 -n1 php -l
```

Inspect permissions:

```bash
ls -la /var/www/html/folderum/
ls -la /var/www/html/folderum/forum/
ls -la /var/www/html/folderum/forum-data/
```

Test PHP's write access:

```bash
sudo -u www-data mkdir /var/www/html/folderum/forum-data/permission_test
sudo -u www-data rmdir /var/www/html/folderum/forum-data/permission_test
```

If that fails:

```bash
sudo chown -R www-data:www-data /var/www/html/folderum/forum
sudo chown -R www-data:www-data /var/www/html/folderum/forum-data
```

Restart Apache if you have been changing modules/config:

```bash
sudo systemctl restart apache2
```

---

## 🔍 Observe The Disaster Directly

Users:

```bash
tree /var/www/html/folderum/forum-data/users
```

Forum hierarchy:

```bash
tree /var/www/html/folderum/forum
```

Everything:

```bash
tree /var/www/html/folderum/forum-data
```

Find bans:

```bash
find /var/www/html/folderum/forum-data/users -type d -name banned
```

Find admins:

```bash
find /var/www/html/folderum/forum-data/users -type d -name role_admin
```

Find locked threads:

```bash
find /var/www/html/folderum/forum -type d -name locked
```

Who needs SQL queries when `find` exists?

---

## 📁 "YOU SAID EVERYTHING IS FOLDERS BUT I SEE `.htaccess`"

Correct.

The `.htaccess` files are **Apache configuration**, not application data.

Folderum itself does not write user/forum data into files.

Check for application-created files while ignoring the Apache guards:

```bash
find /var/www/html/folderum/forum /var/www/html/folderum/forum-data \
    -type f -not -name '.htaccess'
```

After using Folderum, the desired output is:

```text
absolutely fucking nothing
```

If you want the visual gag to be **literally zero files under the data roots**, move the deny rules into your Apache VirtualHost configuration first, then remove:

```bash
sudo rm /var/www/html/folderum/forum/.htaccess
sudo rm /var/www/html/folderum/forum-data/.htaccess
```

Do not remove them until Apache itself denies direct HTTP access to those directories.

---

## 💾 Backups

Somehow this part is almost reasonable.

Back up the "database":

```bash
sudo tar -czf folderum-backup.tar.gz \
    /var/www/html/folderum/forum \
    /var/www/html/folderum/forum-data
```

Restore by extracting the directories again.

We accidentally rediscovered filesystem backups.

Please do not let this success encourage me.

---

## 🧹 Uninstall

If you have regained consciousness:

```bash
sudo rm -rf /var/www/html/folderum
```

Congratulations.

Your filesystem is safe now.

---

## 🏆 Folderum Engineering Principles

1. If a boolean can be a directory, it will be a directory.
2. If a string cannot fit in a directory name, use more directories.
3. If SQL could solve the problem, pretend SQL was never invented.
4. If `mkdir()` can solve the problem, stop thinking immediately.
5. The inode table is basically Redis if you are sufficiently irresponsible.
6. A missing folder is `false`.
7. An existing folder is `true`.
8. Authentication is a scavenger hunt.
9. `tree` is the database administration client.
10. **Everything. Is. Folders.**

---

## Final Warning, Because Apparently One Wasn't Enough

Folderum is intentionally terrible software built around an intentionally terrible storage model.

It exists because this:

```bash
mkdir banned
```

being a complete moderation operation is objectively funny.

Use it locally. Use throwaway passwords. Make a video about it. Show it to a database engineer and watch their soul leave their body.

But please, for the love of all writable filesystems:

**do not use Folderum: Everything Is A Folder Edition for anything that matters.**
