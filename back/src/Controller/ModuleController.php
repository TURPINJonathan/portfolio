<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Module;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/module')]
class ModuleController extends AbstractController
{
    #[Route('/create', name: 'app_module_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['icon'])) {
            return new Response('Name and icon are required', Response::HTTP_BAD_REQUEST);
        }

        $module = new Module();
        $module->setName($data['name']);
        $module->setIcon($data['icon']);
        $module->setOptions($data['options'] ?? []);

        $errors = $validator->validate($module);

        if (count($errors) > 0) {
            $errorsString = '';
            foreach ($errors as $error) {
                $errorsString .= $error->getPropertyPath() . ': ' . $error->getMessage() . "\n";
            }

            return new Response($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($module);
        $entityManager->flush();

        return $this->json([
            'message' => 'Module created successfully!',
            'module'  => [
                'id'      => $module->getId(),
                'name'    => $module->getName(),
                'icon'    => $module->getIcon(),
                'options' => $module->getOptions(),
            ],
        ], Response::HTTP_CREATED);
    }

    #[Route('/show/{id}', name: 'app_module_read', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $module = $entityManager->getRepository(Module::class)->find($id);

        if (!$module) {
            return new Response('Module not found', Response::HTTP_NOT_FOUND);
        }

        return $this->json($module);
    }

    #[Route('/update/{id}', name: 'app_module_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $module = $entityManager->getRepository(Module::class)->find($id);

        if (!$module) {
            return new Response('Module not found', Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $module->setName($data['name']);
        }

        if (isset($data['icon'])) {
            $module->setIcon($data['icon']);
        }

        if (isset($data['options'])) {
            $module->setOptions($data['options']);
        }

        $errors = $validator->validate($module);

        if (count($errors) > 0) {
            $errorsString = '';
            foreach ($errors as $error) {
                $errorsString .= $error->getPropertyPath() . ': ' . $error->getMessage() . "\n";
            }

            return new Response($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Module updated successfully!',
            'module'  => [
                'id'      => $module->getId(),
                'name'    => $module->getName(),
                'icon'    => $module->getIcon(),
                'options' => $module->getOptions(),
            ],
        ], Response::HTTP_OK);
    }

    #[Route('/delete/{id}', name: 'app_module_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $module = $entityManager->getRepository(Module::class)->find($id);

        if (!$module) {
            return new Response('Module not found', Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($module);
        $entityManager->flush();

        return $this->json([
            'message' => 'Module deleted successfully!',
        ], Response::HTTP_OK);
    }

    #[Route('/list', name: 'app_module_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $modules = $entityManager->getRepository(Module::class)->findAll();

        return $this->json($modules);
    }
}
