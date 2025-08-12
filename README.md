# 📧 Bulk Email Account Creator (cPanel PHP Web UI)

A simple PHP-based web tool that lets you create multiple email accounts in **cPanel** at once using a clean web-based interface.  
This script uses the **cPanel UAPI** (`Email::add_pop`) and allows you to bulk-create email accounts by entering usernames in a list.  

---

## 🚀 Features
- Web-based form — no command-line needed
- Create unlimited accounts in one click
- Set **one password** for all accounts
- Specify mailbox quota in MB
- Works with any cPanel server supporting UAPI
- Displays API response for each account

---

## 📷 Screenshot
*(Add screenshot here)*

---

## 📦 Installation & Usage
1. **Upload** the `index.php` file to your hosting account or local PHP server.
2. Open it in your browser:  
3. Fill in:
- cPanel username
- cPanel password
- cPanel host (e.g., `yourdomain.com`)
- Email domain (e.g., `yourdomain.com`)
- Common password for all accounts
- Mailbox size (in MB)
- Usernames (one per line, without `@domain`)
4. Click **Create Accounts**.
5. See the results for each created account.

---

## ⚠ Security Notice
This tool **requires your cPanel credentials**. To keep it secure:
- Restrict access by IP or password-protect the file.
- Run it only in a trusted environment.
- Delete the script after use to avoid unauthorized access.

---

## 🛠 Requirements
- PHP 7+ with `curl` enabled
- Access to cPanel with API permissions
- Web browser to access the UI

---

## 📜 License
MIT License — free to use and modify.  

---

## 🤝 Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you’d like to change.

---

## ⭐ Support
If you like this project, give it a ⭐ on GitHub!
