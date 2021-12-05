<h1>API testing and Validation</h1>

##Description
<p>How to validate your API from the consumer point of view, and test 
to confirm that problems experienced by your end users are being solved</p>

## Links
Behat, Composer are installed in the parent project directory.
Here are the links to install them both;
- https://docs.behat.org/en/latest/quick_start.html#installation
- https://getcomposer.org/download/

## Built With

- Windows OS 10 Pro
- Command Line (I used PowerShell)
- MAMP (install MAMP first, then PHP storm or your favorite editor)
- PHP 7.x , please note that PHP 5 is deprecated for production use. Please upgrade to PHP 7 and above.
- HTTP requests library (probably Guzzle)
- Behat 3.x and above**
- Composer
- Useful Toos: Xdebug, Postman, Runscope, $YourAPI SDK

## Available Commands

In your parent project directory run the following commands:

Install the composer package manager. The easiest way to do that is to go to their
download page and copy the code and paste it into your terminal (Command Line) from
their website. Here is a snippet of the code;

### `php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"`
`php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"`
`php composer-setup.php`
`php -r "unlink('composer-setup.php');"`

<br>
Next your need to install Behat in your project parent directory as well

### `php composer.phar require behat/behat 3.* `,

<br>
Now we need to check to see behat is installed correctly

### `vendor/bin/behat -V`

To prepare your workspace to actually write tests, you'll need to run behat --init

### `vendor/bin/behat --init`

For running the server file on you can use this command.

### `npm run serve`



###API Testing and Validation
In the behat.yaml file, replace username and password with other-authentication-methods.
Take note of the spaces in the yaml file. These should be not modified.
Read more about yaml file syntax online.

GitHub has disabled Basic Auth for API Authentication since Nov 5th, 2020.
https://docs.github.com/en/rest/overview/other-authentication-methods#basic-authentication

In order for this to work, you'll need to configure with the API token.
https://docs.github.com/en/github/authenticating-to-github/keeping-your-account-and-data-secure/creating-a-personal-access-token.
I used the generated token and my Github username and all works fine.

