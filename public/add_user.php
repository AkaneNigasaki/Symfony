<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Kernel;
use App\Entity\User;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();

/** @var UserPasswordHasherInterface $passwordHasher */
$passwordHasher = $container->get('parameter_bag')->get('kernel.environment') === 'test'
    ? $container->get('test.service_container')->get(UserPasswordHasherInterface::class)
    : $container->get('security.password_hasher');

// If the above doesn't work in this specific env, we might need a different way to get the hasher
// But let's try the standard way via the kernel first.
// Actually, using the container directly might be tricky in a script.

$entityManager = $container->get('doctrine')->getManager();

try {
    $email = 'test@example.com';
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

    if (!$user) {
        $user = new User();
        $user->setEmail($email);
        $user->setName('Test User');
        // We'll use a simple hash if we can't get the service, but let's try to get it.
        $hasher = $container->get('security.user_password_hasher');
        $user->setPassword($hasher->hashPassword($user, 'password123'));

        $entityManager->persist($user);
        $entityManager->flush();
        echo "User $email created with password 'password123'\n";
    } else {
        echo "User $email already exists\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
