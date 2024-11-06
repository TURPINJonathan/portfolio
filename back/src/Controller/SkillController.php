<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Skill;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/skill')]
class SkillController extends AbstractController
{
    #[Route('/create', name: 'app_skill_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['icon']) || empty($data['color'])) {
            return new Response('Name, icon, and color are required', Response::HTTP_BAD_REQUEST);
        }

        $skill = new Skill();
        $skill->setName($data['name']);
        $skill->setIcon($data['icon']);
        $skill->setColor($data['color']);
        $skill->setIsHardSkill($data['isHardSkill'] ?? null);

        $errors = $validator->validate($skill);

        if (count($errors) > 0) {
            $errorsString = '';
            foreach ($errors as $error) {
                $errorsString .= $error->getPropertyPath() . ': ' . $error->getMessage() . "\n";
            }

            return new Response($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($skill);
        $entityManager->flush();

        return $this->json([
            'message' => 'Skill created successfully!',
            'skill'   => [
                'id'          => $skill->getId(),
                'name'        => $skill->getName(),
                'icon'        => $skill->getIcon(),
                'color'       => $skill->getColor(),
                'isHardSkill' => $skill->isIsHardSkill(),
            ],
        ], Response::HTTP_CREATED);
    }

    #[Route('/show/{id}', name: 'app_skill_read', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $skill = $entityManager->getRepository(Skill::class)->find($id);

        if (!$skill) {
            return new Response('Skill not found', Response::HTTP_NOT_FOUND);
        }

        return $this->json($skill, Response::HTTP_OK, [], ['groups' => 'skill:read']);
    }

    #[Route('/update/{id}', name: 'app_skill_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $skill = $entityManager->getRepository(Skill::class)->find($id);

        if (!$skill) {
            return new Response('Skill not found', Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $skill->setName($data['name']);
        }

        if (isset($data['icon'])) {
            $skill->setIcon($data['icon']);
        }

        if (isset($data['color'])) {
            $skill->setColor($data['color']);
        }

        if (isset($data['isHardSkill'])) {
            $skill->setIsHardSkill($data['isHardSkill']);
        }

        $errors = $validator->validate($skill);

        if (count($errors) > 0) {
            $errorsString = '';
            foreach ($errors as $error) {
                $errorsString .= $error->getPropertyPath() . ': ' . $error->getMessage() . "\n";
            }

            return new Response($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Skill updated successfully!',
            'skill'   => [
                'id'          => $skill->getId(),
                'name'        => $skill->getName(),
                'icon'        => $skill->getIcon(),
                'color'       => $skill->getColor(),
                'isHardSkill' => $skill->isIsHardSkill(),
            ],
        ], Response::HTTP_OK);
    }

    #[Route('/delete/{id}', name: 'app_skill_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $skill = $entityManager->getRepository(Skill::class)->find($id);

        if (!$skill) {
            return new Response('Skill not found', Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($skill);
        $entityManager->flush();

        return $this->json([
            'message' => 'Skill deleted successfully!',
        ], Response::HTTP_OK);
    }

    #[Route('/list', name: 'app_skill_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $skills = $entityManager->getRepository(Skill::class)->findAll();

        return $this->json($skills, Response::HTTP_OK, [], ['groups' => 'skill:read']);
    }
}
