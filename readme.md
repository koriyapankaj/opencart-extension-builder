# OpenCart Extension Builder

OpenCart Extension Builder is a simple PHP-based console tool that allows you to easily create a new OpenCart extension by prompting you for essential extension details such as name, description, author, and more. This builder uses Symfony's Console component to provide a command-line interface.

## Features

- Generate an OpenCart extension structure quickly.
- Prompts for essential details such as extension name, author, version, etc.
- Allows you to select the OpenCart version (OpenCart 3 or OpenCart 4).
- Automatically sets up the basic structure for your OpenCart extension.

## Prerequisites

- PHP 7.2 or higher
- Composer

## Installation

Follow the steps below to install and use the OpenCart Extension Builder.

### Step 1: Clone the repository

Clone the repository to your local machine:

```bash
git clone https://github.com/koriyapankaj/opencart-extension-builder.git
cd opencart-extension-builder
```

### Step 2: Install dependencies

Use Composer to install the required dependencies:

```bash
composer install
```


### Step 3: Run the application

You can now run the OpenCart Extension Builder using the following command:

```bash
php main.php build
```

# This will start the interactive prompt where you can input the following details:

- Extension Name: The name of the extension (required).
- Extension Directory Name: The directory name for the extension.
- Extension Description: A description of the extension (required).
- Version: The version of your extension.
- Author: The name of the extension author (required).
- Link: The link to the extension's homepage or repository (required).
- OpenCart Version: Choose between "OpenCart 3" or "OpenCart 4".


After you provide the required information, the builder will generate a directory structure for your new OpenCart extension inside buld directory.



