<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
		$user = new User();
		$user->setUsername('Paul');
		$user->setPassword('$2y$13$U82ndlsuRpzGIX4hBdW4FOks95S4W9v8WQW2YF3BBKQSljv1P8I9G');
		$manager->persist($user);

		$admin = new User();
		$admin->setUsername('admin');
		$admin->setPassword('$2y$13$7WB7lSBvHbKWf.C1QBp9y.FCrdu3oWKxVee.7BUg2MHcZYuA33aiy');
		$admin->setRoles(['ROLE_ADMIN']);
		$manager->persist($admin);

        $manager->flush();
    }
}
