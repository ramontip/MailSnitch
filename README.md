<div>
  <h2>MailSnitch 📬</h2>
  <p>
    This is a simple web tool that sends you an email if websites contain or do not contain certain phrases.
    You can use it for example to check if your favorite sneaker is available for purchase on specific online shops.
  </p>
  <p>
    <a href="#Installation">Installation</a>
    ·
    <a href="#Cronjob">Cronjob</a>
    .
    <a href="#License">License</a>
  </p>
</div>

### Installation

1. Clone the repo
   ```sh
   git clone https://github.com/ramontip/MailSnitch.git
   ```
2. Rename `config.example.php` to `config.php`
3. Edit the main config in `config.php` to fit your needs
    ```php
    /**
     * Main Config
     */
    'main' => [
        'user_agent_lang'   =>      'en-En,en',
        'mail_from'         =>      'mailsnitch@your-domain.example',
        'mail_to' => [
            'ramon@your-domain.example',
        ]
    ],
    ```
4. Create your own actions in `config.php`
    ```php
    /**
     * Register Actions
     */
    'actions' => [
        /**
         * Example Action
         * Parse online shops and check if sneaker is available for purchase.
         */
        [
            /**
             * Parameters
             * Insert details for action.
             */
            'key'           =>      'nike',
            'title'         =>      'Check Nike Air Jordan 1 availability',
            'success'       =>      'Nike Air Jordan 1 is available',
            'error'         =>      'not available',

            /**
             * Checks
             * Define urls, phrases and if they should be contained on the page or not.
             */
            'checks' => [
                ['https://first-shop.example/product/nike-air-jordan-1/', 'Add to Basket', true],
                ['https://second-shop.example/product/x3134fw/nike-air-jordan-1/', 'Coming Soon', false],
            ]
        ],
    ],
    ```
This example action shows the two different ways to check urls:
- The first one checks whether the url **contains** the phrase **Add to Basket**.
- The second one checks whether the url **does not contain** the phrase **Coming Soon**.

If one of the checks inside an action is successful, you will get notified per email.

Please check the source code of the website before and make sure that your phrases are unique. Otherwise your results might be faulty.

5. Set the document root to `/public`


### Cronjob

You may create a cronjob to run the action of your choice automatically. 

Therefore you could either call `your-domain.example/?key=key` or execute the script `/public/index.php` with the argument `key`.

In the above example the `key` is **nike**.


## License

Distributed under the MIT License. See `LICENSE.txt` for more information.

---
> a project by [rt](https://github.com/ramontip)