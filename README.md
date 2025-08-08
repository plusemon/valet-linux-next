# Valet Linux Next

Valet Linux Next is a development environment for Linux, inspired by Laravel Valet. It provides a fast and convenient way to serve your PHP applications locally with Nginx and Dnsmasq.

## Features

- **Zero-configuration:** Automatically serves your projects.
- **Nginx Integration:** Uses Nginx for high-performance web serving.
- **Dnsmasq for DNS:** Provides `.test` domain resolution out of the box.
- **PHP-FPM Support:** Seamlessly integrates with PHP-FPM for PHP application execution.
- **Interactive Uninstaller:** Allows selective removal of Valet components.

## Requirements

Before installing Valet Linux Next, ensure you have the following installed on your system:

- **PHP (with PHP-FPM):** Valet Linux Next is designed to work with PHP-FPM. Ensure you have a PHP version (e.g., PHP 8.4) and its FPM component installed.
- **Nginx:** A high-performance HTTP server.
- **Dnsmasq:** A lightweight DHCP and DNS caching server.
- **`sudo` privileges:** Installation and uninstallation require root privileges to configure system services and files.
- **`git`:** For cloning the repository.

## Installation Guide

Follow these steps to install Valet Linux Next on your system:

1.  **Clone the Repository:**

    ```bash
    git clone https://github.com/plusemon/valet-linux-next.git
    cd valet-linux-next
    ```

2.  **Install Composer Dependencies:**

    ```bash
    composer install
    ```

3.  **Run the Valet Installer:**
    This command will install and configure Nginx, Dnsmasq, PHP-FPM (if not already installed), set up Valet directories, and symlink the `valet` executable to `/usr/local/bin`.

    ```bash
    sudo ./valet install
    ```

    During installation, you might be prompted for your `sudo` password.

4.  **Verify Installation:**
    After installation, you can check the status of Valet services:
    ```bash
    valet status
    ```
    You should see Nginx, Dnsmasq, and PHP-FPM reported as `Running`.

## Usage

### Parking Your Projects

To "park" a directory, navigate into it and run the `park` command. This will tell Valet to serve any subdirectories within it as individual sites.

```bash
cd ~/Projects/MyAwesomeProject
valet park
```

Now, any subdirectories within `~/Projects/MyAwesomeProject` (e.g., `~/Projects/MyAwesomeProject/my-site`) will be accessible at `http://my-site.test`.

### Listing Linked Sites

To see all currently parked directories and the sites within them:

```bash
valet links
```

### Uninstalling Valet Linux Next

To uninstall Valet Linux Next and revert its changes, run the `uninstall` command. This command is interactive and will ask for confirmation before performing each step.

```bash
sudo ./valet uninstall
```

## Contributing

We welcome contributions to Valet Linux Next! If you have a bug report, feature request, or want to submit a pull request, please follow these guidelines:

1.  **Fork the repository.**
2.  **Create a new branch** for your feature or bug fix.
3.  **Write clear, concise, and well-documented code.**
4.  **Ensure your changes adhere to the existing coding style.**
5.  **Write tests** for new features or bug fixes where applicable.
6.  **Submit a pull request** with a detailed description of your changes.

## License

Valet Linux Next is open-sourced software licensed under the [MIT license](LICENSE).
