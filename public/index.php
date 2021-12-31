<?php

/**
 * Require
 */
require_once(realpath(dirname(__FILE__) . '/../action.php'));

?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- No Index -->
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <title>MailSnitch</title>
  </head>
  <body>
    <main class="container-md py-3">
      <div class="p-4 mb-4 bg-light rounded-3 border">
        <a href="/" class="text-decoration-none"><h2>MailSnitch</h2></a>
        <h5 class="mt-4">This is a simple web tool that sends you an email if websites contain or do not contain certain phrases.</h5>

        <?php if (!isset($error)) { ?>
          <ul class="list-group my-4">
            <?php
              foreach ($config['actions'] as $action) {
                echo "<li class='list-group-item p-3'>
                        <div class='d-flex justify-content-between'>
                          <span class='mr-5 text-uppercase'>" . $action['key'] . " Action</span>
                          <a class='fw-bold text-decoration-none' href='?key=" . $action['key'] . "'>" . $action['title'] . "</a>
                        </div>
                      </li>";
              }
            ?>
          </ul>
        <?php } ?>

        <?php
          if (isset($error) && $error != "") {
            echo "<div class='alert alert-danger mt-5 border text-break' role='alert'>$error</div>";
          }
          if (isset($success) && $success != "") {
            echo "<div class='alert alert-success border text-break' role='alert'>$success</div>";
          }
          if (isset($warning) && $warning != "") {
            echo "<div class='alert alert-warning border text-break' role='alert'>$warning</div>";
          }
          if (isset($status) && $status != "") {
            echo "<div class='alert alert-dark border text-break' role='alert'>$status</div>";
          }
          if (isset($details) && $details != "") {
            echo "<div class='accordion mt-4' id='details'>
                    <div class='accordion-item'>
                      <h2 class='accordion-header' id='heading'>
                        <button class='accordion-button bg-white shadow-none' type='button' data-bs-toggle='collapse' data-bs-target='#collapseDetails' aria-expanded='true' aria-controls='collapseDetails'>
                          Show details
                        </button>
                      </h2>
                      <div id='collapseDetails' class='accordion-collapse collapse shadow-none' aria-labelledby='heading' data-bs-parent='#details'>
                        <div class='accordion-body text-break'>
                          $details
                        </div>
                      </div>
                    </div>
                  </div>";
          }
        ?>

        <footer class="pt-3 mt-5 text-muted border-top">
          © <?php echo date('Y'); ?> MailSnitch
        </footer>
      </div>
    </main>
  </body>
</html>