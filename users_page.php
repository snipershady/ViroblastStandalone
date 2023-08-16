<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login Page</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="template/assets/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>


        <?php
        require_once __DIR__ . '/vendor/autoload.php';
        $configurationHandler = new \App\Component\ConfigurationHandler();
        $configurationHandler->setEnviromentDataFromConfig();
        $session = \App\Service\SessionService::getInstance();
        $error = $session->get("error", true);
        if (!empty($error)):
            ?>
            <div class="container mt-5">
                <div class="alert alert-primary" role="alert">
                    <?php echo $error; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php
        $repo = new App\Repository\UserRepositoryPDO();
        $allUser = $repo->findAll();
        ?>

        <section class="vh-100">
            <div class="container-fluid h-custom">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-md-9 col-lg-6 col-xl-5">
                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                             class="img-fluid" alt="Sample image">
                    </div>
                    <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                        <h2>Users</h2>
                            <p>Set user role</p>            
                            <table class="table table-stripe">
                              <thead class="table-primary">
                                <tr>
                                  <th>Username</th>
                                  <th>Email</th>
                                  <th>Role</th>
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <tbody>
                                  <?php foreach($allUser as $user): ?>
                                      <tr>
                                          <td><?php echo $user->getUsername() ?></td>
                                          <td><?php echo $user->getEmail() ?></td>
                                          <td><?php echo $user->getRolesHumanReadable() ?></td>
                                         <td>
                                            <a href="#"><i class="fas fa-eye"></i></a>
                                            <a href="#"><i class="fas fa-pencil-alt"></i></a>
                                         </td>
                                      </tr>
                                  <?php endforeach; ?>         
                              </tbody>
                            </table>
                    </div>
                </div>
            </div>
            <div
                class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
                <!-- Copyright -->
                <div class="text-white mb-3 mb-md-0">
                    Copyright © <?php echo (new DateTime())->format("Y") ?>. All rights reserved.
                </div>
                <!-- Copyright -->

                <!-- Right -->
                <div>
                    <a href="#!" class="text-white me-4">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#!" class="text-white me-4">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#!" class="text-white me-4">
                        <i class="fab fa-google"></i>
                    </a>
                    <a href="#!" class="text-white">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
                <!-- Right -->
            </div>
        </section>
    </body>
</html>

