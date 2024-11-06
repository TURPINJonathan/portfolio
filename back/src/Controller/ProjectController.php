<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Skill;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/project')]
class ProjectController extends AbstractController
{
    #[Route('/create', name: 'app_project_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['description'])) {
            return new Response('Name and description are required', Response::HTTP_BAD_REQUEST);
        }

        $project = new Project();
        $project->setName($data['name']);
        $project->setDescription($data['description']);
        $project->setColor($data['color'] ?? null);
        $project->setSubtitle($data['subtitle'] ?? null);
        $project->setIsPersonal($data['isPersonal'] ?? false);
        $project->setLink($data['link'] ?? null);

        if (isset($data['skills'])) {
            foreach ($data['skills'] as $skillId) {
                $skill = $entityManager->getRepository(Skill::class)->find($skillId);
                if ($skill) {
                    $project->addSkill($skill);
                }
            }
        }

        $errors = $validator->validate($project);

        if (count($errors) > 0) {
            $errorsString = '';
            foreach ($errors as $error) {
                $errorsString .= $error->getPropertyPath() . ': ' . $error->getMessage() . "\n";
            }

            return new Response($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($project);
        $entityManager->flush();

        return $this->json([
            'message' => 'Project created successfully!',
            'project' => [
                'id'          => $project->getId(),
                'name'        => $project->getName(),
                'description' => $project->getDescription(),
                'color'       => $project->getColor(),
                'subtitle'    => $project->getSubtitle(),
                'isPersonal'  => $project->isIsPersonal(),
                'link'        => $project->getLink(),
            ],
        ], Response::HTTP_CREATED);
    }

    #[Route('/show/{id}', name: 'app_project_read', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            return new Response('Project not found', Response::HTTP_NOT_FOUND);
        }

        return $this->json($project);
    }

    #[Route('/update/{id}', name: 'app_project_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            return new Response('Project not found', Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $fields = [
            'name'        => 'setName',
            'description' => 'setDescription',
            'color'       => 'setColor',
            'subtitle'    => 'setSubtitle',
            'isPersonal'  => 'setIsPersonal',
            'link'        => 'setLink',
        ];

        foreach ($fields as $key => $method) {
            if (isset($data[$key])) {
                $project->$method($data[$key]);
            }
        }

        if (isset($data['skills'])) {
            $project->getSkills()->clear();
            foreach ($data['skills'] as $skillId) {
                $skill = $entityManager->getRepository(Skill::class)->find($skillId);
                if ($skill) {
                    $project->addSkill($skill);
                }
            }
        }

        $errors = $validator->validate($project);

        if (count($errors) > 0) {
            $errorsString = '';
            foreach ($errors as $error) {
                $errorsString .= $error->getPropertyPath() . ': ' . $error->getMessage() . "\n";
            }

            return new Response($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Project updated successfully!',
            'project' => [
                'id'          => $project->getId(),
                'name'        => $project->getName(),
                'description' => $project->getDescription(),
                'color'       => $project->getColor(),
                'subtitle'    => $project->getSubtitle(),
                'isPersonal'  => $project->isIsPersonal(),
                'link'        => $project->getLink(),
                'skills'      => $project->getSkills()->map(fn ($skill) => $skill->getId())->toArray(),
            ],
        ], Response::HTTP_OK);
    }

    #[Route('/delete/{id}', name: 'app_project_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            return new Response('Project not found', Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($project);
        $entityManager->flush();

        return $this->json([
            'message' => 'Project deleted successfully!',
        ], Response::HTTP_OK);
    }

    #[Route('/list', name: 'app_project_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $projects = $entityManager->getRepository(Project::class)->findAll();

        return $this->json($projects, Response::HTTP_OK, [], ['groups' => 'project:read']);
    }
}
