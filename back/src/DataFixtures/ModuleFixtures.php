<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Module;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class ModuleFixtures extends Fixture
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function load(ObjectManager $manager): void
    {
        $this->addModuleIfNotExists($manager, 'contact', 'at');
        $this->addModuleIfNotExists($manager, 'blog', 'pencil');

        $manager->flush();
    }

    private function addModuleIfNotExists(ObjectManager $manager, string $name, string $icon): void
    {
        $moduleRepository = $this->managerRegistry->getRepository(Module::class);
        $existingModule   = $moduleRepository->findOneBy(['name' => $name]);

        if (!$existingModule) {
            $module = new Module();
            $module->setName($name);
            $module->setIcon($icon);

            $manager->persist($module);
        }
    }
}
